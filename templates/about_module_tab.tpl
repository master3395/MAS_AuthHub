<h3>{$mod->Lang('about_tab_module')|escape:'html'}</h3>

<p><strong>{$mod->Lang('about_label_name')|escape:'html'}</strong> {$mod->GetFriendlyName()|escape:'html'}</p>
<p><strong>{$mod->Lang('about_label_version')|escape:'html'}</strong> {$mod->GetVersion()|escape:'html'}</p>
<p><strong>{$mod->Lang('about_label_author')|escape:'html'}</strong> {$mod->GetAuthor()|escape:'html'}</p>
<p><strong>{$mod->Lang('about_label_email')|escape:'html'}</strong> <a href="mailto:{$mod->GetAuthorEmail()|escape:'url'}">{$mod->GetAuthorEmail()|escape:'html'}</a></p>
<p><strong>{$mod->Lang('about_label_url')|escape:'html'}</strong> <a href="{$mod->GetAuthorUrl()|escape:'url'}" target="_blank" rel="noopener noreferrer">{$mod->GetAuthorUrl()|escape:'html'}</a></p>
<p><strong>{$mod->Lang('about_label_license')|escape:'html'}</strong> {$mod->Lang('about_license_value')|escape:'html'}</p>
