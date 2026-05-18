<?php
if (!function_exists('cmsms')) {
    exit;
}
$drivers = Mas_Ah_DriverRegistry::listKeys();
$rows = Mas_Ah_DriverRegistry::listProviders($this, false);
if (isset($params['delete_provider']) && $this->CheckPermission(MAS_AuthHub::PERM_MANAGE)) {
    $pid = (int) $params['delete_provider'];
    $this->GetDb()->Execute('DELETE FROM ' . Mas_Ah_Tables::providers() . ' WHERE id = ?', array($pid));
    echo $this->ShowMessage('Provider deleted.');
    $rows = Mas_Ah_DriverRegistry::listProviders($this, false);
}
if (isset($params['submit_provider']) && $this->CheckPermission(MAS_AuthHub::PERM_MANAGE)) {
    $cfg = array(
        'provider_key' => preg_replace('/[^a-z0-9_\-]/i', '', (string) ($params['provider_key'] ?? 'p' . time())),
        'client_id' => trim((string) ($params['client_id'] ?? '')),
        'authorize_url' => trim((string) ($params['authorize_url'] ?? '')),
        'token_url' => trim((string) ($params['token_url'] ?? '')),
        'userinfo_url' => trim((string) ($params['userinfo_url'] ?? '')),
        'scope' => trim((string) ($params['scope'] ?? 'openid profile email')),
        'idp_entity_id' => trim((string) ($params['idp_entity_id'] ?? '')),
        'idp_sso_url' => trim((string) ($params['idp_sso_url'] ?? '')),
        'mams_connecting_property' => trim((string) ($params['mams_connecting_property'] ?? '')),
    );
    $secret = trim((string) ($params['client_secret'] ?? ''));
    if ($secret !== '') {
        $this->SetPreference('mas_ah_secret_' . $cfg['provider_key'] . '_client_secret', Mas_Ah_Crypto::encryptPref($this, $secret));
    }
    $now = time();
    $db = $this->GetDb();
    $editId = (int) ($params['provider_id'] ?? 0);
    if ($editId > 0) {
        $db->Execute(
            'UPDATE ' . Mas_Ah_Tables::providers() . ' SET driver=?, name=?, enabled=?, config_json=?, sort_order=?, updated=? WHERE id=?',
            array(
                (string) ($params['driver'] ?? 'oauth2'),
                trim((string) ($params['name'] ?? 'Provider')),
                !empty($params['enabled']) ? 1 : 0,
                json_encode($cfg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                (int) ($params['sort_order'] ?? 0),
                $now,
                $editId,
            )
        );
    } else {
        $db->Execute(
            'INSERT INTO ' . Mas_Ah_Tables::providers() . ' (driver,name,enabled,config_json,sort_order,created,updated) VALUES (?,?,?,?,?,?,?)',
            array(
                (string) ($params['driver'] ?? 'oauth2'),
                trim((string) ($params['name'] ?? 'Provider')),
                !empty($params['enabled']) ? 1 : 0,
                json_encode($cfg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                (int) ($params['sort_order'] ?? 0),
                $now,
                $now,
            )
        );
    }
    echo $this->ShowMessage($this->Lang('prefsupdated'));
    $rows = Mas_Ah_DriverRegistry::listProviders($this, false);
}
$smarty->assign('mod', $this);
$smarty->assign('id', $id);
$smarty->assign('returnid', $returnid);
$smarty->assign('actionid', $actionid);
$smarty->assign('drivers', $drivers);
foreach ($rows as $idx => $row) {
    $rows[$idx]['delete_url'] = $this->CreateLink(
        $id,
        'defaultadmin',
        $returnid,
        '',
        array('activetab' => 'providers', 'delete_provider' => (int) $row['id']),
        '',
        true
    );
}
$smarty->assign('providers', $rows);
$smarty->assign('formstart', $this->CreateFormStart($id, 'defaultadmin', $returnid, 'post', '', false, '', array('activetab' => 'providers', 'submit_provider' => '1')));
$smarty->assign('formend', $this->CreateFormEnd());
echo $this->ProcessTemplate('providers.tpl');
