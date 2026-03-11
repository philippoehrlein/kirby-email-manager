<?php
namespace KirbyEmailManager\Helpers;

/**
 * PathHelper class for managing path-related functions
 * 
 * This class provides methods to get the plugin directory and other related paths.
 * 
 * @author Philipp Oehrlein
 * @version 1.0.0
 */
class PathHelper
{
    /**
     * Retrieves the plugin directory.
     * 
     * @return string The plugin directory path.
     */
    public static function pluginDir(): string
    {
        return __DIR__ . '/../../';
    }

    /**
     * Retrieves the blueprint directory.
     * 
     * @return string The blueprint directory path.
     */
    public static function blueprintDir(): string
    {
        return self::pluginDir() . 'blueprints/';
    }

    /**
     * Retrieves the config directory.
     * 
     * @return string The config directory path.
     */
    public static function configDir(): string
    {
        return self::pluginDir() . 'config/';
    }

    /**
     * Retrieves the translation directory.
     * 
     * @return string The translation directory path.
     */
    public static function translationDir(): string
    {
        return self::pluginDir() . 'translations/';
    }

    /**
     * Retrieves the snippet directory.
     * 
     * @return string The snippet directory path.
     */
    public static function snippetDir(): string
    {
        return self::pluginDir() . 'snippets/';
    }

    /**
     * Retrieves the template directory.
     * 
     * @return string The template directory path.
     */
    public static function templateDir(): string
    {
        return self::pluginDir() . 'templates/';
    }

    /**
     * Retrieves the controller directory.
     * 
     * @return string The controller directory path.
     */
    public static function controllerDir(): string
    {
        return self::pluginDir() . 'controllers/';
    }

    /**
     * Retrieves the collection directory.
     * 
     * @return string The collection directory path.
     */
    public static function collectionDir(): string
    {
        return self::pluginDir() . 'collections/';
    }
}