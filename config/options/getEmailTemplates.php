<?php
use Kirby\Data\Data;

return function() {
    $templateDir = kirby()->root('site') . '/templates/emails';

    $templates = [];
    if (is_dir($templateDir)) {
        foreach (scandir($templateDir) as $folder) {
            $folderPath = $templateDir . '/' . $folder;
            $configFile = $folderPath . '/config.yml';
            
            if ($folder !== '.' && $folder !== '..' && is_dir($folderPath) && file_exists($configFile)) {
                $config = Data::read($configFile);
                
                if (isset($config['type']) && $config['type'] === 'managed-template') {
                    $templateName = $folder;
                    $templateId = $config['id'] ?? $folder;
                    
                    if (isset($config['name'])) {
                        $currentLang = kirby()->language()->code();
                        if (is_array($config['name'])) {
                            $templateName = $config['name'][$currentLang] ?? $config['name']['en'] ?? reset($config['name']);
                        } elseif (is_string($config['name'])) {
                            $templateName = $config['name'];
                        }
                    } else {
                        $templateName = ucfirst($folder) . ' Template';
                    }
                    
                    $templates[$templateId] = [
                        'id' => $templateId,
                        'name' => $templateName,
                        'source' => 'site'
                    ];
                }
            }
        }
    }
    
    return $templates;
};