<h3>{$mod->Lang('settings')|escape:'html'}</h3>
{$start_form}
<p><label>{$mod->Lang('enable_admin_sso')|escape:'html'}</label> {$enable_admin_sso}</p>
<p><label>{$mod->Lang('enable_mams_sso')|escape:'html'}</label> {$enable_mams_sso}</p>
<p><label>{$mod->Lang('auto_link_email')|escape:'html'}</label> {$auto_link_email}</p>
<p><label>{$mod->Lang('session_ttl')|escape:'html'}</label> {$session_ttl}</p>
<p><label>{$mod->Lang('rate_per_hour')|escape:'html'}</label> {$rate_per_hour}</p>
<p>{$submit}</p>
{$end_form}
