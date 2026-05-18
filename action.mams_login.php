<?php
if (!function_exists('cmsms')) {
    exit;
}
$providers = Mas_Ah_DriverRegistry::listProviders($this, true);
$links = array();
foreach ($providers as $p) {
    $links[] = array(
        'name' => $p['name'],
        'url' => Mas_Ah_Config::moduleActionUrl($this, 'oauth_start', array(
            'provider_id' => (int) $p['id'],
            'return_type' => 'mams',
        )),
    );
}
$smarty = cms_utils::get_smarty();
$smarty->assign('mod', $this);
$smarty->assign('mas_ah_providers', $links);
$smarty->assign('mas_ah_webauthn_url', Mas_Ah_Config::moduleActionUrl($this, 'webauthn', array('mode' => 'login', 'return_type' => 'mams')));
echo $this->ProcessTemplate('mams_alt_login.tpl');
