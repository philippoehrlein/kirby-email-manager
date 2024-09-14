<?php
use KirbyEmailManager\Helpers\EmailHelper;
use KirbyEmailManager\Helpers\ExceptionHelper;
use KirbyEmailManager\Helpers\ValidationHelper;
use KirbyEmailManager\Hooks\SystemHooks;

// Konstanten definieren
define('PLUGIN_DIR', __DIR__);
define('SRC_DIR', PLUGIN_DIR . '/src');
define('SNIPPETS_DIR', PLUGIN_DIR . '/snippets');

// Autoloader für Klassen im src-Verzeichnis
spl_autoload_register(function ($class) {
    $prefix = 'KirbyEmailManager\\';
    $base_dir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

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

    'hooks' => [
        'system.loadPlugins:after' => function () {
            SystemHooks::loadPluginsAfter(); 
        }
    ],

    'translations' => [
        'en' => require PLUGIN_DIR . '/translations/en.php',
        'de' => require PLUGIN_DIR . '/translations/de.php'
    ]
]);