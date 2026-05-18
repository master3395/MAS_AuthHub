<?php
/**
 * Hybrid config: site config.php mas_authhub block + module preferences.
 */
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_Config
{
    /**
     * @return array<string,mixed>
     */
    public static function siteBlock()
    {
        static $block = null;
        if ($block !== null) {
            return $block;
        }
        $block = array();
        $config = cms_utils::get_config();
        if (isset($config['mas_authhub']) && is_array($config['mas_authhub'])) {
            $block = $config['mas_authhub'];
        }
        return $block;
    }

    public static function encryptionKey(CMSModule $mod)
    {
        $block = self::siteBlock();
        if (!empty($block['encryption_key']) && is_string($block['encryption_key'])) {
            return (string) $block['encryption_key'];
        }
        $pref = (string) $mod->GetPreference('mas_ah_encryption_key_hint', '');
        if ($pref !== '') {
            return $pref;
        }
        return '';
    }

    public static function signingKey(CMSModule $mod)
    {
        $block = self::siteBlock();
        if (!empty($block['signing_key'])) {
            return is_string($block['signing_key']) ? $block['signing_key'] : '';
        }
        return (string) $mod->GetPreference('mas_ah_signing_key_path', '');
    }

    /**
     * @return mixed
     */
    public static function getProviderSecret(CMSModule $mod, $providerKey, $secretName)
    {
        $providerKey = preg_replace('/[^a-z0-9_\-]/i', '', (string) $providerKey);
        $secretName = preg_replace('/[^a-z0-9_\-]/i', '', (string) $secretName);
        $block = self::siteBlock();
        if (isset($block['providers'][$providerKey][$secretName])) {
            return $block['providers'][$providerKey][$secretName];
        }
        $prefKey = 'mas_ah_secret_' . $providerKey . '_' . $secretName;
        $enc = (string) $mod->GetPreference($prefKey, '');
        if ($enc === '') {
            return '';
        }
        return Mas_Ah_Crypto::decryptPref($mod, $enc);
    }

    public static function callbackBaseUrl(CMSModule $mod)
    {
        $custom = trim((string) $mod->GetPreference('mas_ah_callback_base', ''));
        if ($custom !== '') {
            return rtrim($custom, '/');
        }
        $config = cms_utils::get_config();
        return rtrim((string) $config['root_url'], '/');
    }

    public static function moduleActionUrl(CMSModule $mod, $action, array $extra = array())
    {
        $returnid = (int) $mod->GetPreference('mas_ah_frontend_returnid', '0');
        if ($returnid < 1) {
            $returnid = (int) cmsms()->GetContentOperations()->GetDefaultContent();
        }
        $linkId = 'cntnt01';
        if ($returnid > 0) {
            return $mod->CreateLink($linkId, $action, $returnid, '', $extra, '', true);
        }
        return $mod->CreateLink($linkId, $action, '', '', $extra, '', true);
    }
}
