<?php
namespace KirbyEmailManager\PageMethods;

use Kirby\Data\Data;
use Kirby\Toolkit\V;
use Kirby\Exception\Exception;
use KirbyEmailManager\Helpers\EmailHelper;
use KirbyEmailManager\Helpers\ExceptionHelper;
use KirbyEmailManager\Helpers\ValidationHelper;
use KirbyEmailManager\Helpers\LogHelper;
use KirbyEmailManager\Helpers\ConfigHelper;
use KirbyEmailManager\Helpers\SuccessMessageHelper;

/**
 * FormHandler class provides methods to handle form submissions.
 * Author: Philip Oehrlein
 * Version: 1.0.0
 */
class FormHandler
{
    protected $kirby;
    protected $page;
    protected $languageCode;
    protected $templateConfig;
    protected $translations;

    /**
     * Constructor for FormHandler.
     *
     * @param \Kirby\Cms\App $kirby The Kirby application instance.
     * @param \Kirby\Cms\Page $page The current page instance.
     */
    public function __construct($kirby, $page)
    {
        $this->kirby = $kirby;
        $this->page = $page;
        $this->languageCode = $kirby->language()->code() ?? 'en';
        
        // Lade das Template-Konfiguration
        $this->loadTemplateConfig();
        
        // Lade die Übersetzungen
        $this->loadTranslations();
    }
    
    /**
     * Loads the template configuration.
     *
     * @throws \Kirby\Exception\Exception If the template configuration is not found.
     */
    protected function loadTemplateConfig()
    {
        $selectedTemplateId = $this->page->email_templates()->value();
        $templates = $this->kirby->option('philippoehrlein.kirby-email-manager.templates');
        $this->templateConfig = $templates[$selectedTemplateId] ?? [];

        if (empty($this->templateConfig)) {
            throw new Exception('Selected email template configuration not found.');
        }

        $configPath = $this->kirby->root('site') . '/templates/emails/' . $selectedTemplateId . '/config.yml';
        if (!file_exists($configPath)) {
            throw new Exception('Konfigurations-Datei nicht gefunden: ' . $configPath);
        }
        $this->templateConfig = Data::read($configPath);

        ConfigHelper::validateTemplateConfig($this->templateConfig);
    }
    
    /**
     * Loads the translations.
     *
     * @throws \Kirby\Exception\Exception If the translations are not found.
     */
    protected function loadTranslations()
    {
        $translationsPath = $this->kirby->root('plugins') . '/kirby-email-manager/translations/' . $this->languageCode . '.php';
        $this->translations = Data::read($translationsPath);
    }

    /**
     * Handles the form submission.
     *
     * @return array The form result.
     */
    public function handle()
    {
        $this->kirby->session();
        $data = $this->kirby->request()->data();

        if (!empty($data['website'])) {
            go($this->page->url());
            exit;
        }
        
        $alert = [
            'type' => 'info',
            'message' => $this->translations['form_ready']
        ];

        if ($this->kirby->request()->is('POST') && get('submit')) {

            if (!csrf($data['csrf'] ?? '')) {
                LogHelper::logError('CSRF-Token-Fehler: ' . $data['csrf']);
                throw new Exception($this->translations['error_messages']['csrf_error'] ?? 'Invalid CSRF token.');
            }
    
            $submissionTime = (int)($data['timestamp'] ?? 0);
            $currentTime = time();
            $timeDifference = abs($currentTime - $submissionTime);

            LogHelper::logInfo("Submission time: $submissionTime, Current time: $currentTime, Difference: $timeDifference");

            if ($timeDifference > 7200) {
                $alert['type'] = 'warning';
                $alert['message'] = $this->translations['error_messages']['submission_time_warning'] ?? 'Die Übermittlungszeit ist abgelaufen. Bitte überprüfen Sie Ihre Eingaben und senden Sie das Formular erneut.';
                return [
                    'alert' => $alert,
                    'data' => $data
                ];
            }

            try {
                $errors = [];
                $emailContent = [];
                $subject = $this->translations['default_subject'] ?? 'Kontaktformular';

                foreach ($this->templateConfig['fields'] as $fieldKey => $fieldConfig) {
                    $fieldErrors = ValidationHelper::validateField($fieldKey, $fieldConfig, $data, $this->translations, $this->languageCode);
                    if (!empty($fieldErrors)) {
                        $errors = array_merge($errors, $fieldErrors);
                    }

                    $emailContent[$fieldConfig['label'][$this->languageCode]] = is_array($data[$fieldKey]) 
                        ? implode(', ', $data[$fieldKey]) 
                        : ($data[$fieldKey] ?? $this->translations['not_specified']);
                }

                if ($this->page->gdpr_checkbox()->toBool() && empty($data['gdpr'])) {
                    $errors['gdpr'] = $this->translations['error_messages']['gdpr_required'] ?? 'GDPR consent is required.';
                }

                if (!empty($errors)) {
                    $alert['type'] = 'error';
                    $alert['message'] = $this->translations['error_messages']['validation_error'];
                    $alert['errors'] = $errors;
                } else {
                    try {
                        // Sende die Hauptmail
                        EmailHelper::sendEmail($this->kirby, $this->page, $this->templateConfig, $emailContent, $data, $this->languageCode);
                        
                        // Sende die Bestätigungsmail, falls konfiguriert
                        if ($this->page->send_confirmation()->toBool()) {
                            EmailHelper::sendConfirmationEmail($this->kirby, $this->page, $this->templateConfig, $data, $this->languageCode);
                        }

                        $alert['type'] = 'success';
                        $alert['message'] = $this->translations['form_success'];
                        
                        $successMessage = SuccessMessageHelper::getSuccessMessage(
                            $this->page,
                            $data,
                            $this->languageCode
                        );
                        $alert['successMessage'] = $successMessage;
                        $data = [];
                    } catch (Exception $e) {
                        $alert = ExceptionHelper::handleException($e, $this->translations);
                        LogHelper::logError($e);
                    }
                }
            } catch (Exception $e) {
                $alert = ExceptionHelper::handleException($e, $this->translations);
                LogHelper::logError($e);
            }
        }

        return ['alert' => $alert, 'data' => $data ?? []];
    }
}