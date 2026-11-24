# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [2.0.0] - 2026-11-24

### Changed
- **Kirby 6 only** – Requires Kirby 6 and PHP 8.3+. Kirby 4/5 remain on the `1.x` line (`support/kirby4-5`).
- **Panel fields** – Custom Panel sections migrated to Kirby 6 `DisplayField` PHP classes (`email-manager-license`, `email-manager-local-forms`).
- **Panel frontend** – Built with kirbyup 4 / kirbyuse 2 / Vue 3 for the Kirby 6 Panel.
- **License UI** – License activation is a display field (page + site Email Manager tabs); hidden once activated so it no longer reserves layout space.
- **Trusted HTML** – License activation dialog marks help/info markup as trusted HTML (`HtmlString`) for Kirby 6.
- **Options queries** – Blueprint options use Kirby 6 nested `type: query` syntax.
- **Content updates** – Subject and related content writes use `Content::update()` (Kirby 6 immutable `Field::value()`).
- **Themes** – Block status icons and info colors respect Panel light/dark via `data-theme` / `light-dark`.

### Fixed
- Default mail/reply templates include the legal footer again.
- Reply sending errors are rethrown instead of being swallowed.

## [1.2.0] - 2026-03-11

### Added
- **Global legal settings** – Global panel section to manage default legal information for all forms.
- **Per-form legal overrides** – Toggle to use either global or custom legal information for each form (`kem_email_legal_form` + `legal-fields`).
- **Global spam / blacklist settings** – Global spam section with explanatory notice and configurable blacklist combining site-level entries and plugin options.
- **Local forms overview** – New field `email-manager-local-forms` listing all forms that override the global legal settings.

### Changed
- **Legal / GDPR handling** – Centralized logic in `LegalHelper` for the GDPR checkbox and email footer, taking both global and local configuration into account.

## [1.1.0] - 2026-02-10

### Added
- **Default email templates** – Fallback templates (mail + reply) when no custom templates exist in `site/templates/emails/`
- **Plugin snippets** – `emails/data-html` and `emails/data-text` for form data output; callable via `snippet('emails/data-html', ['form' => $form])`
- **Unit tests** – Tests for `EmailHelper::getTemplates()`

### Changed
- **Email compatibility** – Default templates use inline CSS and wrapper div (better support in Outlook, Gmail, etc.)

## [1.0.0] - 2025-11-06

### Initial Release
- Quick form setup via blueprints
- Customizable email templates
- CSRF protection and validation
- Spam filters (honeypot, rate limiting, timer)
- Multi-recipient routing
- Panel integration (blocks, tabs)
- Multiple language support (DE, EN, ES, FR, IT)
- Webhook support
- File upload validation
- Custom CAPTCHA integration
