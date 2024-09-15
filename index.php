<?php
require_once __DIR__ . '/vendor/autoload.php';

use KirbyEmailManager\Helpers\EmailHelper;
use KirbyEmailManager\Helpers\ExceptionHelper;
use KirbyEmailManager\Helpers\ValidationHelper;
use KirbyEmailManager\Hooks\SystemHooks;

// 
define('PLUGIN_DIR', __DIR__);
define('SRC_DIR', PLUGIN_DIR . '/src');
define('SNIPPETS_DIR', PLUGIN_DIR . '/snippets');


// Plugin-Konfiguration
Kirby::plugin('philippoehrlein/kirby-email-manager', [
    'blueprints' => [
        'email/manager' => PLUGIN_DIR . '/blueprints/email-manager.yml',
    ],

    'pageMethods' => [
        'form_handler' => require SRC_DIR . '/PageMethods/FormHandler.php'
    ],

    'snippets' => require PLUGIN_DIR . '/config/snippets.php',

    'options' => require PLUGIN_DIR . '/config/options.php',

    'translations' => [
        'en' => require PLUGIN_DIR . '/translations/en.php',
        'de' => require PLUGIN_DIR . '/translations/de.php',
        'fr' => require PLUGIN_DIR . '/translations/fr.php',
        'es' => require PLUGIN_DIR . '/translations/es.php',
        'it' => require PLUGIN_DIR . '/translations/it.php'
    ],
    
    'hooks' => [
        'system.loadPlugins:after' => function () {
            SystemHooks::loadPluginsAfter();
            SystemHooks::extendTranslations();
        }
    ],

]);