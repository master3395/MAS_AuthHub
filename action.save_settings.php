<?php
if (!function_exists('cmsms')) {
    exit;
}
if (!$this->CheckPermission(MAS_AuthHub::PERM_MANAGE)) {
    return $this->DisplayErrorPage($id, $params, $returnid, $this->Lang('accessdenied'));
}
$this->SetPreference('mas_ah_enable_admin_sso', !empty($params['enable_admin_sso']) ? '1' : '0');
$this->SetPreference('mas_ah_enable_mams_sso', !empty($params['enable_mams_sso']) ? '1' : '0');
$this->SetPreference('mas_ah_auto_link_email', !empty($params['auto_link_email']) ? '1' : '0');
$this->SetPreference('mas_ah_session_ttl', max(300, (int) ($params['session_ttl'] ?? 3600)));
$this->SetPreference('mas_ah_rate_per_hour', max(1, (int) ($params['rate_per_hour'] ?? 60)));
$this->Redirect($id, 'defaultadmin', $returnid, array('activetab' => 'settings', 'msg' => 'prefsupdated'));
