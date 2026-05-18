<?php
if (!defined('CMS_VERSION')) {
    exit;
}

interface Mas_Ah_DriverInterface
{
    public function getDriverKey();

    /**
     * @param array<string,mixed> $provider Row from providers table + merged config
     */
    public function getAuthorizationUrl(CMSModule $mod, array $provider, array $context);

    /**
     * @return array<string,mixed>|null
     */
    public function handleCallback(CMSModule $mod, array $provider, array $params, array $context);
}
