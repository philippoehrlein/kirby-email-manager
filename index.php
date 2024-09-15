<?php
require_once __DIR__ . '/vendor/autoload.php';

use KirbyEmailManager\Helpers\EmailHelper;
use KirbyEmailManager\Helpers\ExceptionHelper;
use KirbyEmailManager\Helpers\PathHelper;
use KirbyEmailManager\Helpers\SessionHelper;
use KirbyEmailManager\Helpers\TemplateHelper;
use KirbyEmailManager\Helpers\TranslationHelper;
use KirbyEmailManager\Helpers\ValidationHelper;
use KirbyEmailManager\Hooks\SystemHooks;
use KirbyEmailManager\PageMethods\FormHandler;

Kirby::plugin('philippoehrlein/kirby-email-manager', [
    'blueprints' => [
        'email/manager' => PathHelper::blueprintDir() . 'email-manager.yml',
    ],

    'pageMethods' => [
        'form_handler' => function() {    
            $handler = new \KirbyEmailManager\PageMethods\FormHandler(kirby(), $this);
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