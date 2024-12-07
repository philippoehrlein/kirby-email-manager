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
        $blueprintDir = kirby()->root('blueprints') . '/emails';

        if (!Dir::exists($blueprintDir)) {
            return [];
        }

        $templates = [];
        $files = Dir::files($blueprintDir);

        foreach ($files as $file) {
            if (str_ends_with($file, '.yml')) {
                $templateId = basename($file, '.yml');
                $blueprintPath = $blueprintDir . '/' . $file;
                $config = Data::read($blueprintPath);

                if (empty($config)) {
                    continue;
                }

                if (isset($config['type']) && $config['type'] === 'managed-template') {
                    $templateName = self::getTemplateName($config, $templateId);

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
     * Retrieves the template name based on the configuration.
     */
    public static function getTemplateName($config, $templateId) 
    {
        $language = kirby()->languageCode();
        $languageHelper = new LanguageHelper($language, $config);
        
        return $languageHelper->get('name', ['folder' => ucfirst($templateId)]);
    }
}