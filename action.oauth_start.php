<?php
if (!function_exists('cmsms')) {
    exit;
}
$providerId = (int) ($params['provider_id'] ?? 0);
$returnType = isset($params['return_type']) ? (string) $params['return_type'] : 'mams';
$result = Mas_Ah_Router::startOAuth($this, $providerId, $returnType);
if (!empty($result['redirect'])) {
    redirect($result['redirect'], true);
}
echo '<p>' . cms_htmlentities($this->Lang('error_generic')) . '</p>';
