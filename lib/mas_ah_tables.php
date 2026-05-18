<?php
/**
 * Table name helpers for MAS_AuthHub.
 */
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_Tables
{
    public static function prefix()
    {
        return CMS_DB_PREFIX . 'mod_mas_ah_';
    }

    public static function providers()
    {
        return self::prefix() . 'providers';
    }

    public static function userLinks()
    {
        return self::prefix() . 'user_links';
    }

    public static function sessions()
    {
        return self::prefix() . 'sessions';
    }

    public static function tokens()
    {
        return self::prefix() . 'tokens';
    }

    public static function oauthClients()
    {
        return self::prefix() . 'oauth_clients';
    }

    public static function oauthAuthCodes()
    {
        return self::prefix() . 'oauth_auth_codes';
    }

    public static function webauthnCredentials()
    {
        return self::prefix() . 'webauthn_credentials';
    }

    public static function auditLog()
    {
        return self::prefix() . 'audit_log';
    }
}
