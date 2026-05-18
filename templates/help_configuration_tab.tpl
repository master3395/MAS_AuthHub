<h3>{$mod->Lang('help_configuration')|escape:'html'}</h3>

<h4>{$mod->Lang('help_config_secrets_heading')|escape:'html'}</h4>
<p>{$mod->Lang('help_config_secrets_body')|escape:'html'}</p>
<ul>
  <li><code>encryption_key</code>: {$mod->Lang('help_config_encryption_key')|escape:'html'}</li>
  <li><code>signing_key</code>: {$mod->Lang('help_config_signing_key')|escape:'html'}</li>
  <li><code>providers</code>: {$mod->Lang('help_config_providers')|escape:'html'}</li>
</ul>

<h4>{$mod->Lang('help_config_settings_heading')|escape:'html'}</h4>
<ul>
  <li><strong>{$mod->Lang('enable_admin_sso')|escape:'html'}</strong>: {$mod->Lang('help_config_admin_sso')|escape:'html'}</li>
  <li><strong>{$mod->Lang('enable_mams_sso')|escape:'html'}</strong>: {$mod->Lang('help_config_mams_sso')|escape:'html'}</li>
  <li><strong>{$mod->Lang('auto_link_email')|escape:'html'}</strong>: {$mod->Lang('help_config_auto_link')|escape:'html'}</li>
  <li><strong>{$mod->Lang('session_ttl')|escape:'html'}</strong>: {$mod->Lang('help_config_session_ttl')|escape:'html'}</li>
  <li><strong>{$mod->Lang('rate_per_hour')|escape:'html'}</strong>: {$mod->Lang('help_config_rate')|escape:'html'}</li>
</ul>

<h4>{$mod->Lang('help_config_tabs_heading')|escape:'html'}</h4>
<ul>
  <li><strong>{$mod->Lang('tab_providers')|escape:'html'}</strong>: {$mod->Lang('help_config_tab_providers')|escape:'html'}</li>
  <li><strong>{$mod->Lang('tab_mams')|escape:'html'}</strong>: {$mod->Lang('help_config_tab_mams')|escape:'html'}</li>
  <li><strong>{$mod->Lang('tab_oauth_clients')|escape:'html'}</strong>: {$mod->Lang('help_config_tab_oauth')|escape:'html'}</li>
  <li><strong>{$mod->Lang('tab_sessions')|escape:'html'}</strong>: {$mod->Lang('help_config_tab_sessions')|escape:'html'}</li>
  <li><strong>{$mod->Lang('tab_audit')|escape:'html'}</strong>: {$mod->Lang('help_config_tab_audit')|escape:'html'}</li>
</ul>
