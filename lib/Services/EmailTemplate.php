<?php

namespace KirbyEmailManager\Services;

use Kirby\Content\Content;
use Kirby\Data\Data;
use Kirby\Exception\Exception;
use KirbyEmailManager\Helpers\LanguageHelper;
use KirbyEmailManager\Helpers\FormHelper;
use Kirby\Content\Field;
use Kirby\Cms\Page;

/**
 * EmailTemplate class for managing email templates
 * 
 * This class provides methods to manage email templates.
 * 
 * @author Philipp Oehrlein
 * @version 1.0.0
 */
class EmailTemplate
{
    protected $config;
    protected $formData;
    protected $languageCode;
    protected $languageHelper;
    protected $templateId;
    protected $footerContent;
    protected Content $content;
    protected Content $form;
    protected Page $page;

    /**
     * Constructs a new EmailTemplate instance.
     * 
     * @param Page $page The page instance.
     * @param array $formData The form data.
     * @param string $footerContent The footer content.
     * @param string $templateId The template ID.
     * @param array $templateConfig The template configuration.
     */
    public function __construct($page, array $formData, $footerContent, string $templateId, array $templateConfig)
    {
        
        $this->languageHelper = new LanguageHelper(null, $templateConfig);
        $this->page = $page;
        $this->formData = $formData;
        $this->footerContent = $footerContent;
        $this->templateId = $templateId;
        $this->languageCode = $this->languageHelper->getLanguage();
        
        $this->loadConfig();
        
        $data = $this->prepareContentData();
        $this->content = new Content($data['content']);
        $this->form = new Content($data['form']);
    }

    /**
     * Returns the content.
     * 
     * @return Content The content.
     */
    public function content(): Content
    {
        return $this->content;
    }

    /**
     * Returns the form.
     * 
     * @return Content The form.
     */
    public function form(): Content
    {
        return $this->form;
    }

    /**
     * Returns the value of a config key.
     * 
     * @param string $key The key.
     * @return mixed The value.
     */
    protected function getConfigValue(string $key)
    {
        return $this->languageHelper->get($key);
    }

    /**
     * Creates a field.
     * 
     * @param string $key The key.
     * @param mixed $value The value.
     * @return Field The field.
     */
    protected function createField(string $key, $value = ''): Field
    {
        return new Field($this->page, $key, $value);
    }

    /**
     * Prepares the content data.
     * 
     * @return array The content data.
     */
    protected function prepareContentData(): array
    {
        $contentData = [];
        $formData = [];

        if (isset($this->config['emails']['content'])) {
            foreach ($this->config['emails']['content'] as $key => $value) {
                $contentData[$key] = $this->createField($key, $this->languageHelper->get('emails.content.' . $key));
            }
        }

        if ($this->footerContent) {
            $contentData['footer'] = $this->createField('footer', $this->footerContent);
        }

        foreach ($this->formData as $key => $value) {
            if (in_array($key, ['timestamp', 'csrf', 'submit', 'gdpr', 'website_hp_'], true)) {
                continue;
            }

            // Handle select fields - get display value instead of key
            if (is_string($value) && isset($this->config['fields'][$key]['type']) && $this->config['fields'][$key]['type'] === 'select') {
                $value = FormHelper::getSelectDisplayValue($key, $value, $this->config, $this->languageHelper);
            }

            // Decode any HTML entities that may have been introduced during sanitization,
            // so emails render natural characters like apostrophes instead of entities
            if (is_array($value)) {
                $decodedValues = array_map(function ($item) {
                    if (!is_string($item)) {
                        return $item;
                    }
                    $decoded = html_entity_decode($item, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    return strip_tags($decoded);
                }, array_filter($value));
                $valueForField = implode(', ', $decodedValues);
            } else {
                $valueForField = is_string($value)
                    ? strip_tags(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'))
                    : $value;
            }

            $formData[$key] = $this->createField(
                $key,
                $valueForField
            );
        }

        return [
            'content' => $contentData,
            'form' => $formData,
        ];
    }

    /**
     * Loads the config.
     */
    protected function loadConfig(): void
    {
        $configPath = kirby()->root('blueprints') . '/emails/' . $this->templateId . '.yml';
        if (!file_exists($configPath)) {
            throw new Exception(t('error.config_file_not_found') . $configPath);
        }
        $this->config = Data::read($configPath);
    }

    /**
     * Adds a subject to the content.
     * 
     * @param string $subject The subject.
     */
    public function addSubject(string $subject): void
    {
        $this->content()->subject()->value($subject);
    }
}