<?php
if (!function_exists('cmsms')) {
    exit;
}
if (!$this->CheckPermission(MAS_AuthHub::PERM_MANAGE)) {
    return $this->DisplayErrorPage($id, $params, $returnid, $this->Lang('accessdenied'));
}
$section = isset($params['adminsection']) ? (string) $params['adminsection'] : 'extensions';
$allowed = array('extensions', 'content', 'siteadmin', 'usersgroups', 'layout', 'ecommerce');
if (!in_array($section, $allowed, true)) {
    $section = 'extensions';
}
$this->SetPreference(MAS_AuthHub::PREF_ADMIN_SECTION, $section);
if (!empty($params['showdonationstab'])) {
    $this->SetPreference('hidedonationstab', '');
} else {
    $this->SetPreference('hidedonationstab', $this->GetVersion());
}
$this->Redirect($id, 'defaultadmin', $returnid, array('activetab' => 'adminsettings', 'msg' => 'prefsupdated'));
