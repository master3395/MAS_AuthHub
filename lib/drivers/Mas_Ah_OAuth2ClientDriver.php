<?php
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_OAuth2ClientDriver implements Mas_Ah_DriverInterface
{
    public function getDriverKey()
    {
        return 'oauth2';
    }

    public function getAuthorizationUrl(CMSModule $mod, array $provider, array $context)
    {
        return Mas_Ah_Router::buildOAuthAuthorizeUrl($mod, $provider, $context, false);
    }

    public function handleCallback(CMSModule $mod, array $provider, array $params, array $context)
    {
        return Mas_Ah_Router::handleOAuthCallback($mod, $provider, $params, $context, false);
    }
}
