<?php

/*
 * This file is part of the Access to Memory (AtoM) software.
 *
 * Access to Memory (AtoM) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Access to Memory (AtoM) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Access to Memory (AtoM).  If not, see <http://www.gnu.org/licenses/>.
 */

class oidcUser extends myUser implements Zend_Acl_Role_Interface
{
    private $oidcClient;

    public function initialize(sfEventDispatcher $dispatcher, sfStorage $storage, $options = [])
    {
        $this->logger = sfContext::getInstance()->getLogger();
        if (null === $this->oidcClient) {
            $this->oidcClient = arOidc::getOidcInstance();
        }

        parent::initialize($dispatcher, $storage, $options);
    }

    /**
     * Try to login using OIDC.
     *
     * @param null|mixed $username
     * @param null|mixed $password
     */
    public function authenticate($username = null, $password = null)
    {
        $authenticated = false;

        try {
            if (isset($_REQUEST['code'])) {
                $this->logger->info(sprintf('OIDC request "code" is set: %s', $_REQUEST['code']));
            }

            if ($this->oidcClient->authenticate()) {
                $username = $this->oidcClient->requestUserInfo('email');
                $token = $this->oidcClient->getIdToken();
                $refreshToken = $this->oidcClient->getRefreshToken();
                $expiryTime = $this->oidcClient->getVerifiedClaims('exp');

                $this->setAttribute('oidc-token', $token);
                $this->setAttribute('oidc-expiry', $expiryTime);
                $this->setAttribute('oidc-refresh', $refreshToken);
                $claims = $this->oidcClient->getIdTokenPayload();

                // Load user using username or, if one doesn't exist, create it.
                $criteria = new Criteria();
                $criteria->add(QubitUser::USERNAME, $username);
                if (null === $user = QubitUser::getOne($criteria)) {
                    $user = new QubitUser();
                    $user->username = $username;
                    $user->save();
                }
                // Parse OIDC group claims into group memberships. If enabled, we perform this
                // check each time a user authenticates so that changes made on the OIDC
                // server are applied in AtoM on the next login.
                if (true == sfConfig::get('app_oidc_set_groups_from_attributes', false)) {
                    $this->setGroupsFromOIDCGroups($user, $claims->groups);
                }

                $authenticated = true;
                $this->signIn($user);

                return $authenticated;
            }
        } catch (Exception $e) {
            sfContext::getInstance()->getLogger()->err($e->__toString().PHP_EOL);
        }
    }

    public function isAuthenticated()
    {
        $currentTime = time();
        $expiryTime = $this->getAttribute('oidc-expiry', null);
        $refreshToken = $this->getAttribute('oidc-refresh');

        // Check if token has expired.
        if (null !== $expiryTime && $currentTime >= $expiryTime) {
            if ($refreshToken) {
                try {
                    $this->logger->info('ID token expired - using refresh token to extend session.');
                    $refreshResult = $this->oidcClient->refreshToken($refreshToken);

                    $newToken = $this->oidcClient->getIdToken();
                    $newRefreshToken = $this->oidcClient->getRefreshToken();
                    $newExpiryTime = $this->oidcClient->getVerifiedClaims('exp');

                    // Use the new access tokens going forward.
                    $this->setAttribute('oidc-token', $newToken);
                    $this->setAttribute('oidc-expiry', $newExpiryTime);
                    $this->setAttribute('oidc-refresh', $newRefreshToken);
                } catch (Exception $e) {
                    sfContext::getInstance()->getLogger()->err($e->__toString().PHP_EOL);
                }
            } else {
                $this->logger->info('Refresh token unavailable - authenticating user');
                $this->unsetAttributes();

                return false;
            }
        }

        return parent::isAuthenticated();
    }

    /**
     * Logout from AtoM and the OIDC server.
     */
    public function logout()
    {
        $this->unsetAttributes();

        $this->signOut();
        // Dex does not yet implement end_session_endpoint so $this->oidcClient->signOut will fail.
        // https://github.com/dexidp/dex/issues/1697
    }

    /**
     * Set group membership based on user group claims returned by OIDC server.
     *
     * @param mixed $user
     */
    protected function setGroupsFromOIDCGroups($user, array $groups)
    {
        if (null === $groups) {
            sfContext::getInstance()->getLogger()->err('OIDC group list used for setting AtoM group membership is null');

            return;
        }

        // If groups param is a string convert into an array to simplify validation.
        if (!is_array($groups)) {
            $groups = [$groups];
        }

        // Delete existing AclUserGroups for this user. This allows us to reset
        // group membership on each login so that users will only belong to groups
        // that are appropriately configured in app_oidc_user_groups.
        $criteria = new Criteria();
        $criteria->add(QubitAclUserGroup::USER_ID, $user->id);
        foreach (QubitAclUserGroup::get($criteria) as $item) {
            $item->delete();
        }

        // Add the user to AclUserGroups based on the presence of expected OIDC
        // group values as set in app_oidc_user_groups.
        $userGroups = sfConfig::get('app_oidc_user_groups');
        foreach ($userGroups as $item) {
            if (null !== $group = QubitAclGroup::getById($item['group_id'])) {
                $expectedValue = $item['attribute_value'];
                if (in_array($expectedValue, $groups)) {
                    $userGroup = new QubitAclUserGroup();
                    $userGroup->userId = $user->id;
                    $userGroup->groupId = $group->id;
                    $userGroup->save();
                }
            }
        }
    }

    private function unsetAttributes()
    {
        $this->setAttribute('oidc-token', '');
        $this->setAttribute('oidc-expiry', '');
        $this->setAttribute('oidc-refresh', '');
    }
}
