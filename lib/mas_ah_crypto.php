<?php
/**
 * AES-256-GCM encryption for tokens and encrypted preferences.
 */
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_Crypto
{
    public static function encrypt(CMSModule $mod, $plaintext)
    {
        $plaintext = (string) $plaintext;
        if ($plaintext === '') {
            return '';
        }
        $key = self::deriveKey($mod);
        if ($key === '') {
            return '';
        }
        $iv = random_bytes(12);
        $tag = '';
        $cipher = openssl_encrypt($plaintext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($cipher === false) {
            return '';
        }
        return base64_encode($iv . $tag . $cipher);
    }

    public static function decrypt(CMSModule $mod, $payload)
    {
        $payload = (string) $payload;
        if ($payload === '') {
            return '';
        }
        $key = self::deriveKey($mod);
        if ($key === '') {
            return '';
        }
        $raw = base64_decode($payload, true);
        if ($raw === false || strlen($raw) < 28) {
            return '';
        }
        $iv = substr($raw, 0, 12);
        $tag = substr($raw, 12, 16);
        $cipher = substr($raw, 28);
        $plain = openssl_decrypt($cipher, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
        return $plain === false ? '' : $plain;
    }

    public static function encryptPref(CMSModule $mod, $plaintext)
    {
        return self::encrypt($mod, $plaintext);
    }

    public static function decryptPref(CMSModule $mod, $payload)
    {
        return self::decrypt($mod, $payload);
    }

    public static function hashIp($ip)
    {
        return hash('sha256', (string) $ip . '|mas_ah');
    }

    private static function deriveKey(CMSModule $mod)
    {
        $material = Mas_Ah_Config::encryptionKey($mod);
        if ($material === '') {
            $material = (string) cms_siteprefs::get('MAS_AuthHub_fallback_key');
            if ($material === '') {
                $material = bin2hex(random_bytes(16));
                cms_siteprefs::set('MAS_AuthHub_fallback_key', $material);
            }
        }
        return hash('sha256', $material, true);
    }
}
