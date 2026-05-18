<h3>{$mod->Lang('tab_audit')|escape:'html'}</h3>
<p><a href="{$export_url|escape:'html'}">{$mod->Lang('audit_export')|escape:'html'}</a></p>
<table class="pagetable" style="width:100%;"><thead><tr><th>ID</th><th>Time</th><th>Event</th><th>Severity</th><th>User</th></tr></thead>
<tbody>{foreach from=$rows item=r}
<tr><td>{$r.id}</td><td>{$r.created}</td><td>{$r.event|escape:'html'}</td><td>{$r.severity|escape:'html'}</td><td>{$r.user_id}</td></tr>
{/foreach}</tbody></table>
