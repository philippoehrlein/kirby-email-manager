<?php
require_once __DIR__ . '/config/classloader.php';

use KirbyEmailManager\Helpers\PathHelper;
use KirbyEmailManager\Helpers\TranslationHelper;
use Kirby\Cms\App as Kirby;


Kirby::plugin('philippoehrlein/kirby-email-manager', [
    'blueprints' => require PathHelper::configDir() . 'blueprints.php',
    'pageMethods' => require PathHelper::configDir() . 'pageMethods.php',
    'snippets' => require PathHelper::configDir() . 'snippets.php',
    'options' => require PathHelper::configDir() . 'main.php',
    'translations' => TranslationHelper::loadTranslations(PathHelper::translationDir()),
    'hooks' => require PathHelper::configDir() . 'hooks.php',
    'options' => [
        'cache.ip' => true
    ],
    'version' => '0.9.1-beta'
]);
