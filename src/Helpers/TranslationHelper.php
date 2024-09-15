<?php

namespace KirbyEmailManager\Helpers;

class TranslationHelper
{
    public static function loadTranslations(string $translationsDir): array
    {
        $translationFiles = glob($translationsDir . '/*.php');
        $translations = [];

        foreach ($translationFiles as $file) {
            $languageCode = basename($file, '.php'); 
            $translations[$languageCode] = require $file;
        }

        return $translations;
    }
}