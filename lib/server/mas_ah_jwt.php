<?php
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_Jwt
{
    public static function encode(CMSModule $mod, array $payload, $ttl = 3600)
    {
        $autoload = dirname(__DIR__, 2) . '/vendor/autoload.php';
        if (!is_readable($autoload)) {
            return '';
        }
        require_once $autoload;
        if (!class_exists('Firebase\\JWT\\JWT')) {
            return '';
        }
        $key = Mas_Ah_Config::signingKey($mod);
        if ($key === '') {
            $key = Mas_Ah_Config::encryptionKey($mod);
        }
        if ($key === '') {
            return '';
        }
        $payload['iat'] = time();
        $payload['exp'] = time() + (int) $ttl;
        $payload['iss'] = Mas_Ah_Config::callbackBaseUrl($mod);
        return Firebase\JWT\JWT::encode($payload, $key, 'HS256');
    }

    public static function decode(CMSModule $mod, $jwt)
    {
        $autoload = dirname(__DIR__, 2) . '/vendor/autoload.php';
        if (!is_readable($autoload)) {
            return null;
        }
        require_once $autoload;
        if (!class_exists('Firebase\\JWT\\JWT')) {
            return null;
        }
        $key = Mas_Ah_Config::signingKey($mod);
        if ($key === '') {
            $key = Mas_Ah_Config::encryptionKey($mod);
        }
        try {
            if (class_exists('Firebase\\JWT\\Key')) {
                $decoded = Firebase\JWT\JWT::decode($jwt, new Firebase\JWT\Key($key, 'HS256'));
            } else {
                $decoded = Firebase\JWT\JWT::decode($jwt, $key, array('HS256'));
            }
            return json_decode(json_encode($decoded), true);
        } catch (Throwable $e) {
            return null;
        }
    }
}
