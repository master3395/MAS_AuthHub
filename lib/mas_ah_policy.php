<?php
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_Policy
{
    public static function allowAdminSso(CMSModule $mod)
    {
        return (int) $mod->GetPreference('mas_ah_enable_admin_sso', '1') === 1;
    }

    public static function allowMamsSso(CMSModule $mod)
    {
        return (int) $mod->GetPreference('mas_ah_enable_mams_sso', '1') === 1;
    }

    public static function autoLinkByEmail(CMSModule $mod)
    {
        return (int) $mod->GetPreference('mas_ah_auto_link_email', '1') === 1;
    }

    public static function rateLimitAllow(CMSModule $mod, $bucket)
    {
        $max = (int) $mod->GetPreference('mas_ah_rate_per_hour', '60');
        if ($max < 1) {
            return true;
        }
        $key = 'mas_ah_rl_' . md5((string) $bucket);
        $raw = (string) $mod->GetPreference($key, '');
        $now = time();
        $window = 3600;
        $data = array('t' => $now, 'c' => 0);
        if ($raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }
        if (!isset($data['t']) || ($now - (int) $data['t']) > $window) {
            $data = array('t' => $now, 'c' => 0);
        }
        $data['c'] = (int) ($data['c'] ?? 0) + 1;
        $mod->SetPreference($key, json_encode($data));
        return $data['c'] <= $max;
    }

    public static function cmsUserAllowed(CMSModule $mod, $user)
    {
        if (!$user || empty($user->id) || empty($user->active)) {
            return false;
        }
        $allowed = trim((string) $mod->GetPreference('mas_ah_admin_allowed_groups', ''));
        if ($allowed === '') {
            return true;
        }
        $ids = array_filter(array_map('intval', explode(',', $allowed)));
        if (count($ids) === 0) {
            return true;
        }
        $groups = $user->groups ?? array();
        foreach ($ids as $gid) {
            if (in_array($gid, $groups, true)) {
                return true;
            }
        }
        return false;
    }
}
