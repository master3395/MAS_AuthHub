<h3>{$mod->Lang('tab_providers')|escape:'html'}</h3>
<table class="pagetable" style="width:100%;">
<thead><tr><th>ID</th><th>{$mod->Lang('provider_name')|escape:'html'}</th><th>{$mod->Lang('provider_driver')|escape:'html'}</th><th>{$mod->Lang('provider_enabled')|escape:'html'}</th><th></th></tr></thead>
<tbody>
{foreach from=$providers item=p}
<tr>
<td>{$p.id|escape}</td>
<td>{$p.name|escape:'html'}</td>
<td>{$p.driver|escape:'html'}</td>
<td>{if $p.enabled}yes{else}no{/if}</td>
<td><a href="{$p.delete_url|escape:'html'}">{$mod->Lang('provider_delete')|escape:'html'}</a></td>
</tr>
{/foreach}
</tbody>
</table>
<h4>{$mod->Lang('provider_add')|escape:'html'}</h4>
{$formstart}
<p>Name <input type="text" name="{$actionid}name" value=""></p>
<p>Driver <select name="{$actionid}driver">{foreach from=$drivers item=d}<option value="{$d|escape:'html'}">{$d|escape:'html'}</option>{/foreach}</select></p>
<p>Provider key <input type="text" name="{$actionid}provider_key" value=""></p>
<p>Client ID <input type="text" name="{$actionid}client_id" value="" style="width:60%;"></p>
<p>Client secret <input type="password" name="{$actionid}client_secret" value="" autocomplete="new-password"></p>
<p>Authorize URL <input type="text" name="{$actionid}authorize_url" value="" style="width:80%;"></p>
<p>Token URL <input type="text" name="{$actionid}token_url" value="" style="width:80%;"></p>
<p>Userinfo URL <input type="text" name="{$actionid}userinfo_url" value="" style="width:80%;"></p>
<p>IdP SSO URL (SAML) <input type="text" name="{$actionid}idp_sso_url" value="" style="width:80%;"></p>
<p>Enabled <input type="checkbox" name="{$actionid}enabled" value="1" checked></p>
<p><input type="submit" value="{$mod->Lang('submit')|escape:'html'}"></p>
{$formend}
