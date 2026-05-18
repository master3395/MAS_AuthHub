<?php
if (!function_exists('cmsms')) {
    exit;
}
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    header('Content-Type: application/json; charset=utf-8');
    $result = Mas_Ah_Webauthn::handleRequest($this, $params);
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    return;
}
$returnType = isset($params['return_type']) ? (string) $params['return_type'] : 'mams';
$smarty = cms_utils::get_smarty();
$smarty->assign('mod', $this);
$smarty->assign('mas_ah_return_type', $returnType);
$smarty->assign('mas_ah_webauthn_post', Mas_Ah_Config::moduleActionUrl($this, 'webauthn', array('mode' => 'login', 'return_type' => $returnType)));
echo $this->ProcessTemplate('webauthn_login.tpl');
