<?php

namespace KirbyEmailManager\Hooks;

class SystemHooks
{
    public static function loadPluginsAfter()
    {
        kirby()->extend([
            'options' => [
                'philippoehrlein.kirby-email-manager.templates' => option('philippoehrlein.kirby-email-manager.templates')()
            ]
        ]);
    }

    public static function extendTranslations()
    {
        $kirby = kirby();
        $customTranslations = $kirby->option('philippoehrlein.kirby-email-manager.translations', []);
        
        foreach ($customTranslations as $lang => $translations) {
            $kirby->extend([
                'translations' => [
                    $lang => array_merge($kirby->translations($lang), $translations)
                ]
            ]);
        }   
    }
}