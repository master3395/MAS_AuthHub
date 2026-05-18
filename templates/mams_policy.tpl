<h3>{$mod->Lang('tab_mams')|escape:'html'}</h3>
<p><strong>{$mod->Lang('mams_status')|escape:'html'}:</strong> MAMS installed: {$mams_ok|escape:'html'}; MAMSRegistration: {$mams_registration|escape:'html'}</p>
<p><strong>{$mod->Lang('mams_auth_module')|escape:'html'}:</strong> {$mams_auth_module|escape:'html'} {if $authhub_active == 'yes'}(AuthHub active){else}(set to MAS_AuthHub in MAMS admin preferences){/if}</p>
<p>Do not edit MAMS module files. Change authentication module only in MAMS Preferences.</p>
{$formstart}
<p><label>{$mod->Lang('mams_jit_create')|escape:'html'}</label> {$mams_jit_create}</p>
<p><label>{$mod->Lang('block_duplicate_email_signup')|escape:'html'}</label> {$block_duplicate_email_signup}</p>
<p>Default MAMS group IDs (comma-separated) {$mams_default_groups}</p>
<p>Default connecting property {$default_connecting_property}</p>
<p>{$submit}</p>
{$formend}
