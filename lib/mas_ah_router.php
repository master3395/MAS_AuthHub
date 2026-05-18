<?php
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_Router
{
    public static function startOAuth(CMSModule $mod, $providerId, $returnType = 'mams')
    {
        if (!Mas_Ah_Policy::rateLimitAllow($mod, 'oauth_start_' . cms_utils::get_real_ip())) {
            return array('error' => 'rate_limited');
        }
        $provider = Mas_Ah_DriverRegistry::getProvider($mod, $providerId);
        if (!$provider || !(int) $provider['enabled']) {
            return array('error' => 'invalid_provider');
        }
        $driver = Mas_Ah_DriverRegistry::get((string) $provider['driver']);
        if (!$driver) {
            return array('error' => 'invalid_driver');
        }
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        $returnType = in_array($returnType, array('admin', 'mams'), true) ? $returnType : 'mams';
        if ($returnType === 'admin' && !Mas_Ah_Policy::allowAdminSso($mod)) {
            return array('error' => 'admin_sso_disabled');
        }
        if ($returnType === 'mams' && !Mas_Ah_Policy::allowMamsSso($mod)) {
            return array('error' => 'mams_sso_disabled');
        }
        $_SESSION['mas_ah_return_type'] = $returnType;
        $url = $driver->getAuthorizationUrl($mod, $provider, array('return_type' => $returnType));
        if ($url === '') {
            return array('error' => 'authorize_url_failed');
        }
        return array('redirect' => $url);
    }

    public static function buildOAuthAuthorizeUrl(CMSModule $mod, array $provider, array $context, $oidc)
    {
        $cfg = $provider['config'] ?? array();
        $authUrl = isset($cfg['authorize_url']) ? (string) $cfg['authorize_url'] : '';
        $clientId = isset($cfg['client_id']) ? (string) $cfg['client_id'] : '';
        if ($authUrl === '' || $clientId === '') {
            return '';
        }
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        $state = bin2hex(random_bytes(16));
        $verifier = bin2hex(random_bytes(32));
        $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
        $_SESSION['mas_ah_oauth_state'] = $state;
        $_SESSION['mas_ah_oauth_pkce'] = $verifier;
        $_SESSION['mas_ah_oauth_provider'] = (int) $provider['id'];
        $_SESSION['mas_ah_return_type'] = isset($context['return_type']) ? (string) $context['return_type'] : 'mams';
        $redirectUri = Mas_Ah_Config::moduleActionUrl($mod, 'oauth_callback', array(
            'provider_id' => (int) $provider['id'],
        ));
        $scope = isset($cfg['scope']) ? (string) $cfg['scope'] : ($oidc ? 'openid profile email' : 'profile email');
        $params = array(
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'scope' => $scope,
            'state' => $state,
            'code_challenge' => $challenge,
            'code_challenge_method' => 'S256',
        );
        if ($oidc && !empty($cfg['nonce'])) {
            $params['nonce'] = bin2hex(random_bytes(8));
            $_SESSION['mas_ah_oauth_nonce'] = $params['nonce'];
        }
        return $authUrl . (strpos($authUrl, '?') === false ? '?' : '&') . http_build_query($params);
    }

    public static function handleOAuthCallback(CMSModule $mod, array $provider, array $params, array $context, $oidc)
    {
        if (!Mas_Ah_Policy::rateLimitAllow($mod, 'oauth_cb_' . cms_utils::get_real_ip())) {
            return array('error' => 'rate_limited');
        }
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        $state = isset($params['state']) ? (string) $params['state'] : '';
        if ($state === '' || !isset($_SESSION['mas_ah_oauth_state']) || !hash_equals($_SESSION['mas_ah_oauth_state'], $state)) {
            Mas_Ah_Audit::log($mod, 'oauth.state_mismatch', 'error', array(), 0, (int) $provider['id']);
            return array('error' => 'invalid_state');
        }
        $code = isset($params['code']) ? (string) $params['code'] : '';
        if ($code === '') {
            return array('error' => 'missing_code');
        }
        $cfg = $provider['config'] ?? array();
        $tokenUrl = isset($cfg['token_url']) ? (string) $cfg['token_url'] : '';
        $clientId = isset($cfg['client_id']) ? (string) $cfg['client_id'] : '';
        $pkey = isset($provider['provider_key']) ? $provider['provider_key'] : ('provider_' . $provider['id']);
        $clientSecret = (string) Mas_Ah_Config::getProviderSecret($mod, $pkey, 'client_secret');
        $verifier = isset($_SESSION['mas_ah_oauth_pkce']) ? (string) $_SESSION['mas_ah_oauth_pkce'] : '';
        $redirectUri = Mas_Ah_Config::moduleActionUrl($mod, 'oauth_callback', array('provider_id' => (int) $provider['id']));
        $tokenBody = self::httpPostForm($tokenUrl, array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirectUri,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code_verifier' => $verifier,
        ));
        if (!is_array($tokenBody) || empty($tokenBody['access_token'])) {
            Mas_Ah_Audit::log($mod, 'oauth.token_failed', 'error', array('http' => is_array($tokenBody) ? 'empty' : 'fail'), 0, (int) $provider['id']);
            Mas_Ah_Session::clearOAuthState();
            return array('error' => 'token_exchange_failed');
        }
        $profile = self::fetchUserProfile($cfg, $tokenBody, $oidc);
        Mas_Ah_Session::clearOAuthState();
        return self::finalizeFederatedLogin($mod, $provider, $profile, $_SESSION['mas_ah_return_type'] ?? 'mams');
    }

    /**
     * @return array<string,mixed>
     */
    private static function fetchUserProfile(array $cfg, array $tokenBody, $oidc)
    {
        $sub = '';
        $email = '';
        $name = '';
        if ($oidc && !empty($tokenBody['id_token'])) {
            $parts = explode('.', (string) $tokenBody['id_token']);
            if (count($parts) >= 2) {
                $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
                if (is_array($payload)) {
                    $sub = isset($payload['sub']) ? (string) $payload['sub'] : '';
                    $email = isset($payload['email']) ? (string) $payload['email'] : '';
                    $name = isset($payload['name']) ? (string) $payload['name'] : '';
                }
            }
        }
        if ($sub === '' && !empty($cfg['userinfo_url'])) {
            $info = self::httpGetJson((string) $cfg['userinfo_url'], (string) $tokenBody['access_token']);
            if (is_array($info)) {
                $sub = isset($info['sub']) ? (string) $info['sub'] : (isset($info['id']) ? (string) $info['id'] : '');
                if ($email === '' && isset($info['email'])) {
                    $email = (string) $info['email'];
                }
                if ($name === '' && isset($info['name'])) {
                    $name = (string) $info['name'];
                }
            }
        }
        if ($sub === '') {
            $sub = hash('sha256', (string) $tokenBody['access_token']);
        }
        return array('sub' => $sub, 'email' => $email, 'name' => $name, 'token' => $tokenBody);
    }

    public static function finalizeFederatedLogin(CMSModule $mod, array $provider, array $profile, $returnType)
    {
        $returnType = in_array($returnType, array('admin', 'mams'), true) ? $returnType : 'mams';
        $sub = (string) ($profile['sub'] ?? '');
        $email = strtolower(trim((string) ($profile['email'] ?? '')));
        $link = Mas_Ah_UserLink::findByExternal($mod, (int) $provider['id'], $sub);
        if (!$link && $email !== '' && Mas_Ah_Policy::autoLinkByEmail($mod)) {
            $link = Mas_Ah_UserLink::findByEmail($mod, $email, (int) $provider['id']);
        }
        if ($returnType === 'admin') {
            return self::finalizeAdmin($mod, $provider, $profile, $link, $sub, $email);
        }
        return self::finalizeMams($mod, $provider, $profile, $link, $sub, $email);
    }

    private static function finalizeAdmin(CMSModule $mod, array $provider, array $profile, $link, $sub, $email)
    {
        $cmsUserId = $link ? (int) ($link['cms_user_id'] ?? 0) : 0;
        if ($cmsUserId < 1 && $email !== '') {
            $userops = UserOperations::get_instance();
            $u = $userops->LoadUserByUsername($email);
            if ($u && $u->id) {
                $cmsUserId = (int) $u->id;
            }
        }
        if ($cmsUserId < 1) {
            Mas_Ah_Audit::log($mod, 'admin.login_no_link', 'warning', array('sub' => $sub), 0, (int) $provider['id']);
            return array('error' => 'no_admin_account');
        }
        $userops = UserOperations::get_instance();
        $user = $userops->LoadUserByID($cmsUserId);
        if (!$user || !Mas_Ah_Policy::cmsUserAllowed($mod, $user)) {
            return array('error' => 'access_denied');
        }
        Mas_Ah_UserLink::upsert($mod, array(
            'provider_id' => (int) $provider['id'],
            'external_sub' => $sub,
            'cms_user_id' => $cmsUserId,
            'email' => $email,
            'attrs_json' => $profile,
        ));
        Mas_Ah_CmsLoginBridge::loginCmsUser($user);
        Mas_Ah_Session::startHubSession($mod, array('cms_user_id' => $cmsUserId, 'provider_id' => (int) $provider['id']));
        Mas_Ah_Audit::log($mod, 'admin.sso_success', 'info', array(), $cmsUserId, (int) $provider['id']);
        return array('success' => true, 'type' => 'admin', 'redirect_admin' => true);
    }

    private static function finalizeMams(CMSModule $mod, array $provider, array $profile, $link, $sub, $email)
    {
        $mamsUid = $link ? (int) ($link['mams_user_id'] ?? 0) : 0;
        if ($mamsUid < 1) {
            $mamsUid = Mas_Ah_MamsProvisioner::provisionOrLink($mod, $provider, $profile, $sub, $email);
        }
        if ($mamsUid < 1) {
            Mas_Ah_Audit::log($mod, 'mams.login_no_user', 'warning', array('sub' => $sub), 0, (int) $provider['id']);
            return array('error' => 'no_mams_account');
        }
        Mas_Ah_UserLink::upsert($mod, array(
            'provider_id' => (int) $provider['id'],
            'external_sub' => $sub,
            'mams_user_id' => $mamsUid,
            'email' => $email,
            'attrs_json' => $profile,
        ));
        Mas_Ah_MamsProvisioner::finalizeMamsLogin($mod, $mamsUid, $provider, $sub);
        Mas_Ah_Session::startHubSession($mod, array('mams_user_id' => $mamsUid, 'provider_id' => (int) $provider['id']));
        \CMSMS\HookManager::do_hook('MAS_AuthHub::OnMamsLogin', array(
            'mams_uid' => $mamsUid,
            'provider_id' => (int) $provider['id'],
            'sub' => $sub,
        ));
        Mas_Ah_Audit::log($mod, 'mams.sso_success', 'info', array(), $mamsUid, (int) $provider['id']);
        return array('success' => true, 'type' => 'mams');
    }

    public static function handleSamlAcs(CMSModule $mod, array $provider, array $params, array $context)
    {
        $cfg = $provider['config'] ?? array();
        $pkey = isset($provider['provider_key']) ? $provider['provider_key'] : ('provider_' . $provider['id']);
        $autoload = dirname(__DIR__) . '/vendor/autoload.php';
        if (!is_readable($autoload)) {
            Mas_Ah_Audit::log($mod, 'saml.vendor_missing', 'error', array(), 0, (int) $provider['id']);
            return array('error' => 'saml_not_configured');
        }
        require_once $autoload;
        if (!class_exists('OneLogin\\Saml2\\Auth')) {
            return array('error' => 'saml_not_configured');
        }
        try {
            $settings = self::buildSamlSettings($mod, $provider, $cfg, $pkey);
            $auth = new OneLogin\Saml2\Auth($settings);
            $auth->processResponse();
            if (!$auth->isAuthenticated()) {
                return array('error' => 'saml_auth_failed');
            }
            $attrs = $auth->getAttributes();
            $email = '';
            if (isset($attrs['email'][0])) {
                $email = (string) $attrs['email'][0];
            } elseif (isset($attrs['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress'][0])) {
                $email = (string) $attrs['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress'][0];
            }
            $sub = (string) $auth->getNameId();
            $profile = array('sub' => $sub, 'email' => $email, 'name' => $sub, 'saml' => $attrs);
            $returnType = isset($_SESSION['mas_ah_return_type']) ? (string) $_SESSION['mas_ah_return_type'] : 'mams';
            return self::finalizeFederatedLogin($mod, $provider, $profile, $returnType);
        } catch (Throwable $e) {
            Mas_Ah_Audit::log($mod, 'saml.exception', 'error', array('msg' => $e->getMessage()), 0, (int) $provider['id']);
            return array('error' => 'saml_error');
        }
    }

    public static function buildSamlSettings(CMSModule $mod, array $provider, array $cfg, $pkey)
    {
        $base = Mas_Ah_Config::callbackBaseUrl($mod);
        $spEntityId = isset($cfg['sp_entity_id']) ? (string) $cfg['sp_entity_id'] : $base . '/mas-authhub-sp';
        $acs = Mas_Ah_Config::moduleActionUrl($mod, 'saml_acs', array('provider_id' => (int) $provider['id']));
        return array(
            'strict' => true,
            'debug' => false,
            'sp' => array(
                'entityId' => $spEntityId,
                'assertionConsumerService' => array(
                    'url' => $acs,
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
                ),
            ),
            'idp' => array(
                'entityId' => isset($cfg['idp_entity_id']) ? (string) $cfg['idp_entity_id'] : '',
                'singleSignOnService' => array(
                    'url' => isset($cfg['idp_sso_url']) ? (string) $cfg['idp_sso_url'] : '',
                ),
                'x509cert' => isset($cfg['idp_cert']) ? (string) $cfg['idp_cert'] : '',
            ),
            'security' => array(
                'wantAssertionsSigned' => true,
            ),
        );
    }

    private static function httpPostForm($url, array $fields)
    {
        if ($url === '') {
            return null;
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($fields),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => array('Accept: application/json'),
        ));
        $body = curl_exec($ch);
        curl_close($ch);
        if ($body === false) {
            return null;
        }
        $decoded = json_decode($body, true);
        return is_array($decoded) ? $decoded : null;
    }

    private static function httpGetJson($url, $accessToken)
    {
        if ($url === '') {
            return null;
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $accessToken,
                'Accept: application/json',
            ),
        ));
        $body = curl_exec($ch);
        curl_close($ch);
        if ($body === false) {
            return null;
        }
        $decoded = json_decode($body, true);
        return is_array($decoded) ? $decoded : null;
    }
}
