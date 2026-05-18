<h3>{$mod->Lang('tab_sessions')|escape:'html'}</h3>
<h4>Sessions</h4>
<table class="pagetable"><thead><tr><th>Session</th><th>CMS user</th><th>MAMS user</th><th>Expires</th><th></th></tr></thead>
<tbody>{foreach from=$sessions item=s}
<tr>
<td>{$s.session_id|escape:'html'|truncate:16}</td>
<td>{$s.cms_user_id}</td>
<td>{$s.mams_user_id}</td>
<td>{$s.expires_at}</td>
<td><a href="{$s.revoke_url|escape:'html'}">{$mod->Lang('revoke_session')|escape:'html'}</a></td>
</tr>
{/foreach}</tbody></table>
<h4>Tokens</h4>
<table class="pagetable"><thead><tr><th>ID</th><th>Type</th><th>Expires</th><th></th></tr></thead>
<tbody>{foreach from=$tokens item=t}
<tr><td>{$t.id}</td><td>{$t.token_type|escape:'html'}</td><td>{$t.expires_at}</td>
<td><a href="{$t.revoke_url|escape:'html'}">{$mod->Lang('revoke_session')|escape:'html'}</a></td></tr>
{/foreach}</tbody></table>
