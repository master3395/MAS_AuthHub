<?php
if (!function_exists('cmsms')) {
    exit;
}
$providerId = (int) ($params['provider_id'] ?? 0);
$provider = Mas_Ah_DriverRegistry::getProvider($this, $providerId);
if (!$provider) {
    header('HTTP/1.1 404 Not Found');
    exit;
}
$autoload = dirname(__FILE__) . '/vendor/autoload.php';
if (!is_readable($autoload)) {
    header('HTTP/1.1 503 Service Unavailable');
    echo 'SAML library not installed. Run composer install in modules/MAS_AuthHub.';
    exit;
}
require_once $autoload;
$cfg = $provider['config'] ?? array();
$pkey = isset($provider['provider_key']) ? $provider['provider_key'] : ('provider_' . $provider['id']);
$settings = Mas_Ah_Router::buildSamlSettings($this, $provider, $cfg, $pkey);
try {
    $meta = new OneLogin\Saml2\Settings($settings, true);
    header('Content-Type: text/xml; charset=utf-8');
    echo $meta->getSPMetadata();
} catch (Throwable $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo 'Metadata error.';
}
