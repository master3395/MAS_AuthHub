<h3>{$mod->Lang('help_protocols')|escape:'html'}</h3>

<h4>OAuth2 / OIDC</h4>
<p>{$mod->Lang('help_protocols_oauth_body')|escape:'html'}</p>
<ul>
  <li><code>oauth_start</code>, <code>oauth_callback</code></li>
  <li><code>oauth_authorize</code>, <code>oauth_token</code> ({$mod->Lang('help_protocols_oauth_server')|escape:'html'})</li>
  <li><code>oidc_discovery</code> ({$mod->Lang('help_protocols_oidc_discovery')|escape:'html'})</li>
</ul>

<h4>SAML</h4>
<p>{$mod->Lang('help_protocols_saml_body')|escape:'html'}</p>
<ul>
  <li><code>saml_acs</code>: {$mod->Lang('help_protocols_saml_acs')|escape:'html'}</li>
  <li><code>saml_metadata</code>: {$mod->Lang('help_protocols_saml_metadata')|escape:'html'}</li>
</ul>

<h4>WebAuthn</h4>
<p>{$mod->Lang('help_protocols_webauthn_body')|escape:'html'}</p>
<ul>
  <li><code>webauthn</code>: {$mod->Lang('help_protocols_webauthn_action')|escape:'html'}</li>
</ul>

<h4>MAMS</h4>
<p>{$mod->Lang('help_protocols_mams_body')|escape:'html'}</p>
<ul>
  <li><code>mams_login</code>: {$mod->Lang('help_protocols_mams_login')|escape:'html'}</li>
</ul>
{if $mas_ah_have_mams_registration|default:'0' == '1'}
<p class="information">{$mod->Lang('help_protocols_mams_reg')|escape:'html'}</p>
{/if}
