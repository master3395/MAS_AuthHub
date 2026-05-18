<h3>{$mod->Lang('help_general')|escape:'html'}</h3>

<h4>{$mod->Lang('help_overview_heading')|escape:'html'}</h4>
<p>{$mod->Lang('help_overview_body')|escape:'html'}</p>

<h4>{$mod->Lang('help_quickstart_heading')|escape:'html'}</h4>
<ol>
  <li>{$mod->Lang('help_quickstart_1')|escape:'html'}</li>
  <li>{$mod->Lang('help_quickstart_2')|escape:'html'}</li>
  <li>{$mod->Lang('help_quickstart_3')|escape:'html'}</li>
  <li>{$mod->Lang('help_quickstart_4')|escape:'html'}</li>
  <li>{$mod->Lang('help_quickstart_5')|escape:'html'}</li>
</ol>

<h4>{$mod->Lang('help_requirements_heading')|escape:'html'}</h4>
<ul>
  <li>{$mod->Lang('help_requirements_cms')|escape:'html'} {$mod->MinimumCMSVersion()|escape:'html'}</li>
  <li>{$mod->Lang('help_requirements_php')|escape:'html'} {$mod->GetMinimumPHPVersion()|escape:'html'}</li>
  <li>{$mod->Lang('help_requirements_mams')|escape:'html'}</li>
  <li>{$mod->Lang('help_requirements_https')|escape:'html'}</li>
</ul>

<h4>{$mod->Lang('help_credits_heading')|escape:'html'}</h4>
<p>{$mod->Lang('help_credits_body')|escape:'html'} <a href="{$mod->GetAuthorUrl()|escape:'url'}" target="_blank" rel="noopener noreferrer">{$mod->GetAuthor()|escape:'html'}</a> | <a href="mailto:{$mod->GetAuthorEmail()|escape:'url'}">{$mod->GetAuthorEmail()|escape:'html'}</a></p>
