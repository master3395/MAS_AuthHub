<?php
if (!function_exists('cmsms')) {
    exit;
}
$db = $this->GetDb();
if (isset($params['submit_oauth_client']) && $this->CheckPermission(MAS_AuthHub::PERM_MANAGE)) {
    $clientId = trim((string) ($params['oauth_client_id'] ?? ''));
    if ($clientId === '') {
        $clientId = 'ah_' . bin2hex(random_bytes(8));
    }
    $secret = (string) ($params['oauth_client_secret'] ?? '');
    $hash = $secret !== '' ? password_hash($secret, PASSWORD_DEFAULT) : '';
    $uris = array_filter(array_map('trim', explode("\n", (string) ($params['redirect_uris'] ?? ''))));
    $db->Execute(
        'INSERT INTO ' . Mas_Ah_Tables::oauthClients() . ' (client_id, secret_hash, name, redirect_uris, grants, scopes, enabled, created) VALUES (?,?,?,?,?,?,1,?)',
        array(
            $clientId,
            $hash,
            trim((string) ($params['oauth_client_name'] ?? 'Client')),
            json_encode($uris, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'authorization_code refresh_token',
            trim((string) ($params['oauth_scopes'] ?? 'openid profile email')),
            time(),
        )
    );
    echo $this->ShowMessage($this->Lang('prefsupdated'));
}
$clients = $db->GetArray('SELECT id, client_id, name, enabled, created FROM ' . Mas_Ah_Tables::oauthClients() . ' ORDER BY id DESC');
$smarty->assign('mod', $this);
$smarty->assign('id', $id);
$smarty->assign('returnid', $returnid);
$smarty->assign('actionid', $actionid);
$smarty->assign('clients', is_array($clients) ? $clients : array());
$smarty->assign('discovery_url', Mas_Ah_Config::moduleActionUrl($this, 'oidc_discovery', array()));
$smarty->assign('formstart', $this->CreateFormStart($id, 'defaultadmin', $returnid, 'post', '', false, '', array('activetab' => 'oauth_clients')));
$smarty->assign('formend', $this->CreateFormEnd());
$smarty->assign('oauth_client_name', $this->CreateInputText($id, 'oauth_client_name', '', 40, 120));
$smarty->assign('oauth_client_id', $this->CreateInputText($id, 'oauth_client_id', '', 40, 120));
$smarty->assign('oauth_client_secret', $this->CreateInputPassword($id, 'oauth_client_secret', '', 40, 120));
$smarty->assign('redirect_uris', $this->CreateTextArea(false, $id, '', 'redirect_uris', '', '', '', '', 3, 60));
$smarty->assign('oauth_scopes', $this->CreateInputText($id, 'oauth_scopes', 'openid profile email', 40, 120));
$smarty->assign('submit_client', $this->CreateInputSubmit($id, 'submit_oauth_client', $this->Lang('oauth_client_add')));
echo $this->ProcessTemplate('oauth_clients.tpl');
