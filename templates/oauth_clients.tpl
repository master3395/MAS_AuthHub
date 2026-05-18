<h3>{$mod->Lang('tab_oauth_clients')|escape:'html'}</h3>
<p>OIDC discovery: <code>{$discovery_url|escape:'html'}</code></p>
<table class="pagetable"><thead><tr><th>ID</th><th>{$mod->Lang('client_id')|escape:'html'}</th><th>Name</th></tr></thead>
<tbody>{foreach from=$clients item=c}<tr><td>{$c.id}</td><td>{$c.client_id|escape:'html'}</td><td>{$c.name|escape:'html'}</td></tr>{/foreach}</tbody></table>
<h4>{$mod->Lang('oauth_client_add')|escape:'html'}</h4>
{$formstart}
<p>Name {$oauth_client_name}</p>
<p>Client ID {$oauth_client_id}</p>
<p>Secret {$oauth_client_secret}</p>
<p>Redirect URIs {$redirect_uris}</p>
<p>Scopes {$oauth_scopes}</p>
<p>{$submit_client}</p>
{$formend}
