<?php
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_SamlSpDriver implements Mas_Ah_DriverInterface
{
    public function getDriverKey()
    {
        return 'saml';
    }

    public function getAuthorizationUrl(CMSModule $mod, array $provider, array $context)
    {
        $cfg = $provider['config'] ?? array();
        $idpSso = isset($cfg['idp_sso_url']) ? (string) $cfg['idp_sso_url'] : '';
        if ($idpSso === '') {
            return '';
        }
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        $_SESSION['mas_ah_saml_provider'] = (int) $provider['id'];
        $_SESSION['mas_ah_return_type'] = isset($context['return_type']) ? (string) $context['return_type'] : 'mams';
        $relay = bin2hex(random_bytes(16));
        $_SESSION['mas_ah_saml_relay'] = $relay;
        $acs = Mas_Ah_Config::moduleActionUrl($mod, 'saml_acs', array('provider_id' => (int) $provider['id']));
        $qs = http_build_query(array(
            'SAMLRequest' => '', // SP-initiated via redirect to IdP login URL when using simple redirect mode
            'RelayState' => $relay,
        ));
        return $idpSso . (strpos($idpSso, '?') === false ? '?' : '&') . $qs;
    }

    public function handleCallback(CMSModule $mod, array $provider, array $params, array $context)
    {
        return Mas_Ah_Router::handleSamlAcs($mod, $provider, $params, $context);
    }
}
