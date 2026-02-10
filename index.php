<?php
require_once __DIR__ . '/config/classloader.php';

use KirbyEmailManager\Helpers\PathHelper;
use KirbyEmailManager\Helpers\TranslationHelper;
use Kirby\Cms\App as Kirby;
use Kirby\Plugin\Plugin;
use KirbyEmailManager\Helpers\EmailManagerLicense;

$kirbyVersion = Kirby::version();
$kirbyMajorVersion = intval(explode('.', $kirbyVersion)[0]);

$plugin = [
  'api' =>  require PathHelper::configDir() . 'api.php',
  'blueprints' => require PathHelper::configDir() . 'blueprints.php',
  'pageMethods' => require PathHelper::configDir() . 'pageMethods.php',
  'snippets' => require PathHelper::configDir() . 'snippets.php',
  'options' => require PathHelper::configDir() . 'main.php',
  'translations' => TranslationHelper::loadTranslations(PathHelper::translationDir()),
  'hooks' => require PathHelper::configDir() . 'hooks.php',
  'areas' => require PathHelper::configDir() . 'areas.php',
  'sections' => require PathHelper::configDir() . 'sections.php',
  'templates' => require PathHelper::configDir() . 'templates.php',
  'version' => '1.1.0'
];


if($kirbyMajorVersion <= 4) {
	Kirby::plugin('philippoehrlein/kirby-email-manager', [
	...$plugin
	]);
} else {
	Kirby::plugin(
		name: 'philippoehrlein/kirby-email-manager',
		extends: [...$plugin],
		license: fn (Plugin $plugin) => new EmailManagerLicense($plugin),
	);
}
