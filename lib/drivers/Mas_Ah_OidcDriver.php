<?php
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_OidcDriver implements Mas_Ah_DriverInterface
{
    public function getDriverKey()
    {
        return 'oidc';
    }

    public function getAuthorizationUrl(CMSModule $mod, array $provider, array $context)
    {
        return Mas_Ah_Router::buildOAuthAuthorizeUrl($mod, $provider, $context, true);
    }

    public function handleCallback(CMSModule $mod, array $provider, array $params, array $context)
    {
        return Mas_Ah_Router::handleOAuthCallback($mod, $provider, $params, $context, true);
    }
}
