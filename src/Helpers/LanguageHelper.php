<?php

namespace KirbyEmailManager\Helpers;

class LanguageHelper
{
    public static function getCurrentLanguageCode()
    {
        $kirby = kirby();
        
        // Check if Kirby is multilingual
        if ($kirby->multilang()) {
            return $kirby->language()->code();
        }
        
        // Fallback for single-language sites
        return 'en';
    }

    public static function getTranslatedValue($config, $key, $fallback = null)
    {
        if (!isset($config[$key])) {
            return $fallback;
        }

        // If the value is a string, return it directly
        if (is_string($config[$key])) {
            return $config[$key];
        }

        // If the value is an array
        if (is_array($config[$key])) {
            $langCode = self::getCurrentLanguageCode();
            
            // Current language
            if (isset($config[$key][$langCode])) {
                return $config[$key][$langCode];
            }
            
            // Fallback: English
            if (isset($config[$key]['en'])) {
                return $config[$key]['en'];
            }
            
            // Last fallback: First available value
            return reset($config[$key]);
        }

        return $fallback;
    }
}