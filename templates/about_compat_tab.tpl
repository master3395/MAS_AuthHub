<h3>{$mod->Lang('about_tab_compat')|escape:'html'}</h3>

<p><strong>{$mod->Lang('about_min_cms')|escape:'html'}</strong> {$mod->MinimumCMSVersion()|escape:'html'}</p>
<p><strong>{$mod->Lang('about_min_php')|escape:'html'}</strong> {$mod->GetMinimumPHPVersion()|escape:'html'}</p>

<h4>{$mod->Lang('about_dependencies_heading')|escape:'html'}</h4>
<ul>
  <li><strong>CMSMSExt</strong> &gt;= 1.0</li>
  <li><strong>MAMS</strong> &gt;= 1.0</li>
</ul>
{if $mas_ah_have_mams_registration|default:'0' == '1'}
<p class="information">{$mod->Lang('about_mams_registration_note')|escape:'html'}</p>
{/if}
