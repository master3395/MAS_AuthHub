<?php
if (!function_exists('cmsms')) {
    exit;
}
if (!$this->CheckPermission(MAS_AuthHub::PERM_MANAGE)) {
    return $this->DisplayErrorPage($id, $params, $returnid, $this->Lang('accessdenied'));
}
require_once dirname(__DIR__) . '/MAS_Common/lib/mas_admin_ui.php';
Mas_Admin_Ui::ensureIconGif($this);
Mas_Admin_Ui::ensureBanner($this);
Mas_Admin_Ui::assignBranding($this, cmsms()->GetSmarty());
Mas_Admin_Ui::echoSettingsBanner($this);
$actionid = $id;
if (isset($params['hidedonationssubmit'])) {
    $this->SetPreference('hidedonationstab', $this->GetVersion());
}
if (!empty($params['msg'])) {
    echo $this->ShowMessage($this->Lang($params['msg']));
}
$activetab = isset($params['activetab']) ? (string) $params['activetab'] : 'settings';
echo $this->StartTabHeaders();
echo $this->SetTabHeader('settings', $this->Lang('settings'), $activetab === 'settings');
echo $this->SetTabHeader('providers', $this->Lang('tab_providers'), $activetab === 'providers');
echo $this->SetTabHeader('mams', $this->Lang('tab_mams'), $activetab === 'mams');
echo $this->SetTabHeader('oauth_clients', $this->Lang('tab_oauth_clients'), $activetab === 'oauth_clients');
echo $this->SetTabHeader('sessions', $this->Lang('tab_sessions'), $activetab === 'sessions');
echo $this->SetTabHeader('audit', $this->Lang('tab_audit'), $activetab === 'audit');
echo $this->SetTabHeader('adminsettings', $this->Lang('tab_adminsettings'), $activetab === 'adminsettings');
if ($this->ShowDonationsTab()) {
    echo $this->SetTabHeader('donations', $this->Lang('donationstab'), $activetab === 'donations');
}
echo $this->EndTabHeaders();
echo $this->StartTabContent();
echo $this->StartTab('settings');
include dirname(__FILE__) . '/function.settings.php';
echo $this->EndTab();
echo $this->StartTab('providers');
include dirname(__FILE__) . '/function.providers.php';
echo $this->EndTab();
echo $this->StartTab('mams');
include dirname(__FILE__) . '/function.mams_policy.php';
echo $this->EndTab();
echo $this->StartTab('oauth_clients');
include dirname(__FILE__) . '/function.oauth_clients.php';
echo $this->EndTab();
echo $this->StartTab('sessions');
include dirname(__FILE__) . '/function.sessions.php';
echo $this->EndTab();
echo $this->StartTab('audit');
include dirname(__FILE__) . '/function.audit.php';
echo $this->EndTab();
echo $this->StartTab('adminsettings');
include dirname(__FILE__) . '/function.admin_settings.php';
echo $this->EndTab();
if ($this->ShowDonationsTab()) {
    echo $this->StartTab('donations');
    include dirname(__FILE__) . '/function.donations.php';
    echo $this->EndTab();
}
echo $this->EndTabContent();
