<?php
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_MamsProvisioner
{
    public static function mamsModule()
    {
        $m = cms_utils::get_module('MAMS');
        return $m ? $m : null;
    }

    public static function provisionOrLink(CMSModule $mod, array $provider, array $profile, $sub, $email)
    {
        $mams = self::mamsModule();
        if (!$mams) {
            return 0;
        }
        $propName = self::connectingProperty($provider);
        $existing = self::findMamsUserByProperty($mams, $propName, $sub);
        if ($existing > 0) {
            return $existing;
        }
        if ($email !== '') {
            $byEmail = self::findMamsUserByEmail($mams, $email);
            if ($byEmail > 0) {
                self::setConnectingProperty($mams, $byEmail, $propName, $sub);
                return $byEmail;
            }
        }
        $jit = (int) $mod->GetPreference('mas_ah_mams_jit_create', '1');
        if ($jit !== 1) {
            return 0;
        }
        return self::jitCreate($mod, $mams, $provider, $profile, $sub, $email, $propName);
    }

    public static function finalizeMamsLogin(CMSModule $mod, $mamsUid, array $provider, $sub)
    {
        $mams = self::mamsModule();
        if (!$mams) {
            return false;
        }
        $propName = self::connectingProperty($provider);
        self::setConnectingProperty($mams, $mamsUid, $propName, $sub);
        if (method_exists($mams, 'SetSessionUser')) {
            $mams->SetSessionUser($mamsUid);
        }
        return true;
    }

    private static function connectingProperty(array $provider)
    {
        $cfg = $provider['config'] ?? array();
        if (!empty($cfg['mams_connecting_property'])) {
            return (string) $cfg['mams_connecting_property'];
        }
        return 'authhub_' . preg_replace('/[^a-z0-9_]/i', '_', (string) ($provider['name'] ?? 'provider')) . '_sub';
    }

    private static function findMamsUserByProperty($mams, $propName, $sub)
    {
        if (!method_exists($mams, 'GetUserIDFromProperty')) {
            return 0;
        }
        $uid = $mams->GetUserIDFromProperty($propName, $sub);
        return $uid ? (int) $uid : 0;
    }

    private static function findMamsUserByEmail($mams, $email)
    {
        if (!method_exists($mams, 'GetUserIDFromProperty')) {
            return 0;
        }
        $uid = $mams->GetUserIDFromProperty('email', $email);
        if ($uid) {
            return (int) $uid;
        }
        return 0;
    }

    private static function setConnectingProperty($mams, $uid, $propName, $sub)
    {
        if (method_exists($mams, 'SetUserPropertyFull')) {
            $mams->SetUserPropertyFull($uid, $propName, $sub);
        }
        if (method_exists($mams, 'SetUserProperty')) {
            $mams->SetUserProperty($uid, $propName, $sub);
        }
    }

    private static function jitCreate(CMSModule $mod, $mams, array $provider, array $profile, $sub, $email, $propName)
    {
        $username = self::uniqueUsername($mams, $email, $sub);
        $password = bin2hex(random_bytes(16));
        $uid = 0;
        if (method_exists($mams, 'AddUser')) {
            $uid = (int) $mams->AddUser($username, $password, $email);
        }
        if ($uid < 1) {
            return 0;
        }
        $groups = self::defaultGroups($mod, $provider);
        foreach ($groups as $gid) {
            if (method_exists($mams, 'AssignUserToGroup')) {
                $mams->AssignUserToGroup($uid, $gid);
            }
        }
        self::setConnectingProperty($mams, $uid, $propName, $sub);
        $name = isset($profile['name']) ? (string) $profile['name'] : $username;
        if (method_exists($mams, 'SetUserPropertyFull')) {
            $mams->SetUserPropertyFull($uid, 'display_name', $name);
            if ($email !== '') {
                $mams->SetUserPropertyFull($uid, 'email', $email);
            }
            $mams->SetUserPropertyFull($uid, 'authhub_provider', (string) ($provider['name'] ?? ''));
        }
        \CMSMS\HookManager::do_hook('MAS_AuthHub::OnMamsUserProvisioned', array(
            'mams_uid' => $uid,
            'provider_id' => (int) $provider['id'],
            'created' => true,
        ));
        Mas_Ah_Audit::log($mod, 'mams.jit_created', 'info', array('username' => $username), $uid, (int) $provider['id']);
        return $uid;
    }

    private static function uniqueUsername($mams, $email, $sub)
    {
        $base = 'user';
        if ($email !== '' && strpos($email, '@') !== false) {
            $base = preg_replace('/[^a-z0-9_]/i', '_', strstr($email, '@', true));
        }
        if ($base === '') {
            $base = 'authhub';
        }
        $candidate = substr($base, 0, 40);
        $n = 0;
        while ($n < 100) {
            $try = $candidate . ($n > 0 ? (string) $n : '');
            if (method_exists($mams, 'IsValidUsername') && $mams->IsValidUsername($try)) {
                if (method_exists($mams, 'GetUserID') && !$mams->GetUserID($try)) {
                    return $try;
                }
            }
            $n++;
        }
        return 'authhub_' . substr($sub, 0, 12);
    }

    private static function defaultGroups(CMSModule $mod, array $provider)
    {
        $cfg = $provider['config'] ?? array();
        if (!empty($cfg['mams_default_groups']) && is_array($cfg['mams_default_groups'])) {
            return array_map('intval', $cfg['mams_default_groups']);
        }
        $raw = trim((string) $mod->GetPreference('mas_ah_mams_default_groups', ''));
        if ($raw === '') {
            return array();
        }
        return array_filter(array_map('intval', explode(',', $raw)));
    }
}
