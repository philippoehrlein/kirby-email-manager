<?php
namespace KirbyEmailManager\Helpers;

class PathHelper
{
    public static function pluginDir(): string
    {
        return __DIR__ . '/../../';
    }

    public static function blueprintDir(): string
    {
        return self::pluginDir() . 'blueprints/';
    }

    public static function configDir(): string
    {
        return self::pluginDir() . 'config/';
    }

    public static function translationDir(): string
    {
        return self::pluginDir() . 'translations/';
    }

    public static function snippetDir(): string
    {
        return self::pluginDir() . 'snippets/';
    }
}