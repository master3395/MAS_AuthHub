<?php
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_WebAuthnDriver implements Mas_Ah_DriverInterface
{
    public function getDriverKey()
    {
        return 'webauthn';
    }

    public function getAuthorizationUrl(CMSModule $mod, array $provider, array $context)
    {
        return Mas_Ah_Config::moduleActionUrl($mod, 'webauthn', array(
            'mode' => 'login',
            'return_type' => isset($context['return_type']) ? $context['return_type'] : 'mams',
        ));
    }

    public function handleCallback(CMSModule $mod, array $provider, array $params, array $context)
    {
        return null;
    }
}
