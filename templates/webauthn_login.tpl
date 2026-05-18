<div class="mas-authhub-webauthn">
<p>Passkey login (WebAuthn). Registration is available from AuthHub admin when logged in.</p>
<p id="mas-ah-wa-status"></p>
<button type="button" id="mas-ah-wa-login">Use passkey</button>
</motion>
<script src="{$mod->GetModuleURLPath()|cat:'/templates/webauthn_login.js'|escape:'html'}"></script>
<script>
window.MAS_AUTHHUB_WEBAUTHN = {
  postUrl: {$mas_ah_webauthn_post|@json_encode},
  returnType: {$mas_ah_return_type|@json_encode}
};
</script>
