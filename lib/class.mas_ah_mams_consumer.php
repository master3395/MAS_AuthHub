<?php
if (!defined('CMS_VERSION')) {
    exit;
}

require_once dirname(__DIR__) . '/../MAMS/lib/interface.mams_auth_consumer.php';

final class Mas_Ah_MamsConsumer implements mams_auth_consumer
{
    /** @var CMSModule */
    private $mod;

    public function __construct(CMSModule $mod)
    {
        $this->mod = $mod;
    }

    public function is_authenticated()
    {
        $mams = cms_utils::get_module('MAMS');
        if (!$mams) {
            return false;
        }
        return (int) $mams->LoggedInId() > 0;
    }

    public function get_capabilities()
    {
        return array(
            mams_auth_consumer::CAPABILITY_ALTLOGIN,
            mams_auth_consumer::CAPABILITY_LOGOUT,
        );
    }

    public function has_capability($flag)
    {
        if (!is_array($flag)) {
            $flag = array($flag);
        }
        foreach ($flag as $one) {
            if (in_array($one, $this->get_capabilities(), true)) {
                return true;
            }
        }
        return false;
    }

    public function get_login_display($id, $returnid, $params)
    {
        if (!Mas_Ah_Policy::allowMamsSso($this->mod)) {
            return '';
        }
        $smarty = cms_utils::get_smarty();
        if (!$smarty) {
            return '';
        }
        $providers = Mas_Ah_DriverRegistry::listProviders($this->mod, true);
        $links = array();
        foreach ($providers as $p) {
            if (!in_array((string) $p['driver'], array('oauth2', 'oidc', 'saml', 'webauthn'), true)) {
                continue;
            }
            $links[] = array(
                'name' => $p['name'],
                'url' => Mas_Ah_Config::moduleActionUrl($this->mod, 'oauth_start', array(
                    'provider_id' => (int) $p['id'],
                    'return_type' => 'mams',
                )),
                'driver' => $p['driver'],
            );
        }
        $smarty->assign('mas_ah_providers', $links);
        $smarty->assign('mas_ah_webauthn_url', Mas_Ah_Config::moduleActionUrl($this->mod, 'webauthn', array(
            'mode' => 'login',
            'return_type' => 'mams',
        )));
        return $this->mod->ProcessTemplate('mams_alt_login.tpl');
    }

    public function get_logout_display($id, $returnid, $params)
    {
        return '';
    }

    public function get_changesettings_display($id, $returnid, $params)
    {
        return '';
    }

    public function get_user_info()
    {
        return array();
    }

    public function get_connecting_property_name()
    {
        return (string) $this->mod->GetPreference('mas_ah_default_connecting_property', 'authhub_sub');
    }

    public function get_unique_identifier()
    {
        return '';
    }

    public function get_group_list($with_count = false)
    {
        return array();
    }

    public function get_group_membership($userid)
    {
        return null;
    }

    public function get_default_groups()
    {
        return null;
    }

    public function get_username_prompt()
    {
        $mams = cms_utils::get_module('MAMS');
        return $mams ? $mams->Lang('prompt_username') : 'Username';
    }

    public function validate_username($username, $check_email_addr = false, $uid = -1)
    {
        $mams = cms_utils::get_module('MAMS');
        if ($mams && method_exists($mams, 'IsValidUsername')) {
            return (bool) $mams->IsValidUsername($username, $check_email_addr, $uid);
        }
        return true;
    }
}
