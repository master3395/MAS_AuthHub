# MAS_AuthHub

Centralized identity and access management for [CMS Made Simple](https://www.cmsmadesimple.com): OAuth2, OIDC, SAML, WebAuthn, CMSMS admin SSO, and MAMS integration.

## Requirements

- CMS Made Simple 2.2.10+
- PHP 7.4+
- CMSMSExt 1.0+
- MAMS 1.0+

## Configuration

Store secrets in the site root `config.php` under the `mas_authhub` array. See `to-do/config.mas_authhub.example.php` for shape (do not commit real secrets).

## Install

Copy into `modules/MAS_AuthHub/`, run Composer if needed (`composer install` in the module directory), then install via Extensions in the CMSMS admin.

## Author

master3395 | https://newstargeted.com/contact/
