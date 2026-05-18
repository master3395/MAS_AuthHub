# Changelog

## 1.0.5 - 19/05/2026

### Changed

- **Help and About:** Tabbed documentation matching LogWatch style (`templates/help.tpl`, `templates/about.tpl`, and per-tab includes). `GetHelp()` and `GetAbout()` render Smarty templates instead of a single lang string.
- **Admin branding:** Refreshed `images/banner.png` (600x120), `images/icon-192.png`, `images/icon-512.png`, and `images/icon.gif` (32x32) with indigo identity styling and a shield/key motif. Regenerate via `Test/generate_brand_assets.py`.
- **CDN cache:** Module image URLs now append `?v=filemtime` via `Mas_Admin_Ui::versionedAssetUrl()` and OneEleven admin theme module header icon, so Cloudflare no longer serves stale Disqus copies cached under `/modules/MAS_AuthHub/images/`. Hard-refresh admin once after upgrade if needed.

### Fixed

- `webauthn_login.tpl`: invalid closing tag corrected to a proper `div` end tag.

## 1.0.4 - 19/05/2026

### Fixed

- Correct `CreateLink()` / `moduleActionUrl()` argument order (fixes modform foreach warnings, OIDC discovery showing "Array", and broken frontend action URLs).
- Admin export and revoke links built in PHP instead of incorrect Smarty `CreateLink` calls.
- Donations tab: added `donationstext` and `sponsors` language strings.

### Changed

- Replaced Disqus-copied `images/banner.png` and `images/icon.gif` with MAS AuthHub branding (blue identity theme).

## 1.0.3 - 19/05/2026

### Fixed

- Admin defaultadmin: `CreateFormStart()` argument order (`$params` was passed as `$extra`, causing modform warnings).
- `Mas_Ah_Config::moduleActionUrl()` treats `GetDefaultContent()` as page ID (int), not a content object (fatal on OAuth clients tab).
- Set `$actionid = $id` for included admin tab scripts; OAuth clients form uses CMSMS form helpers.

## 1.0.2 - 19/05/2026

### Fixed

- `DoEvent()` signature uses `&$params` to match `CMSModule::DoEvent()` (Module Manager fatal error).
- Install schema uses `id I KEY NOTNULL` (CMSMS ADODB convention).
- MAMS consumer class loads only from `GetMAMSAuthConsumer()` so module list does not require MAMS interface at bootstrap.

## 1.0.1 - 19/05/2026

### Fixed

- Renamed public static `audit()` to `logAudit()` so the module class does not conflict with final `CMSModule::audit()` (fatal error in Module Manager).

## 1.0.0 - 19/05/2026

### Added

- MAS_AuthHub CMSMS module: OAuth2/OIDC client, OAuth2/OIDC authorization server, SAML 2.0 SP, WebAuthn passkeys.
- CMSMS admin SSO and MAMS `GetMAMSAuthConsumer()` integration without modifying third-party MAMS modules.
- Hybrid secrets via site `config.php` `mas_authhub` block and encrypted module preferences.
- Audit log, session and token management, user identity links (CMS + MAMS).
- MAMSRegistration hook listeners (beforeNewUser, onNewUser, onUserRegistered) from AuthHub only.

### Operator notes

- Set MAMS Preferences, Authentication Module, to `MAS_AuthHub` for alt login on MAMS forms.
- Add `mas_authhub` encryption and provider secrets to `config.php` before production use.
- Run `composer install` in the module directory for SAML and JWT libraries.
