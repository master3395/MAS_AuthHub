<?php
/**
 * Autoload AuthHub library classes (no Composer required for core).
 */
if (!defined('CMS_VERSION')) {
    exit;
}

if (defined('MAS_AH_LOADER_DONE')) {
    return;
}
define('MAS_AH_LOADER_DONE', true);

$base = dirname(__FILE__);
$files = array(
    'mas_ah_tables.php',
    'mas_ah_config.php',
    'mas_ah_crypto.php',
    'mas_ah_audit.php',
    'mas_ah_user_link.php',
    'mas_ah_session.php',
    'mas_ah_token_store.php',
    'mas_ah_policy.php',
    'mas_ah_cms_login.php',
    'drivers/Mas_Ah_DriverInterface.php',
    'drivers/Mas_Ah_DriverRegistry.php',
    'drivers/Mas_Ah_OAuth2ClientDriver.php',
    'drivers/Mas_Ah_OidcDriver.php',
    'drivers/Mas_Ah_SamlSpDriver.php',
    'drivers/Mas_Ah_WebAuthnDriver.php',
    'server/mas_ah_jwt.php',
    'server/mas_ah_oauth_server.php',
    'server/mas_ah_oidc_discovery.php',
    'mas_ah_webauthn.php',
    'mas_ah_router.php',
    'mas_ah_mams_provisioner.php',
    'mas_ah_mams_events.php',
    'mas_ah_mams_registration.php',
);

foreach ($files as $f) {
    $path = $base . DIRECTORY_SEPARATOR . $f;
    if (is_readable($path)) {
        require_once $path;
    }
}

$vendor = dirname($base) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if (is_readable($vendor)) {
    require_once $vendor;
}
