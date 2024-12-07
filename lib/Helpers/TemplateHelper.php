<?php

namespace KirbyEmailManager\Helpers;

use Kirby\Data\Data;
use Kirby\Filesystem\Dir;
use KirbyEmailManager\Helpers\LanguageHelper;

/**
 * TemplateHelper class for managing email templates
 * 
 * This class provides methods to retrieve email templates.
 * 
 * @version 1.0.0
 */
class TemplateHelper
{
    /**
     * Retrieves the email templates.
     * 
     * @return array The email templates.
     */
    public static function getEmailTemplates(): array
    {
        $templateDir = kirby()->root('site') . '/templates/emails';

        if (!Dir::exists($templateDir)) {
            return [];
        }

        $templates = [];
        $folders = Dir::read($templateDir);

        foreach ($folders as $folder) {
            $folderPath = $templateDir . '/' . $folder;
            $configFile = $folderPath . '/config.yml';
            
            if (is_dir($folderPath) && file_exists($configFile)) {
                $config = Data::read($configFile);

                if (empty($config)) {
                    continue;
                }

                if (isset($config['type']) && $config['type'] === 'managed-template') {
                    $templateId = $config['id'] ?? $folder;
                    $templateName = self::getTemplateName($config, $folder);

                    $templates[$templateId] = [
                        'id' => $templateId,
                        'name' => $templateName,
                        'source' => 'site'
                    ];
                }
            }
        }

        return $templates;
    }

    /**
     * Retrieves the template name based on the configuration and folder.
     * 
     * @param array $config The configuration array.
     * @param string $folder The folder name.
     * @return string The template name.
     */
    public static function getTemplateName($config, $folder) 
    {
        // Setzt die aktuelle Sprache
        $language = kirby()->languageCode();
        
        // Erstellt eine Instanz von LanguageHelper und übergibt die Template-Config
        $languageHelper = new LanguageHelper($language, $config);

        // Holt den Namen aus der Konfiguration oder nutzt den Fallback
        return $languageHelper->get('name', ['folder' => ucfirst($folder)]);
    }
}