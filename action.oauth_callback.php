<?php
if (!function_exists('cmsms')) {
    exit;
}
$providerId = (int) ($params['provider_id'] ?? 0);
$provider = Mas_Ah_DriverRegistry::getProvider($this, $providerId);
if (!$provider) {
    echo '<p>' . cms_htmlentities($this->Lang('error_generic')) . '</p>';
    return;
}
$driver = Mas_Ah_DriverRegistry::get((string) $provider['driver']);
if (!$driver) {
    echo '<p>' . cms_htmlentities($this->Lang('error_generic')) . '</p>';
    return;
}
$oidc = ((string) $provider['driver'] === 'oidc');
$result = Mas_Ah_Router::handleOAuthCallback($this, $provider, $params, array(), $oidc);
if (!empty($result['redirect_admin'])) {
    Mas_Ah_CmsLoginBridge::redirectAfterLogin();
    return;
}
if (!empty($result['success']) && ($result['type'] ?? '') === 'mams') {
    $config = cms_utils::get_config();
    $url = isset($params['redirect']) ? (string) $params['redirect'] : $config['root_url'];
    redirect($url);
    return;
}
echo '<p>' . cms_htmlentities($this->Lang('error_generic')) . '</p>';
