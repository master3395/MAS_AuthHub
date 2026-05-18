<?php
if (!function_exists('cmsms')) {
    exit;
}
header('Content-Type: application/json; charset=utf-8');
if (!Mas_Ah_Policy::rateLimitAllow($this, 'oauth_token_' . cms_utils::get_real_ip())) {
    http_response_code(429);
    echo json_encode(array('error' => 'rate_limited'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}
$input = $_POST;
if (empty($input)) {
    $raw = file_get_contents('php://input');
    parse_str($raw, $input);
}
$grant = isset($input['grant_type']) ? (string) $input['grant_type'] : '';
$clientId = isset($input['client_id']) ? (string) $input['client_id'] : '';
$client = Mas_Ah_OauthServer::getClient($this, $clientId);
if (!$client || !Mas_Ah_OauthServer::verifyClientSecret($client, (string) ($input['client_secret'] ?? ''))) {
    http_response_code(401);
    echo json_encode(array('error' => 'invalid_client'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}
if ($grant === 'authorization_code') {
    $tokens = Mas_Ah_OauthServer::exchangeCode(
        $this,
        (string) ($input['code'] ?? ''),
        $clientId,
        (string) ($input['redirect_uri'] ?? '')
    );
    if (!$tokens) {
        http_response_code(400);
        echo json_encode(array('error' => 'invalid_grant'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
    echo json_encode($tokens, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}
http_response_code(400);
echo json_encode(array('error' => 'unsupported_grant_type'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
