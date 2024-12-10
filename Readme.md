# Kirby Email Manager

The **Email Manager** is a powerful plugin for [Kirby CMS](https://getkirby.com/) that simplifies email form creation and management. Designed for developers, it ensures a seamless experience while giving CMS users the flexibility to adapt content dynamically.

>Complete documentation is available on the [Email Manager Documentation](https://email-manager.philippoehrlein.de).


## Features
- **Quick Setup:** Get started in minutes with pre-built blueprints and templates.
- **Kirby Integration:** Built with Kirby’s syntax and conventions for a natural developer experience.
- **Customizable Forms:** Adjust templates, layouts, and CSS to meet your project needs.
- **Security:** Includes CSRF protection, input sanitization, and secure file validation.
- **Extendable:** Add custom CAPTCHAs and integrate webhooks effortlessly.
- **Multi-Language Support:** Pre-configured for EN, DE, FR, IT, and ES.


## Installation

### Composer Installation (Recommended)
Run the following command in your Kirby installation directory:
```bash
composer require philippoehrlein/kirby-email-manager
```

### Manual Installation
1.	Download the latest release from the [GitHub repository](https://github.com/philippoehrlein/kirby-email-manager/releases/latest).
2.	Extract the contents and move the kirby-email-manager folder to /site/plugins/.


## Quick Start
### 1. Configure Kirby for Email Sending

In your Kirby configuration file (`site/config/config.php`), set up the email transport settings. It's recommended to use a noreply email address for sending emails:

```php
return [
    'email' => [
        'noreply' => 'no-reply@yourdomain.com'
        'transport' => [
            'type' => 'smtp',
            'host' => 'smtp.server.com',
            'port' => 465,
            'security' => true,
            'auth' => true,
            'username' => 'SMTP_USERNAME',
            'password' => 'SMTP_PASSWORD'
        ]
    ]
];
```

> ⚠️ Don't store credentials directly in the production configuration. Consider using the [Kirby ENV Plugin](https://github.com/beebmx/kirby-env) to manage sensitive credentials.

### 2. Create the Email Template

#### Blueprint
Create the file `site/blueprints/emails/contact-form.yml`:

```yaml
type: managed-template
name: Contact Form

emails:
  mail:
    subject: Contact Form Submission
    sender: Contact Form

fields:
  name:
    label: Name
    placeholder: Enter your name
    type: text
    required: true
    validate: alpha
    user_name: true
  email:
    label: Email
    placeholder: Enter your email
    type: email
    required: true
    reply_to: true
  message:
    label: Message
    placeholder: Enter your message
    type: textarea
    resizable: vertical
    rows: 6
    required: true
    minlength: 10
```

#### Email Template File
Create `site/templates/emails/contact-form/mail.txt.php`:

```php
From: <?= $form->email() ?>

Name: <?= $form->name() ?>

Message:
<?= $form->message() ?>
```

### 3. Add the Form to a Page

#### Blueprint
Create `site/blueprints/pages/contact.yml`:

```yaml
title: Contact Page

tabs:
  email-manager: email-manager
```

#### Page Template
Create `site/templates/contact.php`:

```php
<?php snippet('email-manager/form-wrapper'); ?>
```

### 4. Create Contact Page

1. In the Kirby panel, create a new page using the **Contact Page** blueprint
2. Select the **Contact Form** template
3. Enter the recipient email address
4. Set up success title and message for form submissions

Your contact form is now ready to use!