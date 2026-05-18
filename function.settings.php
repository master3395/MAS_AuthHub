<?php
if (!function_exists('cmsms')) {
    exit;
}
require_once dirname(__DIR__) . '/MAS_Common/lib/mas_admin_ui.php';
$smarty->assign('mod', $this);
$smarty->assign('id', $id);
$smarty->assign('returnid', $returnid);
Mas_Admin_Ui::assignBranding($this, $smarty);
$smarty->assign('start_form', $this->CreateFormStart($id, 'save_settings', $returnid));
$smarty->assign('enable_admin_sso', $this->CreateInputCheckbox($id, 'enable_admin_sso', '1', (int) $this->GetPreference('mas_ah_enable_admin_sso', '1')));
$smarty->assign('enable_mams_sso', $this->CreateInputCheckbox($id, 'enable_mams_sso', '1', (int) $this->GetPreference('mas_ah_enable_mams_sso', '1')));
$smarty->assign('auto_link_email', $this->CreateInputCheckbox($id, 'auto_link_email', '1', (int) $this->GetPreference('mas_ah_auto_link_email', '1')));
$smarty->assign('session_ttl', $this->CreateInputText($id, 'session_ttl', $this->GetPreference('mas_ah_session_ttl', '3600'), 10, 10));
$smarty->assign('rate_per_hour', $this->CreateInputText($id, 'rate_per_hour', $this->GetPreference('mas_ah_rate_per_hour', '60'), 6, 6));
$smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('submit')));
$smarty->assign('end_form', $this->CreateFormEnd());
echo $this->ProcessTemplate('settings.tpl');
