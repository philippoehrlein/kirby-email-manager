<?php
namespace KirbyEmailManager\Hooks;

/**
 * SystemHooks class for managing system hooks
 * 
 * This class provides methods to load plugins after the Kirby instance is created.
 * 
 * @author Philipp Oehrlein
 * @version 1.0.0
 */
class SystemHooks
{
    /**
     * Loads plugins after the Kirby instance is created.
     */
    public static function loadPluginsAfter()
    {
        kirby()->extend([
            'options' => [
                'philippoehrlein.kirby-email-manager.templates' => \KirbyEmailManager\Helpers\TemplateHelper::getEmailTemplates(),
            ]
        ]);
    }

    /**
     * Extends the translations with custom translations.
     */
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