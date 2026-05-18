<?php
if (!defined('CMS_VERSION')) {
    exit;
}

require_once dirname(__FILE__) . '/lib/mas_ah_tables.php';

$db = $this->GetDb();
$tables = array(
    Mas_Ah_Tables::auditLog(),
    Mas_Ah_Tables::webauthnCredentials(),
    Mas_Ah_Tables::oauthAuthCodes(),
    Mas_Ah_Tables::oauthClients(),
    Mas_Ah_Tables::tokens(),
    Mas_Ah_Tables::sessions(),
    Mas_Ah_Tables::userLinks(),
    Mas_Ah_Tables::providers(),
);
foreach ($tables as $t) {
    $db->Execute('DROP TABLE IF EXISTS ' . $t);
}

$this->RemovePermission('Manage MAS_AuthHub');
$this->RemovePermission('Use MAS_AuthHub');

$this->Audit(0, $this->Lang('friendlyname'), $this->Lang('uninstalled'));
