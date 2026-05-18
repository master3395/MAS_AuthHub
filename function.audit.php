<?php
if (!function_exists('cmsms')) {
    exit;
}
$db = $this->GetDb();
if (isset($params['export_audit']) && $this->CheckPermission(MAS_AuthHub::PERM_MANAGE)) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=mas_authhub_audit.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, array('id', 'created', 'event', 'severity', 'user_id', 'provider_id'));
    $rows = $db->GetArray('SELECT id, created, event, severity, user_id, provider_id FROM ' . Mas_Ah_Tables::auditLog() . ' ORDER BY id DESC LIMIT 5000');
    if (is_array($rows)) {
        foreach ($rows as $r) {
            fputcsv($out, $r);
        }
    }
    fclose($out);
    exit;
}
$rows = $db->GetArray('SELECT * FROM ' . Mas_Ah_Tables::auditLog() . ' ORDER BY id DESC LIMIT 200');
$smarty->assign('mod', $this);
$smarty->assign('id', $id);
$smarty->assign('returnid', $returnid);
$smarty->assign('rows', is_array($rows) ? $rows : array());
$smarty->assign('export_url', $this->CreateLink($id, 'defaultadmin', $returnid, '', array('activetab' => 'audit', 'export_audit' => 1), '', true));
echo $this->ProcessTemplate('audit.tpl');
