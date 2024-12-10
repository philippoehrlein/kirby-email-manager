<?php

namespace KirbyEmailManager\CLI;

use Exception;
use Kirby\Data\Data;

class TemplateInspector
{
    public function inspect(string $templateId): array
    {
        try {
            $configPath = kirby()->root('blueprints') . '/emails/' . $templateId . '.yml';
            if (!file_exists($configPath)) {
                throw new Exception("Konfigurationsdatei nicht gefunden: {$configPath}");
            }
            
            $config = Data::read($configPath);
            
            return [
                'form' => $this->getFormFields($config),
                'content' => $this->getContentBlocks($config)
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    protected function getFormFields(array $config): array
    {
        if (!isset($config['fields'])) {
            return [];
        }

        $fields = [];
        foreach ($config['fields'] as $fieldName => $field) {
            $fields[$fieldName] = [
                'type' => $field['type'],
                'required' => $field['required'] ?? false
            ];
        }
        return $fields;
    }

    protected function getContentBlocks(array $config): array
    {
        if (!isset($config['emails']['content'])) {
            return [];
        }

        return array_keys($config['emails']['content']);
    }
}