<?php
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_DriverRegistry
{
    /** @var array<string,Mas_Ah_DriverInterface> */
    private static $drivers = array();

    public static function register(Mas_Ah_DriverInterface $driver)
    {
        self::$drivers[$driver->getDriverKey()] = $driver;
    }

    public static function boot()
    {
        if (count(self::$drivers) > 0) {
            return;
        }
        self::register(new Mas_Ah_OAuth2ClientDriver());
        self::register(new Mas_Ah_OidcDriver());
        self::register(new Mas_Ah_SamlSpDriver());
        self::register(new Mas_Ah_WebAuthnDriver());
    }

    /**
     * @return Mas_Ah_DriverInterface|null
     */
    public static function get($key)
    {
        self::boot();
        $key = strtolower((string) $key);
        return isset(self::$drivers[$key]) ? self::$drivers[$key] : null;
    }

    public static function listKeys()
    {
        self::boot();
        return array_keys(self::$drivers);
    }

    public static function getProvider(CMSModule $mod, $providerId)
    {
        $db = $mod->GetDb();
        $row = $db->GetRow(
            'SELECT * FROM ' . Mas_Ah_Tables::providers() . ' WHERE id = ? LIMIT 1',
            array((int) $providerId)
        );
        if (!is_array($row)) {
            return null;
        }
        $cfg = json_decode((string) ($row['config_json'] ?? '{}'), true);
        if (!is_array($cfg)) {
            $cfg = array();
        }
        $row['config'] = $cfg;
        $row['provider_key'] = isset($cfg['provider_key']) ? (string) $cfg['provider_key'] : 'provider_' . $row['id'];
        return $row;
    }

    public static function listProviders(CMSModule $mod, $enabledOnly = false)
    {
        $sql = 'SELECT * FROM ' . Mas_Ah_Tables::providers() . ' ORDER BY sort_order ASC, name ASC';
        if ($enabledOnly) {
            $sql = 'SELECT * FROM ' . Mas_Ah_Tables::providers() . ' WHERE enabled = 1 ORDER BY sort_order ASC, name ASC';
        }
        $rows = $mod->GetDb()->GetArray($sql);
        return is_array($rows) ? $rows : array();
    }
}
