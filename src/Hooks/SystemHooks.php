<?php
namespace KirbyEmailManager\Hooks;

use KirbyEmailManager\Helpers\TemplateHelper;

class SystemHooks
{
    public static function loadPluginsAfter()
    {
        kirby()->extend([
            'options' => [
                'philippoehrlein.kirby-email-manager.templates' => \KirbyEmailManager\Helpers\TemplateHelper::getEmailTemplates(),
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