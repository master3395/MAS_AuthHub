<?php
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_MamsRegistration
{
    public static function register(CMSModule $mod)
    {
        if (!cms_utils::get_module('MAMSRegistration')) {
            return;
        }
        $mod->AddEventHandler('MAMSRegistration', 'beforeNewUser', false);
        $mod->AddEventHandler('MAMSRegistration', 'onNewUser', false);
        $mod->AddEventHandler('MAMSRegistration', 'onUserRegistered', false);
    }

    public static function handle(CMSModule $mod, $originator, $eventname, &$params)
    {
        if ($originator !== 'MAMSRegistration') {
            return;
        }
        switch ($eventname) {
            case 'beforeNewUser':
                self::beforeNewUser($mod, $params);
                break;
            case 'onNewUser':
                Mas_Ah_Audit::log($mod, 'mamsreg.temp_user', 'info', $params, 0, 0);
                break;
            case 'onUserRegistered':
                Mas_Ah_Audit::log($mod, 'mamsreg.registered', 'info', $params, isset($params['id']) ? (int) $params['id'] : 0, 0);
                break;
        }
    }

    private static function beforeNewUser(CMSModule $mod, array &$params)
    {
        if ((int) $mod->GetPreference('mas_ah_block_duplicate_email_signup', '0') !== 1) {
            return;
        }
        $email = isset($params['email']) ? strtolower(trim((string) $params['email'])) : '';
        if ($email === '') {
            return;
        }
        $link = Mas_Ah_UserLink::findByEmail($mod, $email, 0);
        if ($link && ((int) ($link['mams_user_id'] ?? 0) > 0)) {
            $msg = $mod->Lang('error_email_sso_linked');
            if (class_exists('MAMSREGValidationError')) {
                throw new MAMSREGValidationError($msg);
            }
            $params['message'] = $msg;
        }
    }
}
