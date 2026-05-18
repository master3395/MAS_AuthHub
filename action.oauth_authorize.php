<?php
if (!function_exists('cmsms')) {
    exit;
}
$clientId = isset($params['client_id']) ? (string) $params['client_id'] : (isset($_GET['client_id']) ? (string) $_GET['client_id'] : '');
$redirectUri = isset($params['redirect_uri']) ? (string) $params['redirect_uri'] : (isset($_GET['redirect_uri']) ? (string) $_GET['redirect_uri'] : '');
$scope = isset($params['scope']) ? (string) $params['scope'] : (isset($_GET['scope']) ? (string) $_GET['scope'] : 'openid');
$state = isset($params['state']) ? (string) $params['state'] : (isset($_GET['state']) ? (string) $_GET['state'] : '');
$client = Mas_Ah_OauthServer::getClient($this, $clientId);
if (!$client) {
    header('HTTP/1.1 400 Bad Request');
    echo 'invalid_client';
    exit;
}
$uid = isset($_SESSION['cms_userid']) ? (int) $_SESSION['cms_userid'] : 0;
if ($uid < 1) {
    $config = cms_utils::get_config();
    redirect($config['admin_url'] . '/login.php');
}
$code = Mas_Ah_OauthServer::createAuthCode($this, array(
    'client_id' => $clientId,
    'user_id' => $uid,
    'scopes' => $scope,
    'redirect_uri' => $redirectUri,
));
$sep = strpos($redirectUri, '?') === false ? '?' : '&';
redirect($redirectUri . $sep . http_build_query(array('code' => $code, 'state' => $state)));
