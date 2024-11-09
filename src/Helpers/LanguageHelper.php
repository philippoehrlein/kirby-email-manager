<?php

namespace KirbyEmailManager\Helpers;

use Kirby\Toolkit\I18n;

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
    public function get(string $key, array $placeholders = []): string
    {
        $cacheKey = $this->currentLang . '.' . $key;

        if (isset($this->cache[$cacheKey])) {
            return $this->replacePlaceholders($this->cache[$cacheKey], $placeholders);
        }

        // 1. Versuche Template Config in aktueller Sprache
        $string = $this->getFromTemplateConfig($key);
        
        if ($string === null && $this->currentLang !== $this->defaultLang) {
            // 2. Versuche Template Config in Standardsprache
            $string = $this->getFromTemplateConfig($key, $this->defaultLang);
        }

        if ($string === null) {
            // 3. Versuche Plugin Übersetzungen in aktueller Sprache
            $string = $this->getPluginTranslation($key);
        }

        if ($string === null && $this->currentLang !== $this->defaultLang) {
            // 4. Versuche Plugin Übersetzungen in Standardsprache
            $string = $this->getPluginTranslation($key, $this->defaultLang);
        }

        if ($string === null) {
            $string = $key;
        }

        $this->cache[$cacheKey] = $string;

        return $this->replacePlaceholders($string, $placeholders);
    }

    /**
     * Retrieves a value from the template configuration.
     *
     * @param string $key The key to retrieve.
     * @return string|null The value from the template configuration or null if not found.
     */
    protected function getFromTemplateConfig(string $key, ?string $language = null): ?string
    {
        if (empty($this->templateConfig)) {
            return null;
        }

        $value = $this->getNestedValue($this->templateConfig, explode('.', $key));

        if (is_array($value)) {
            $lang = $language ?? $this->currentLang;
            return $value[$lang] ?? null;
        }

        return is_string($value) ? $value : null;
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
        $translationFile = dirname(__DIR__, 2) . "/translations/{$lang}.php";
        
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
}