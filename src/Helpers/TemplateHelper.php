<?php

namespace KirbyEmailManager\Helpers;

use Kirby\Data\Data;
use Kirby\Filesystem\Dir;
use KirbyEmailManager\Helpers\LanguageHelper;

class TemplateHelper
{
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

    public static function getTemplateName($config, $folder) 
    {
        if (!isset($config['name'])) {
            return ucfirst($folder) . ' Template';
        }

        return LanguageHelper::getTranslatedValue(
            $config,
            'name',
            ucfirst($folder) . ' Template'
        );
    }
}