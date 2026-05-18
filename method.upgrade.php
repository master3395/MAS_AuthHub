<?php
if (!defined('CMS_VERSION')) {
    exit;
}

$old = isset($oldversion) ? $oldversion : '0';
if (version_compare($old, '1.0.0', '<')) {
    Mas_Ah_MamsEvents::register($this);
    Mas_Ah_MamsRegistration::register($this);
}
require_once dirname(__DIR__) . '/MAS_Common/lib/mas_admin_ui.php';
Mas_Admin_Ui::ensureIconGif($this);
Mas_Admin_Ui::ensureBanner($this);
$this->Audit(0, $this->Lang('friendlyname'), $this->Lang('upgraded', $this->GetVersion()));
