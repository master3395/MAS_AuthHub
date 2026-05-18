<h3>{$mod->Lang('about_tab_changelog')|escape:'html'}</h3>

{if $mas_ah_changelog_html|default:'' != ''}
  {$mas_ah_changelog_html nofilter}
{else}
  <p>{$mod->Lang('changelog')|escape:'html'}</p>
{/if}
