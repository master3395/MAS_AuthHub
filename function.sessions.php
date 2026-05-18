<?php
if (!function_exists('cmsms')) {
    exit;
}
if (isset($params['revoke_sid']) && $this->CheckPermission(MAS_AuthHub::PERM_MANAGE)) {
    Mas_Ah_Session::revoke($this, (string) $params['revoke_sid']);
    echo $this->ShowMessage('Session revoked.');
}
if (isset($params['revoke_token']) && $this->CheckPermission(MAS_AuthHub::PERM_MANAGE)) {
    Mas_Ah_TokenStore::revoke($this, (int) $params['revoke_token']);
    echo $this->ShowMessage('Token revoked.');
}
$sessions = Mas_Ah_Session::listActive($this, 100);
$tokens = Mas_Ah_TokenStore::listForUser($this, 0, 0, 50);
if (is_array($sessions)) {
    foreach ($sessions as $idx => $row) {
        $sessions[$idx]['revoke_url'] = $this->CreateLink(
            $id,
            'defaultadmin',
            $returnid,
            '',
            array('activetab' => 'sessions', 'revoke_sid' => (string) $row['session_id']),
            '',
            true
        );
    }
}
if (is_array($tokens)) {
    foreach ($tokens as $idx => $row) {
        $tokens[$idx]['revoke_url'] = $this->CreateLink(
            $id,
            'defaultadmin',
            $returnid,
            '',
            array('activetab' => 'sessions', 'revoke_token' => (int) $row['id']),
            '',
            true
        );
    }
}
$smarty->assign('mod', $this);
$smarty->assign('id', $id);
$smarty->assign('returnid', $returnid);
$smarty->assign('sessions', $sessions);
$smarty->assign('tokens', $tokens);
echo $this->ProcessTemplate('sessions.tpl');
