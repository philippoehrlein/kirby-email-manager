<?php

namespace KirbyEmailManager\Helpers;

/**
 * LanguageHelper class for managing language-related functions
 * 
 * This class provides methods to get the current language code.
 * 
 * @author Philipp Oehrlein
 * @version 1.0.0
 */
class LanguageHelper
{
    /**
     * Retrieves the current language code.
     * 
     * @return string The current language code.
     */
    public static function getCurrentLanguageCode()
    {
        $kirby = kirby();
        
        if ($kirby->multilang()) {
            return $kirby->language()->code();
        }
        
        return 'en';
    }

    /**
     * Retrieves the translated value from the configuration.
     * 
     * @param array $config The configuration array.
     * @param string $key The key to retrieve the value for.
     * @param mixed $fallback The fallback value if the key is not found.
     * @return mixed The translated value.
     */
    public static function getTranslatedValue($config, $key, $fallback = null)
    {
        if (!isset($config[$key])) {
            return $fallback;
        }

        if (is_string($config[$key])) {
            return $config[$key];
        }

        if (is_array($config[$key])) {
            $langCode = self::getCurrentLanguageCode();
            
            if (isset($config[$key][$langCode])) {
                return $config[$key][$langCode];
            }
            
            if (isset($config[$key]['en'])) {
                return $config[$key]['en'];
            }
            
            return reset($config[$key]);
        }

        return $fallback;
    }
}