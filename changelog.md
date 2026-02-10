# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

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

## [1.1.0] - 2026-02-10

### Added
- **Default email templates** – Fallback templates (mail + reply) when no custom templates exist in `site/templates/emails/`
- **Plugin snippets** – `emails/data-html` and `emails/data-text` for form data output; callable via `snippet('emails/data-html', ['form' => $form])`
- **Unit tests** – Tests for `EmailHelper::getTemplates()`

### Changed
- **Email compatibility** – Default templates use inline CSS and wrapper div (better support in Outlook, Gmail, etc.)
