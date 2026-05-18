<?php
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_OidcDiscovery
{
    public static function document(CMSModule $mod)
    {
        $base = Mas_Ah_Config::callbackBaseUrl($mod);
        $returnid = (int) $mod->GetPreference('mas_ah_frontend_returnid', '0');
        if ($returnid < 1) {
            $returnid = (int) cmsms()->GetContentOperations()->GetDefaultContent();
        }
        $linkId = 'cntnt01';
        $authorize = $mod->CreateLink($linkId, 'oauth_authorize', $returnid, '', array(), '', true);
        $token = $mod->CreateLink($linkId, 'oauth_token', $returnid, '', array(), '', true);
        $jwks = $base . '/modules/MAS_AuthHub/oidc/jwks.json';
        return array(
            'issuer' => $base,
            'authorization_endpoint' => $authorize,
            'token_endpoint' => $token,
            'jwks_uri' => $jwks,
            'response_types_supported' => array('code'),
            'subject_types_supported' => array('public'),
            'id_token_signing_alg_values_supported' => array('HS256'),
            'scopes_supported' => array('openid', 'profile', 'email', 'cmsms.admin'),
            'token_endpoint_auth_methods_supported' => array('client_secret_post'),
        );
    }
}
