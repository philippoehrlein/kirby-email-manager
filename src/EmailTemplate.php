<?php

namespace KirbyEmailManager;

use Kirby\Data\Data;
use Kirby\Exception\Exception;
use KirbyEmailManager\Helpers\LanguageHelper;

class EmailTemplate
{
    protected $id;
    protected $config;
    protected $formData;
    protected $languageCode;
    protected $type;

    public function __construct(string $id, array $formData, string $type = 'confirmation')
    {
        $this->id = $id;
        $this->formData = $formData;
        $this->type = $type;
        $this->languageCode = LanguageHelper::getCurrentLanguageCode();
        $this->loadConfig();
    }

    protected function loadConfig(): void
    {
        $configPath = kirby()->root('site') . '/templates/emails/' . $this->id . '/config.yml';
        
        if (!file_exists($configPath)) {
            throw new Exception(t('error_messages.config_file_not_found') . $configPath);
        }

        $this->config = Data::read($configPath);
        
        if (empty($this->config)) {
            throw new Exception(t('error_messages.template_config_empty'));
        }
    }

    public function path(string $format = 'html'): string 
    {
        return kirby()->root('site') . '/templates/emails/' . $this->id . '/' . $this->type . '/' . $this->type . '.' . $format . '.php';
    }

    public function render(string $format = 'html'): string 
    {
        $templatePath = $this->path($format);
        
        if (!file_exists($templatePath)) {
            throw new Exception(t('error_messages.template_not_found') . $templatePath);
        }

        $kirby = kirby();
        $formData = $this->formData;
        $config = $this->config;
        
        ob_start();
        require $templatePath;
        return ob_get_clean();
    }
}
