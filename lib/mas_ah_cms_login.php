<?php
if (!defined('CMS_VERSION')) {
    exit;
}

final class Mas_Ah_CmsLoginBridge
{
    public static function loginCmsUser($user)
    {
        if (!$user || empty($user->id)) {
            throw new InvalidArgumentException('Invalid CMS user');
        }
        if (empty($user->active)) {
            throw new RuntimeException('User account is not active');
        }
        $loginOps = \CMSMS\LoginOperations::get_instance();
        $loginOps->initialize_authentication($user);
        $key = $loginOps->finalize_authentication($user);
        audit($user->id, 'Admin Username: ' . $user->username, 'Logged In via MAS_AuthHub');
        return $key;
    }

    public static function redirectAfterLogin()
    {
        $config = cms_utils::get_config();
        if (isset($_SESSION['login_redirect_to'])) {
            $url_ob = new \cms_url($_SESSION['login_redirect_to']);
            unset($_SESSION['login_redirect_to']);
            $url_ob->erase_queryvar('_s_');
            $url_ob->erase_queryvar('sp_');
            $url_ob->set_queryvar(CMS_SECURE_PARAM_NAME, $_SESSION[CMS_USER_KEY]);
            redirect((string) $url_ob);
        }
        $homepage = \cms_userprefs::get_for_user($_SESSION['cms_userid'], 'homepage');
        if (!$homepage) {
            $homepage = $config['admin_url'];
        }
        $homepage = \CmsAdminUtils::get_session_url($homepage);
        redirect(html_entity_decode($homepage));
    }
}
