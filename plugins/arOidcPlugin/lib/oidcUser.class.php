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
    public function initialize(sfEventDispatcher $dispatcher, sfStorage $storage, $options = [])
    {
        // initialize parent
        parent::initialize($dispatcher, $storage, $options);
        $this->logger = sfContext::getInstance()->getLogger();
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
            $oidc = arOidc::initializeOidc();

            if (isset($_REQUEST['code'])) {
                $this->logger->info('OIDC request "code" is set: %1%', ['%1%' => $_REQUEST['code']]);
            }

            if ($oidc->authenticate()) {
                $username = $oidc->requestUserInfo('email');    
                $token = $oidc->getIdToken();

                $this->setAttribute('oidc-token', $token);

                $claims = $oidc->getIdTokenPayload();
                $this->logger->info('OIDC claims: %1%', ['%1%' => json_encode($claims)]);
                
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
        } catch ( Exception $e ) {
            sfContext::getInstance()->getLogger()->err($e->__toString() . PHP_EOL);
        }
    }

    /**
     * Logout from AtoM and the OIDC server.
     */
    public function logout()
    {
        $this->signOut();
        // Dex does not yet implement end_session_endpoint so $oidc->signOut will fail.
        // https://github.com/dexidp/dex/issues/1697
    }

    /**
     * Set group membership based on user group claims returned by OIDC server.
     *
     * @param mixed $user
     * @param mixed $attributes
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
}
