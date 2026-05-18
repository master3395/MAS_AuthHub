<?php
if (!function_exists('cmsms')) {
    exit;
}
$providerId = (int) ($params['provider_id'] ?? 0);
if ($providerId < 1 && session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['mas_ah_saml_provider'])) {
    $providerId = (int) $_SESSION['mas_ah_saml_provider'];
}
$provider = Mas_Ah_DriverRegistry::getProvider($this, $providerId);
if (!$provider) {
    echo '<p>' . cms_htmlentities($this->Lang('error_generic')) . '</p>';
    return;
}
$result = Mas_Ah_Router::handleSamlAcs($this, $provider, $_POST, array());
if (!empty($result['redirect_admin'])) {
    Mas_Ah_CmsLoginBridge::redirectAfterLogin();
    return;
}
if (!empty($result['success'])) {
    $config = cms_utils::get_config();
    redirect($config['root_url']);
    return;
}
echo '<p>' . cms_htmlentities($this->Lang('error_generic')) . '</p>';
