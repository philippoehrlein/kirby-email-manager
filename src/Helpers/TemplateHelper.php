<?php

namespace KirbyEmailManager\Helpers;

use Kirby\Data\Data;
use Kirby\Filesystem\Dir;

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

    private static function getTemplateName(array $config, string $folder): string
    {
        if (isset($config['name'])) {
            $currentLang = kirby()->language()->code();
            if (is_array($config['name'])) {
                return $config['name'][$currentLang] ?? $config['name']['en'] ?? reset($config['name']);
            } elseif (is_string($config['name'])) {
                return $config['name'];
            }
        }

        return ucfirst($folder) . ' Template';
    }
}