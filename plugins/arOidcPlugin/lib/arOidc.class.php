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
 *
 * This file is based heavily on the sfCASPlugin developed by D.Jeanmonod and
 * maintained by H.Lepesant, MIT License, https://github.com/jeanmonod/sfCASPlugin.
 */

class arOidc
{
    protected static $oidcIsInitialized = false;

    /**
     * Initialize.
     */
    public static function initializeOidc()
    {
        // Return if already initialized
        if (self::$oidcIsInitialized) {
            return;
        }

        require_once sfConfig::get('sf_root_dir').'/vendor/composer/jumbojett/openid-connect-php/src/OpenIDConnectClient.php';

        $provider_url = sfConfig::get('app_oidc_provider_url', '');
        if (empty($provider_url)) {
            throw new Exception('Invalid OIDC provider URL. Please review the app_oidc_provider_url parameter in plugin app.yml.');
        }
        $client_id = sfConfig::get('app_oidc_client_id', '');
        if (empty($client_id)) {
            throw new Exception('Invalid OIDC client id. Please review the app_oidc_client_id parameter in plugin app.yml.');
        }
        $client_secret = sfConfig::get('app_oidc_client_secret', '');
        if (empty($client_secret)) {
            throw new Exception('Invalid OIDC client secret. Please review the app_oidc_client_secret parameter in plugin app.yml.');
        }

        $oidc = new \Jumbojett\OpenIDConnectClient($provider_url, $client_id, $client_secret);

        $oidc->addScope(['profile', 'email', 'openid', 'groups']);

        $redirect_url = sfConfig::get('app_oidc_redirect_url', '');
        if (empty($redirect_url)) {
            throw new Exception('Invalid OIDC redirect URL. Please review the app_oidc_provider_url parameter in plugin app.yml.');
        }
        $oidc->setRedirectURL($redirect_url);

        // Validate the server SSL certificate according to configuration.
        $certPath = sfConfig::get('app_oidc_server_cert', false);
        if (0 === !strpos($certPath, '/')) {
            $certPath = sfConfig::get('sf_root_dir').DIRECTORY_SEPARATOR.$certPath;
        }
        if (file_exists($certPath)) {
            // setOidcServerCACert(certPath);
        } elseif (false === $certPath) {
            // setNoServerValidation();
        } else {
            throw new Exception('Invalid SSL certificate settings. Please review the app_oidc_server_cert parameter in plugin app.yml.');
        }

        self::$oidcIsInitialized = true;

        return $oidc;
    }
}
