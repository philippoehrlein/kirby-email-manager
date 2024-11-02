<?php

namespace KirbyEmailManager;

use Kirby\Content\Content;
use Kirby\Data\Data;
use Kirby\Exception\Exception;
use KirbyEmailManager\Helpers\LanguageHelper;
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
     */
    public function __construct($page, array $formData, $footerContent, string $templateId)
    {
        $this->page = $page;
        $this->formData = $formData;
        $this->footerContent = $footerContent;
        $this->templateId = $templateId;
        $this->languageCode = LanguageHelper::getCurrentLanguageCode();
        
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
     * @param mixed $default The default value.
     * @return mixed The value.
     */
    protected function getConfigValue(string $key, $default = null)
    {
        return LanguageHelper::getTranslatedValue(
            $this->config['emails']['content'][$key] ?? [],
            $this->languageCode,
            $default
        );
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
            foreach ($this->config['emails']['content'] as $key => $translations) {
                $contentData[$key] = $this->createField($key, $this->getConfigValue($key), $this->page);
            }
        }

        if ($this->footerContent) {
            $contentData['footer'] = $this->createField('footer', $this->footerContent, $this->page);
        }

        foreach ($this->formData as $key => $value) {
            if (in_array($key, ['timestamp', 'csrf', 'submit', 'gdpr'], true)) {
                continue;
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

    /**
     * Loads the config.
     */
    protected function loadConfig(): void
    {
        $configPath = kirby()->root('site') . '/templates/emails/' . $this->templateId . '/config.yml';
        if (!file_exists($configPath)) {
            throw new Exception(t('error_messages.config_file_not_found') . $configPath);
        }
        $this->config = Data::read($configPath);
    }
}