<?php
if (!function_exists('cmsms')) {
    exit;
}
require_once dirname(__DIR__) . '/MAS_Common/lib/mas_admin_ui.php';
$smarty->assign('mod', $this);
Mas_Admin_Ui::assignBranding($this, $smarty);
$donationsHidden = ($this->GetPreference('hidedonationstab') == $this->GetVersion());
$adminsections = array(
    $this->Lang('extensions') => 'extensions',
    'content' => 'content',
    'siteadmin' => 'siteadmin',
    'usersgroups' => 'usersgroups',
    'layout' => 'layout',
    'ecommerce' => 'ecommerce',
);
$smarty->assign('formstart', $this->CreateFormStart($id, 'admin_settings_save', $returnid));
$smarty->assign('formend', $this->CreateFormEnd());
$smarty->assign('adminsection_dropdown', $this->CreateInputDropdown($id, 'adminsection', $adminsections, -1, $this->GetPreference(MAS_AuthHub::PREF_ADMIN_SECTION, 'extensions')));
$smarty->assign('showdonationstab_checkbox', $this->CreateInputCheckbox($id, 'showdonationstab', '1', !$donationsHidden));
$smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('submit')));
echo $this->ProcessTemplate('admin_settings.tpl');
