<?php

namespace KirbyEmailManager\CLI;

use Exception;
use KirbyEmailManager\Helpers\ConfigHelper;
use KirbyEmailManager\Helpers\TemplateHelper;
use KirbyEmailManager\Helpers\LogHelper;
use Kirby\Data\Data;

class TemplateChecker
{
    protected $errors = [];
    protected $warnings = [];

    /**
     * Performs the template check
     * @return array An array containing errors and warnings.
     */
    public function check(): array 
    {
        try {
            $templates = TemplateHelper::getEmailTemplates();
            
            foreach ($templates as $templateId => $template) {
                $this->checkTemplate($templateId);
            }

            return [
                'errors' => $this->errors,
                'warnings' => $this->warnings
            ];
        } catch (Exception $e) {
            LogHelper::logError($e);
            return [
                'errors' => [$e->getMessage()],
                'warnings' => []
            ];
        }
    }

    /**
     * Checks a single template
     * @param string $templateId The ID of the template to check.
     */
    protected function checkTemplate(string $templateId): void
    {
        try {
            $configPath = kirby()->root('blueprints') . '/emails/' . $templateId . '.yml';
            if (!file_exists($configPath)) {
                $this->errors[] = "Template {$templateId}: Configuration file not found: {$configPath}";
                return;
            }

            $config = Data::read($configPath);
            ConfigHelper::validateTemplateConfig($config);

            $this->checkTemplateFiles($templateId);

        } catch (Exception $e) {
            $this->errors[] = "Template {$templateId}: " . $e->getMessage();
        }
    }

    /**
     * Checks the template files
     * @param string $templateId The ID of the template to check.
     */
    protected function checkTemplateFiles(string $templateId): void
    {
        $templateBase = kirby()->root('templates') . '/emails/' . $templateId;

        // Required files
        $requiredFiles = ['mail.text.php'];
        foreach ($requiredFiles as $file) {
            $path = $templateBase . '/' . $file;
            if (!file_exists($path)) {
                $this->errors[] = "Template {$templateId}: Required file missing: {$file}";
            }
        }

        // Optional files
        $optionalFiles = ['mail.html.php', 'reply.text.php', 'reply.html.php'];
        foreach ($optionalFiles as $file) {
            $path = $templateBase . '/' . $file;
            if (!file_exists($path)) {
                $this->warnings[] = "Template {$templateId}: Optional file missing: {$file}";
            }
        }
    }
}
