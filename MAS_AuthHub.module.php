<?php
/**
 * MAS_AuthHub: centralized identity and access management for CMS Made Simple.
 *
 * @author master3395
 */
if (!defined('CMS_VERSION')) {
    exit;
}

require_once __DIR__ . '/lib/mas_ah_loader.php';

final class MAS_AuthHub extends CMSModule
{
    public const PERM_MANAGE = 'Manage MAS_AuthHub';
    public const PERM_USE = 'Use MAS_AuthHub';
    public const PREF_ADMIN_SECTION = 'mas_ah_admin_section';

    /**
     * Programmatic audit API (do not name "audit": CMSModule::audit() is final).
     */
    public static function logAudit($event, array $context = array(), $severity = 'info', $userId = 0, $providerId = 0)
    {
        $m = cms_utils::get_module('MAS_AuthHub');
        if ($m instanceof self) {
            Mas_Ah_Audit::log($m, $event, $severity, $context, $userId, $providerId);
        }
    }

    public static function linkUser($providerId, $externalSub, $cmsUserId = null, $mamsUserId = null, $email = '')
    {
        $m = cms_utils::get_module('MAS_AuthHub');
        if (!$m instanceof self) {
            return false;
        }
        return Mas_Ah_UserLink::upsert($m, array(
            'provider_id' => (int) $providerId,
            'external_sub' => (string) $externalSub,
            'cms_user_id' => $cmsUserId,
            'mams_user_id' => $mamsUserId,
            'email' => $email,
        ));
    }

    public function GetName()
    {
        return 'MAS_AuthHub';
    }

    public function GetFriendlyName()
    {
        return $this->Lang('friendlyname');
    }

    public function GetVersion()
    {
        return '1.0.5';
    }

    public function GetAuthor()
    {
        return 'master3395';
    }

    public function GetAuthorEmail()
    {
        return 'info@newstargeted.com';
    }

    public function GetAuthorUrl()
    {
        return 'https://newstargeted.com/contact/';
    }

    public function GetHelp()
    {
        require_once dirname(__DIR__) . '/MAS_Common/lib/mas_admin_ui.php';
        $mods = ModuleOperations::get_instance()->GetInstalledModules();
        $extra = array(
            'mas_ah_have_mams_registration' => in_array('MAMSRegistration', $mods, true) ? '1' : '0',
        );
        return Mas_Admin_Ui::fetchTabbedHelp($this, array(
            array('id' => 'general', 'lang' => 'help_general'),
            array('id' => 'configuration', 'lang' => 'help_configuration'),
            array('id' => 'protocols', 'lang' => 'help_protocols'),
            array('id' => 'security', 'lang' => 'help_security'),
            array('id' => 'troubleshooting', 'lang' => 'help_troubleshooting'),
        ), $extra);
    }

    public function GetAbout()
    {
        require_once dirname(__DIR__) . '/MAS_Common/lib/mas_admin_ui.php';
        $mods = ModuleOperations::get_instance()->GetInstalledModules();
        return Mas_Admin_Ui::fetchTabbedAbout($this, null, array(
            'mas_ah_have_mams_registration' => in_array('MAMSRegistration', $mods, true) ? '1' : '0',
        ));
    }

    public function GetChangeLog()
    {
        $baseDir = realpath($this->GetModulePath());
        $file = realpath($this->GetModulePath() . DIRECTORY_SEPARATOR . 'CHANGELOG.md');
        if (!$baseDir || !$file || !is_file($file) || strpos($file, $baseDir) !== 0) {
            return $this->Lang('changelog');
        }
        $markdown = @file_get_contents($file);
        if ($markdown === false || $markdown === '') {
            return $this->Lang('changelog');
        }
        return '<div class="mas_ah_changelog">' . nl2br(cms_htmlentities($markdown)) . '</div>';
    }

    public function GetAdminDescription()
    {
        return $this->Lang('moddescription');
    }

    public function HasAdmin()
    {
        return true;
    }

    public function IsPluginModule()
    {
        return true;
    }

    public function GetAdminSection()
    {
        return $this->GetPreference(self::PREF_ADMIN_SECTION, 'extensions');
    }

    public function VisibleToAdminUser()
    {
        return $this->CheckPermission(self::PERM_MANAGE) || $this->CheckPermission(self::PERM_USE);
    }

    public function GetDependencies()
    {
        return array(
            'CMSMSExt' => '1.0',
            'MAMS' => '1.0',
        );
    }

    public function MinimumCMSVersion()
    {
        return '2.2.10';
    }

    public function GetMinimumPHPVersion()
    {
        return '7.4.0';
    }

    public function GetMAMSAuthConsumer()
    {
        $iface = dirname(__DIR__) . '/MAMS/lib/interface.mams_auth_consumer.php';
        if (!is_readable($iface)) {
            return null;
        }
        require_once dirname(__FILE__) . '/lib/class.mas_ah_mams_consumer.php';
        return new Mas_Ah_MamsConsumer($this);
    }

    public function ShowDonationsTab()
    {
        return $this->GetPreference('hidedonationstab') != $this->GetVersion();
    }

    public function InstallPostMessage()
    {
        return $this->Lang('postinstall');
    }

    public function UninstallPostMessage()
    {
        return $this->Lang('postuninstall');
    }

    public function UninstallPreMessage()
    {
        return $this->Lang('really_uninstall');
    }

    public function InitializeFrontend()
    {
        $this->RestrictUnknownParams();
        $this->RegisterModulePlugin(true, false);
        $types = array(CLEAN_STRING, CLEAN_INT, CLEAN_FLOAT);
        foreach (array(
            'action', 'provider_id', 'return_type', 'mode', 'code', 'state',
            'client_id', 'response_type', 'scope', 'redirect_uri',
        ) as $p) {
            $this->SetParameterType($p, CLEAN_STRING);
        }
        $this->SetParameterType('provider_id', CLEAN_INT);
    }

    public function InitializeAdmin()
    {
        $this->CreateParameter('action', 'defaultadmin', $this->Lang('help_action'));
    }

    public function RegisterEvents()
    {
        $this->CreateEvent('OnProviderLinked');
        $this->CreateEvent('OnMamsLogin');
        $this->CreateEvent('OnMamsLogout');
        $this->CreateEvent('OnMamsUserProvisioned');
    }

    public function DoEvent($originator, $eventname, &$params)
    {
        if ($originator === 'Core') {
            $this->handleCoreEvent($eventname, $params);
        }
        if ($originator === 'MAMS') {
            Mas_Ah_MamsEvents::handle($this, $originator, $eventname, $params);
        }
        if ($originator === 'MAMSRegistration') {
            Mas_Ah_MamsRegistration::handle($this, $originator, $eventname, $params);
        }
    }

    private function handleCoreEvent($eventname, &$params)
    {
        switch ($eventname) {
            case 'LoginFailed':
                Mas_Ah_Audit::log($this, 'core.login_failed', 'warning', $params, 0, 0);
                break;
            case 'LoginPost':
                $uid = isset($params['user']->id) ? (int) $params['user']->id : 0;
                Mas_Ah_Audit::log($this, 'core.login_post', 'info', array(), $uid, 0);
                break;
            case 'LogoutPost':
                Mas_Ah_Audit::log($this, 'core.logout_post', 'info', $params, isset($params['uid']) ? (int) $params['uid'] : 0, 0);
                break;
        }
    }
}
