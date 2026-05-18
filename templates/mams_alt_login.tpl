<div class="mas-authhub-alt-login" style="margin:1em 0;padding:1em;border:1px solid #ccc;border-radius:6px;">
<p><strong>{$mod->Lang('sso_login_with')|default:'Sign in with'|escape:'html'}:</strong></p>
<ul style="list-style:none;padding:0;">
{foreach from=$mas_ah_providers item=pr}
<li style="margin:0.5em 0;"><a class="mas-authhub-sso-btn" href="{$pr.url|escape:'html'}">{$pr.name|escape:'html'}</a></li>
{/foreach}
</ul>
{if $mas_ah_webauthn_url|default:'' != ''}
<p><a href="{$mas_ah_webauthn_url|escape:'html'}">Passkey login</a></p>
{/if}
</motion>
