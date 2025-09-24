<?php

namespace KirbyEmailManager\Helpers;

/**
 * LanguageHelper class provides methods to handle language-related tasks.
 * @author Philipp Oehrlein
 * @version 1.0.0
 */
class LanguageHelper
{
    protected string $defaultLang = 'en';
    protected string $currentLang;
    protected array $templateConfig;
    protected array $cache = [];

    /**
     * Constructs a new LanguageHelper instance.
     *
     * @param string|null $language The language code to use.
     * @param array $templateConfig The template configuration.
     */
    public function __construct(?string $language = null, array $templateConfig = [])
    {
        $this->currentLang = $language ?? kirby()->languageCode() ?? $this->defaultLang;
        $this->templateConfig = $templateConfig;
    }

    /**
     * Retrieves a translated string from the template configuration or the plugin translations.
     *
     * @param string $key The key to translate.
     * @param array $placeholders The placeholders to replace in the translated string.
     * @return string The translated string.
     */
    public function get(string $key, array $placeholders = []): ?string
    {
        $cacheKey = $this->currentLang . '.' . $key;

        if (isset($this->cache[$cacheKey])) {
            return $this->replacePlaceholders($this->cache[$cacheKey], $placeholders);
        }

        // 1. Try Template Config
        $value = $this->getTranslationFromTemplateConfig($key, $cacheKey, $placeholders);
        if ($value !== null) {
            return $this->replacePlaceholders($value, $placeholders);
        }

        // 2. Try Plugin Translations
        $value = $this->getPluginTranslation($key);
        if ($value !== null) {
            return $this->replacePlaceholders($value, $placeholders);
        }
        
        // Fallback: gebe den Key selbst zurück (mit Platzhalterersetzung) und cache ihn
        $this->cache[$cacheKey] = $key;
        return $this->replacePlaceholders($this->cache[$cacheKey], $placeholders);
    }

    protected function getFallbackTranslation(string $key, string $cacheKey, array $placeholders = []): ?string
    {
        $this->cache[$cacheKey] = $key;
        return $this->replacePlaceholders($key, $placeholders);
    }

    /**
     * Retrieves a translated string from the template configuration.
     *
     * @param string $key The key to translate.
     * @param string $cacheKey The cache key.
     * @param array $placeholders The placeholders to replace in the translated string.
     * @return string The translated string.
     */
    public function getTranslationFromTemplateConfig(string $key, ?string $cacheKey = null, ?array $placeholders = []): ?string
    {
        $value = $this->getNestedValue($this->templateConfig, explode('.', $key));
        if ($value === null) {
            return null;
        }

        $string = self::getTranslatedValue($value, $this->currentLang, $this->defaultLang);
        if ($string === null) {
            return null;
        }

        if ($cacheKey !== null) {
            $this->cache[$cacheKey] = $string;
        }

        return $this->replacePlaceholders($string, $placeholders ?? []);
    }

    /**
     * Retrieves a translated string from the plugin translations.
     *
     * @param string $key The key to translate.
     * @param string $cacheKey The cache key.
     * @param array $placeholders The placeholders to replace in the translated string.
     * @return string The translated string.
     */
    protected function getTranslationFromPluginTranslations(string $key, string $cacheKey, array $placeholders = []): ?string
    {
        $translations = $this->getPluginTranslation($key);
        if ($translations !== null) {
            $string = self::getTranslatedValue($translations, $this->currentLang, $this->defaultLang);
            if ($string !== null) {
                $this->cache[$cacheKey] = $string;
                return $this->replacePlaceholders($string, $placeholders);
            }
        }

        return null;
    }

    /**
     * Replaces placeholders in a string with their corresponding values.
     *
     * @param string $string The string to replace placeholders in.
     * @param array $placeholders The placeholders to replace.
     * @return string The string with placeholders replaced.
     */
    protected function replacePlaceholders(string $string, array $placeholders): string
    {
        foreach ($placeholders as $key => $value) {
            $string = str_replace(':' . $key, $value, $string);
        }

        return $string;
    }

    /**
     * Sets the current language.
     *
     * @param string $language The language code to set.
     */
    public function setLanguage(string $language)
    {
        $this->currentLang = $language;
    }

    /**
     * Retrieves the current language code.
     *
     * @return string The current language code.
     */
    public function getLanguage()
    {
        return $this->currentLang;
    }

    protected function getPluginTranslation(string $key, ?string $language = null): ?string 
    {
        $lang = $language ?? $this->currentLang;
        $translationFile = dirname(__DIR__, 2) . "/translations/form/{$lang}.php";
        
        if (!file_exists($translationFile)) {
            return null;
        }

        $translations = require $translationFile;
        return $this->getNestedValue($translations, explode('.', $key));
    }

    protected function getNestedValue(array $array, array $keys): mixed
    {
        $current = $array;

        foreach ($keys as $key) {
            if (!is_array($current) || !isset($current[$key])) {
                return null;
            }
            $current = $current[$key];
        }

        return $current;
    }

    /**
     * Checks and gets the correct value from a string or translated array
     *
     * @param string|array $value The value to check
     * @param string|null $language The desired language
     * @param string $defaultLang The fallback language (default: 'en')
     * @return string|null
     */
    public static function getTranslatedValue(string|array $value, ?string $language = null, string $defaultLang = 'en'): ?string
    {
        // If it's a string, return it directly
        if (is_string($value)) {
            return $value;
        }
        
        // If it's an array, return the corresponding translation
        if (is_array($value)) {
            // If a language is specified
            if ($language && isset($value[$language])) {
                return $value[$language];
            }
            
            // Fallback to default language
            if (isset($value[$defaultLang])) {
                return $value[$defaultLang];
            }
            
            // If no matching translation is found, take the first available value
            return reset($value) ?: null;
        }
        
        return null;
    }
}