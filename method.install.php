<?php
if (!defined('CMS_VERSION')) {
    exit;
}

require_once dirname(__FILE__) . '/lib/mas_ah_tables.php';
require_once dirname(__FILE__) . '/lib/mas_ah_mams_events.php';
require_once dirname(__FILE__) . '/lib/mas_ah_mams_registration.php';

$db = $this->GetDb();
$dict = NewDataDictionary($db);
$tabopt = array('mysql' => 'TYPE=InnoDB');

$flds = '
    id I KEY NOTNULL,
    driver C(32) NOTNULL,
    name C(128) NOTNULL,
    enabled I1 NOTNULL DEFAULT 1,
    config_json X2,
    sort_order I NOTNULL DEFAULT 0,
    created I NOTNULL,
    updated I NOTNULL
';
$sql = $dict->CreateTableSQL(Mas_Ah_Tables::providers(), $flds, $tabopt);
$dict->ExecuteSQLArray($sql);

$flds = '
    id I KEY NOTNULL,
    provider_id I NOTNULL,
    external_sub C(255) NOTNULL,
    cms_user_id I,
    mams_user_id I,
    email C(255),
    attrs_json X2,
    created I NOTNULL,
    updated I NOTNULL
';
$sql = $dict->CreateTableSQL(Mas_Ah_Tables::userLinks(), $flds, $tabopt);
$dict->ExecuteSQLArray($sql);
$db->Execute('CREATE UNIQUE INDEX idx_mas_ah_ul_provider_sub ON ' . Mas_Ah_Tables::userLinks() . ' (provider_id, external_sub)');

$flds = '
    id I KEY NOTNULL,
    session_id C(64) NOTNULL,
    cms_user_id I NOTNULL DEFAULT 0,
    mams_user_id I NOTNULL DEFAULT 0,
    provider_id I NOTNULL DEFAULT 0,
    ip_hash C(64),
    ua_hash C(64),
    expires_at I NOTNULL,
    revoked I1 NOTNULL DEFAULT 0,
    created I NOTNULL
';
$sql = $dict->CreateTableSQL(Mas_Ah_Tables::sessions(), $flds, $tabopt);
$dict->ExecuteSQLArray($sql);

$flds = '
    id I KEY NOTNULL,
    token_type C(16) NOTNULL,
    token_enc X NOTNULL,
    scopes C(255),
    expires_at I NOTNULL,
    provider_id I NOTNULL DEFAULT 0,
    cms_user_id I NOTNULL DEFAULT 0,
    mams_user_id I NOTNULL DEFAULT 0,
    created I NOTNULL
';
$sql = $dict->CreateTableSQL(Mas_Ah_Tables::tokens(), $flds, $tabopt);
$dict->ExecuteSQLArray($sql);

$flds = '
    id I KEY NOTNULL,
    client_id C(64) NOTNULL,
    secret_hash C(255),
    name C(128) NOTNULL,
    redirect_uris X2,
    grants C(255),
    scopes C(255),
    enabled I1 NOTNULL DEFAULT 1,
    created I NOTNULL
';
$sql = $dict->CreateTableSQL(Mas_Ah_Tables::oauthClients(), $flds, $tabopt);
$dict->ExecuteSQLArray($sql);

$flds = '
    id I KEY NOTNULL,
    code C(64) NOTNULL,
    client_id C(64) NOTNULL,
    user_id I NOTNULL,
    scopes C(255),
    redirect_uri C(512),
    expires_at I NOTNULL,
    used I1 NOTNULL DEFAULT 0,
    created I NOTNULL
';
$sql = $dict->CreateTableSQL(Mas_Ah_Tables::oauthAuthCodes(), $flds, $tabopt);
$dict->ExecuteSQLArray($sql);

$flds = '
    id I KEY NOTNULL,
    credential_id C(255) NOTNULL,
    public_key X NOTNULL,
    sign_count I NOTNULL DEFAULT 0,
    aaguid C(64),
    label C(128),
    cms_user_id I NOTNULL DEFAULT 0,
    mams_user_id I NOTNULL DEFAULT 0,
    created I NOTNULL
';
$sql = $dict->CreateTableSQL(Mas_Ah_Tables::webauthnCredentials(), $flds, $tabopt);
$dict->ExecuteSQLArray($sql);

$flds = '
    id I KEY NOTNULL,
    created I NOTNULL,
    event C(128) NOTNULL,
    severity C(16) NOTNULL,
    user_id I NOTNULL DEFAULT 0,
    provider_id I NOTNULL DEFAULT 0,
    ip_hash C(64),
    context_json X2
';
$sql = $dict->CreateTableSQL(Mas_Ah_Tables::auditLog(), $flds, $tabopt);
$dict->ExecuteSQLArray($sql);

$this->CreatePermission('Manage MAS_AuthHub', 'Configure MAS AuthHub identity providers, OAuth server, and policies');
$this->CreatePermission('Use MAS_AuthHub', 'Use MAS AuthHub SSO login');

$this->SetPreference('mas_ah_admin_section', 'extensions');
$this->SetPreference('mas_ah_enable_admin_sso', '1');
$this->SetPreference('mas_ah_enable_mams_sso', '1');
$this->SetPreference('mas_ah_auto_link_email', '1');
$this->SetPreference('mas_ah_session_ttl', '3600');
$this->SetPreference('mas_ah_rate_per_hour', '60');
$this->SetPreference('mas_ah_mams_jit_create', '1');
$this->SetPreference('mas_ah_allow_local_signup_when_sso', '1');
$this->SetPreference('mas_ah_block_duplicate_email_signup', '0');
$this->SetPreference('hidedonationstab', '');
$this->SetPreference('mas_ah_default_connecting_property', 'authhub_sub');

$this->RegisterEvents();
Mas_Ah_MamsEvents::register($this);
Mas_Ah_MamsRegistration::register($this);

$this->AddEventHandler('Core', 'LoginFailed', false);
$this->AddEventHandler('Core', 'LoginPost', false);
$this->AddEventHandler('Core', 'LogoutPost', false);

require_once dirname(__DIR__) . '/MAS_Common/lib/mas_admin_ui.php';
Mas_Admin_Ui::ensureIconGif($this);
Mas_Admin_Ui::ensureBanner($this);

$this->Audit(0, $this->Lang('friendlyname'), $this->Lang('installed', $this->GetVersion()));
