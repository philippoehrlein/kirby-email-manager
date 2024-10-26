<?php
namespace KirbyEmailManager\PageMethods;

use Kirby\Data\Data;
use Kirby\Toolkit\V;
use Kirby\Filesystem\F;

use Kirby\Exception\Exception;
use KirbyEmailManager\Helpers\EmailHelper;
use KirbyEmailManager\Helpers\ExceptionHelper;
use KirbyEmailManager\Helpers\ValidationHelper;
use KirbyEmailManager\Helpers\LogHelper;
use KirbyEmailManager\Helpers\ConfigHelper;
use KirbyEmailManager\Helpers\SuccessMessageHelper;
use KirbyEmailManager\PageMethods\ContentWrapper;


/**
 * FormHandler class provides methods to handle form submissions.
 * Author: Philipp Oehrlein
 * Version: 1.0.0
 */
class FormHandler
{
    protected $kirby;
    protected $contentWrapper; 
    protected $languageCode;
    protected $templateConfig;
    protected $translations;
    protected $page;
    /**
     * Constructor for FormHandler.
     *
     * @param \Kirby\Cms\App $kirby The Kirby application instance.
     * @param \Kirby\Cms\Page $page The current page instance.
     */
    public function __construct($kirby, $page, $contentWrapper)
    {
        $this->kirby = $kirby;
        $this->page = $page;
        $this->contentWrapper = $contentWrapper;
        $this->languageCode = $kirby->language()->code() ?? 'en';
        
        // Lade das Template-Konfiguration
        $this->loadTemplateConfig();
        
        // // Lade die Übersetzungen
        $this->loadTranslations();
    }
    
    /**
     * Loads the template configuration.
     *
     * @throws \Kirby\Exception\Exception If the template configuration is not found.
     */
    protected function loadTemplateConfig()
    {
        $selectedTemplateId = $this->contentWrapper->email_templates()->value();
        
        if (empty($selectedTemplateId)) {
            throw new Exception('No email template selected.');
        }

        $templates = $this->kirby->option('philippoehrlein.kirby-email-manager.templates');
        $this->templateConfig = $templates[$selectedTemplateId] ?? [];

        if (empty($this->templateConfig)) {
            throw new Exception('Selected email template configuration not found in FormHandler.');
        }

        $configPath = $this->kirby->root('site') . '/templates/emails/' . $selectedTemplateId . '/config.yml';
        if (!file_exists($configPath)) {
            throw new Exception('Konfigurations-Datei nicht gefunden: ' . $configPath);
        }
        $this->templateConfig = Data::read($configPath);

        // Fügen Sie den Template-Pfad zur Konfiguration hinzu
        $this->templateConfig['template_path'] = 'emails/' . $selectedTemplateId;

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
        $session = $this->kirby->session();
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
            $uploads = $this->kirby->request()->files()->toArray();

            $attachments = [];
            foreach ($uploads as $fieldName => $uploadField) {
                foreach ($uploadField as $upload) {
                    if ($upload['error'] === UPLOAD_ERR_OK) {
                        $tmpName = $upload['tmp_name'];
                        $originalName = $upload['name'];
                        $safeFileName = F::safeName($originalName);
                        $targetPath = $tmpName . '_' . $safeFileName;

                        if (move_uploaded_file($tmpName, $targetPath)) {
                            $attachments[] = $targetPath;
                        } else {
                            error_log('Fehler beim Verschieben der Datei. PHP-Fehler: ' . error_get_last()['message']);
                        }
                    }
                }
            }

            
            if (csrf(get('csrf')) !== true) {
                LogHelper::logError('CSRF-Token-Fehler: ' . $data['csrf']);
                throw new Exception($this->translations['error_messages']['csrf_error'] ?? 'Invalid CSRF token.');
            }
    
            $submissionTime = (int)($data['timestamp'] ?? 0);
            $currentTime = time();
            $timeDifference = abs($currentTime - $submissionTime);

            $minTime = $this->templateConfig['form_submission']['min_time'] ?? 10;
            $maxTime = $this->templateConfig['form_submission']['max_time'] ?? 7200;

            if ($timeDifference < $minTime) { 
                $alert['type'] = 'warning';
                $alert['message'] = $this->translations['error_messages']['submission_time_too_fast'] ?? 'Das Formular wurde zu schnell übermittelt. Bitte versuchen Sie es erneut.';
                return [
                    'alert' => $alert,
                    'data' => $data
                ];
            } elseif ($timeDifference > $maxTime) {
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
                    if ($fieldConfig['type'] === 'file') {
                        continue;
                    }

                    $fieldErrors = ValidationHelper::validateField($fieldKey, $fieldConfig, $data, $this->translations, $this->languageCode);
                    if (!empty($fieldErrors)) {
                        $errors = array_merge($errors, $fieldErrors);
                    }

                    $emailContent[$fieldConfig['label'][$this->languageCode]] = is_array($data[$fieldKey]) 
                    ? implode(', ', array_map('htmlspecialchars', $data[$fieldKey])) 
                    : htmlspecialchars($data[$fieldKey] ?? $this->translations['not_specified']);

                    
                }

                if ($this->contentWrapper->gdpr_checkbox()->toBool() && empty($data['gdpr'])) {
                    $errors['gdpr'] = $this->translations['error_messages']['gdpr_required'] ?? 'GDPR consent is required.';
                }

                if (!empty($errors)) {
                    $alert['type'] = 'error';
                    $alert['message'] = $this->translations['error_messages']['validation_error'];
                    $alert['errors'] = $errors;
                } else {
                    try {
                        
                        EmailHelper::sendEmail($this->kirby, $this->contentWrapper, $this->page, $this->templateConfig, $emailContent, $data, $this->languageCode, $attachments);
                        
                        // Sende die Bestätigungsmail, falls konfiguriert
                        if ($this->contentWrapper->send_confirmation_email()->toBool()) {
                            EmailHelper::sendConfirmationEmail($this->kirby, $this->contentWrapper, $this->page, $this->templateConfig, $data, $this->languageCode);
                        }

                        $alert['type'] = 'success';
                        $alert['message'] = $this->translations['form_success'];
                        
                        $successMessage = SuccessMessageHelper::getSuccessMessage($this->contentWrapper, $data, $this->languageCode);
                        $session->set('form.success', $successMessage);
                    
                        go($this->page->url());
                        exit;

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
