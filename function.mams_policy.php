<?php
if (!function_exists('cmsms')) {
    exit;
}
$mams = cms_utils::get_module('MAMS');
$authMod = '';
$mamsOk = $mams ? 'yes' : 'no';
$regOk = cms_utils::get_module('MAMSRegistration') ? 'yes' : 'no';
if ($mams) {
    $authMod = (string) $mams->GetPreference('auth_module', '');
}
if (isset($params['submit_mams_policy']) && $this->CheckPermission(MAS_AuthHub::PERM_MANAGE)) {
    $this->SetPreference('mas_ah_mams_jit_create', !empty($params['mams_jit_create']) ? '1' : '0');
    $this->SetPreference('mas_ah_block_duplicate_email_signup', !empty($params['block_duplicate_email_signup']) ? '1' : '0');
    $this->SetPreference('mas_ah_mams_default_groups', trim((string) ($params['mams_default_groups'] ?? '')));
    $this->SetPreference('mas_ah_default_connecting_property', trim((string) ($params['default_connecting_property'] ?? 'authhub_sub')));
    echo $this->ShowMessage($this->Lang('prefsupdated'));
}
$smarty->assign('mod', $this);
$smarty->assign('id', $id);
$smarty->assign('returnid', $returnid);
$smarty->assign('actionid', $actionid);
$smarty->assign('mams_ok', $mamsOk);
$smarty->assign('mams_auth_module', $authMod !== '' ? $authMod : '(built-in)');
$smarty->assign('mams_registration', $regOk);
$smarty->assign('authhub_active', ($authMod === 'MAS_AuthHub') ? 'yes' : 'no');
$smarty->assign('formstart', $this->CreateFormStart($id, 'defaultadmin', $returnid, 'post', '', false, '', array('activetab' => 'mams')));
$smarty->assign('formend', $this->CreateFormEnd());
$smarty->assign('mams_jit_create', $this->CreateInputCheckbox($id, 'mams_jit_create', '1', (int) $this->GetPreference('mas_ah_mams_jit_create', '1')));
$smarty->assign('block_duplicate_email_signup', $this->CreateInputCheckbox($id, 'block_duplicate_email_signup', '1', (int) $this->GetPreference('mas_ah_block_duplicate_email_signup', '0')));
$smarty->assign('mams_default_groups', $this->CreateInputText($id, 'mams_default_groups', $this->GetPreference('mas_ah_mams_default_groups', ''), 40, 80));
$smarty->assign('default_connecting_property', $this->CreateInputText($id, 'default_connecting_property', $this->GetPreference('mas_ah_default_connecting_property', 'authhub_sub'), 30, 64));
$smarty->assign('submit', $this->CreateInputSubmit($id, 'submit_mams_policy', $this->Lang('submit')));
echo $this->ProcessTemplate('mams_policy.tpl');
