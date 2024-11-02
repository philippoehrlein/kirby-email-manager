<?php
require_once __DIR__ . '/vendor/autoload.php';

use KirbyEmailManager\Helpers\PathHelper;
use KirbyEmailManager\Helpers\SessionHelper;
use KirbyEmailManager\Helpers\TranslationHelper;
use KirbyEmailManager\Hooks\SystemHooks;

use Kirby\Cms\App as Kirby;

Kirby::plugin('philippoehrlein/kirby-email-manager', [
    'blueprints' => [
        'email-manager' => PathHelper::blueprintDir() . 'tabs/email-manager.yml',
        'email-manager/legal-section' => PathHelper::blueprintDir() . 'sections/legal-section.yml',
        'email-manager/email-section' => PathHelper::blueprintDir() . 'sections/email-section.yml',
        'email-manager/legal-tab' => PathHelper::blueprintDir() . 'tabs/legal-tab.yml',
        'email-manager/email-tab' => PathHelper::blueprintDir() . 'tabs/email-tab.yml',
    ],

    'pageMethods' => [
        'form_handler' => function ($contentWrapper = null) {
            if (!$contentWrapper) {
                $contentWrapper = new \KirbyEmailManager\PageMethods\ContentWrapper($this, null);
            }
            $handler = new \KirbyEmailManager\PageMethods\FormHandler(kirby(), $this, $contentWrapper);
            return $handler->handle();
        },
        'isFormSuccess' => function() {
            return SessionHelper::isFormSuccess();
        },
        'successTitle' => function() {
            return SessionHelper::getSuccessTitle($this);
        },
        'successText' => function() {
            return SessionHelper::getSuccessText($this);
        },
    ],

    'snippets' => require PathHelper::configDir() . 'snippets.php',

    'options' => require PathHelper::configDir() . 'main.php',

    'translations' => TranslationHelper::loadTranslations(PathHelper::translationDir()),
    
    'hooks' => [
        'system.loadPlugins:after' => function () {
            SystemHooks::loadPluginsAfter();
            SystemHooks::extendTranslations();
        },
    ],
]);