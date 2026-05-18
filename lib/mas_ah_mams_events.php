<?php
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_MamsEvents
{
    public static function register(CMSModule $mod)
    {
        $mod->AddEventHandler('MAMS', 'OnLoginFailed', false);
        $mod->AddEventHandler('MAMS', 'OnLogin', false);
        $mod->AddEventHandler('MAMS', 'OnLogout', false);
        $mod->AddEventHandler('MAMS', 'OnCreateUser', false);
    }

    public static function handle(CMSModule $mod, $originator, $eventname, array $params)
    {
        if ($originator !== 'MAMS') {
            return;
        }
        switch ($eventname) {
            case 'OnLoginFailed':
                Mas_Ah_Audit::log($mod, 'mams.login_failed', 'warning', $params, 0, 0);
                break;
            case 'OnLogin':
                $uid = isset($params['id']) ? (int) $params['id'] : 0;
                Mas_Ah_Audit::log($mod, 'mams.login', 'info', $params, $uid, 0);
                break;
            case 'OnLogout':
                if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['mas_ah_sid'])) {
                    Mas_Ah_Session::revoke($mod, (string) $_SESSION['mas_ah_sid']);
                }
                \CMSMS\HookManager::do_hook('MAS_AuthHub::OnMamsLogout', $params);
                Mas_Ah_Audit::log($mod, 'mams.logout', 'info', $params, 0, 0);
                break;
            case 'OnCreateUser':
                Mas_Ah_Audit::log($mod, 'mams.user_created', 'info', $params, isset($params['id']) ? (int) $params['id'] : 0, 0);
                break;
        }
    }
}
