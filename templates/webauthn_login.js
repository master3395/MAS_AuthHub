(function () {
  'use strict';
  var cfg = window.MAS_AUTHHUB_WEBAUTHN || {};
  var btn = document.getElementById('mas-ah-wa-login');
  var status = document.getElementById('mas-ah-wa-status');
  if (!btn || !cfg.postUrl) {
    return;
  }
  btn.addEventListener('click', function () {
    if (status) {
      status.textContent = 'Passkey login requires a registered credential. Use OAuth SSO or register a passkey from admin.';
    }
  });
})();
