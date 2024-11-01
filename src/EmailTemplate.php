<?php

namespace KirbyEmailManager;

use Kirby\Cms\Content;
use Kirby\Data\Data;
use Kirby\Exception\Exception;
use KirbyEmailManager\Helpers\LanguageHelper;
use Kirby\Content\Field;

class EmailTemplate
{
    protected $config;
    protected $formData;
    protected $languageCode;
    protected $templateId;
    protected Content $content;
    protected Content $form;
    protected $page;

    public function __construct($page, array $formData, string $templateId)
    {
        $this->page = $page;
        $this->formData = $formData;
        $this->templateId = $templateId;
        $this->languageCode = LanguageHelper::getCurrentLanguageCode();
        
        $this->loadConfig();
        
        $data = $this->prepareContentData();
        $this->content = new Content($data['content']);
        $this->form = new Content($data['form']);
    }

    public function content(): Content
    {
        return $this->content;
    }

    public function form(): Content
    {
        return $this->form;
    }

    protected function getConfigValue(string $key, $default = null)
    {
        return LanguageHelper::getTranslatedValue(
            $this->config['emails']['content'][$key] ?? [],
            $this->languageCode,
            $default
        );
    }

    protected function createField(string $key, $value = ''): Field
    {
        // Verwende $this->page als Parent
        return new Field($this->page, $key, $value);
    }

    protected function prepareContentData(): array
    {
        $contentData = [];
        $formData = [];

        // Pflichtfelder aus der Konfiguration
        if (isset($this->config['emails']['content'])) {
            foreach ($this->config['emails']['content'] as $key => $translations) {
                $contentData[$key] = $this->createField($key, $this->getConfigValue($key), $this->page);
            }
        }

        // Optionales Formularfeld, alle `formData`-Keys abdecken
        foreach ($this->formData as $key => $value) {
            // Überprüfung und Filterung der Felder
            if (in_array($key, ['timestamp', 'csrf', 'submit'], true)) {
                continue; // Überspringe interne Felder
            }

            $formData[$key] = $this->createField(
                $key,
                is_array($value) ? implode(', ', array_filter($value)) : $value,
                $this->page
            );
        }

        return [
            'content' => $contentData,
            'form' => $formData,
        ];
    }

    protected function loadConfig(): void
    {
        $configPath = kirby()->root('site') . '/templates/emails/' . $this->templateId . '/config.yml';
        if (!file_exists($configPath)) {
            throw new Exception(t('error_messages.config_file_not_found') . $configPath);
        }
        $this->config = Data::read($configPath);
    }
}