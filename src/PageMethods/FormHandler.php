<?php
namespace KirbyEmailManager\PageMethods;

use KirbyEmailManager\Helpers\LanguageHelper;
use KirbyEmailManager\Helpers\EmailHelper;
use Kirby\Data\Data;
use Kirby\Http\Request\Files;
use Exception;

use Kirby\Filesystem\F;

use KirbyEmailManager\Helpers\ExceptionHelper;
use KirbyEmailManager\Helpers\ValidationHelper;
use KirbyEmailManager\Helpers\LogHelper;
use KirbyEmailManager\Helpers\ConfigHelper;
use KirbyEmailManager\Helpers\SuccessMessageHelper;
use KirbyEmailManager\Helpers\SecurityHelper;
use KirbyEmailManager\Helpers\FileValidationHelper;

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
    protected $languageHelper;
    protected $page;
    /**
     * Constructor for FormHandler.
     *
     * @param \Kirby\Cms\App $kirby The Kirby application instance.
     * @param \Kirby\Cms\Page $page The current page instance.
     * @param \KirbyEmailManager\PageMethods\ContentWrapper $contentWrapper The content wrapper instance.
     */
    public function __construct($kirby, $page, $contentWrapper)
    {
        $this->kirby = $kirby;
        $this->page = $page;
        $this->contentWrapper = $contentWrapper;
        
        // Config laden und validieren
        $this->loadTemplateConfig();
        ConfigHelper::validateTemplateConfig($this->templateConfig);
        
        // LanguageHelper nur einmal initialisieren
        $this->languageHelper = new LanguageHelper(null, $this->templateConfig);
        $this->languageCode = $this->languageHelper->getLanguage();
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
            throw new Exception('No template selected');
        }

        // Plugin-Config und YML-Config zusammenführen
        $templates = $this->kirby->option('philippoehrlein.kirby-email-manager.templates');
        $pluginConfig = $templates[$selectedTemplateId] ?? [];
        
        $configPath = $this->kirby->root('site') . '/templates/emails/' . $selectedTemplateId . '/config.yml';
        if (!file_exists($configPath)) {
            throw new Exception('Config file not found: ' . $configPath);
        }

        $ymlConfig = Data::read($configPath);
        if (!is_array($ymlConfig)) {
            $ymlConfig = [];
        }
        $this->templateConfig = array_merge($pluginConfig, $ymlConfig);
    }

    /**
     * Returns the template configuration.
     *
     * @return array The template configuration.
     */
    public function getTemplateConfig() {
        return $this->templateConfig;
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
        $errors = [];
        $alert = [];

        // CAPTCHA Validierung durchführen
        if (isset($this->templateConfig['captcha'])) {
            $captchaErrors = ValidationHelper::validateCaptcha(
                $data, 
                $this->templateConfig, 
                $this->languageHelper
            );
            
            if (!empty($captchaErrors)) {
                return [
                    'alert' => [
                        'type' => 'error',
                        'message' => $this->languageHelper->get('captcha.error.invalid'),
                        'errors' => $captchaErrors
                    ],
                    'data' => $data
                ];
            }
        }

        if (!empty($data['website_hp_'])) {
            go($this->page->url());
        }
        
        $alert = [
            'type' => 'info',
            'message' => 'Form ready'
        ];

        if ($this->kirby->request()->is('POST') && get('submit')) {

            // CSRF-Token validieren
            if (!SecurityHelper::validateCSRFToken(get('csrf'))) {
                return [
                    'alert' => [
                        'type' => 'error',
                        'message' => $this->languageHelper->get('system.csrf_error')
                    ],
                    'data' => $data
                ];
            }

            // Formulardaten bereinigen
            $data = SecurityHelper::sanitizeAndValidateFormData($data);

            // Datei-Uploads verarbeiten
            $attachments = [];
            $uploads = $this->kirby->request()->files()->toArray();
            foreach ($uploads as $field => $uploadField) {
                if (is_array($uploadField)) {
                    foreach ($uploadField as $upload) {
                        if (is_array($upload) && isset($upload['error']) && $upload['error'] === UPLOAD_ERR_OK) {
                            $originalName = SecurityHelper::sanitizeFilename($upload['name']);
                            $targetPath = $upload['tmp_name'] . '_' . $originalName;

                            if (move_uploaded_file($upload['tmp_name'], $targetPath)) {
                                $attachments[] = $targetPath;
                            }
                        }
                    }
                }
            }

            // E-Mail-Validierung für E-Mail-Felder
            if (isset($data['email']) && !SecurityHelper::validateEmail($data['email'])) {
                return [
                    'alert' => [
                        'type' => 'error',
                        'message' => $this->languageHelper->get('validation.email')
                    ],
                    'data' => $data
                ];
            }

            $submissionTime = (int)($data['timestamp'] ?? 0);
            $currentTime = time();
            $timeDifference = abs($currentTime - $submissionTime);

            $minTime = $this->templateConfig['form_submission']['min_time'] ?? 10;
            $maxTime = $this->templateConfig['form_submission']['max_time'] ?? 7200;

            if ($timeDifference < $minTime) { 
                $alert['type'] = 'warning';
                $alert['message'] = $this->languageHelper->get('validation.system.submission_time.too_fast');
                return [
                    'alert' => $alert,
                    'data' => $data
                ];
            } elseif ($timeDifference > $maxTime) {
                $alert['type'] = 'warning';
                $alert['message'] = $this->languageHelper->get('validation.system.submission_time.warning');
                return [
                    'alert' => $alert,
                    'data' => $data
                ];
            }

            try {
                $errors = [];
                $emailContent = [];

                if(isset($data['topic'])) {
                    $subject = $this->languageHelper->get('emails.subject.topic', ['topic' => $data['topic']]);
                } else {
                    $subject = $this->languageHelper->get('emails.subject.default');
                }

                foreach ($this->templateConfig['fields'] as $fieldKey => $fieldConfig) {
                    // Spezielle Behandlung für File-Uploads
                    if ($fieldConfig['type'] === 'file') {
                        $files = $this->kirby->request()->files();
                        $fileErrors = [];
                        
                        // Required-Prüfung für Files
                        if (!empty($fieldConfig['required'])) {
                            if (!isset($files->data()[$fieldKey]) || empty($files->data()[$fieldKey])) {
                                $errors[$fieldKey] = $this->languageHelper->get('validation.fields.file.no_file_uploaded');
                                continue;
                            }
                        }
                        
                        // Wenn Files vorhanden sind, validiere sie
                        if (isset($files->data()[$fieldKey])) {
                            // Prüfe maximale Anzahl der Dateien
                            if (isset($fieldConfig['max_files']) && count($files->data()[$fieldKey]) > $fieldConfig['max_files']) {
                                $errors[$fieldKey] = str_replace(
                                    ':maxFiles',
                                    $fieldConfig['max_files'],
                                    $this->languageHelper->get('validation.fields.file.too_many_files')
                                );
                                continue;
                            }

                            foreach ($files->data()[$fieldKey] as $file) {
                                $fileErrors = FileValidationHelper::validateFile($file, $fieldConfig, $this->languageCode);
                                if (!empty($fileErrors)) {
                                    $errors[$fieldKey] = $fileErrors['error'] ?? $this->languageHelper->get('validation.fields.file.unknown_error');
                                    break;
                                }
                            }
                            
                            // Wenn keine Fehler, speichere nur den Dateinamen für die E-Mail
                            if (empty($fileErrors)) {
                                $data[$fieldKey] = array_map(function($file) {
                                    return $file['name'];
                                }, $files->data()[$fieldKey]);
                            }
                        }
                        
                        continue;
                    }

                    // Normale Validierung für alle anderen Feldtypen
                    $fieldErrors = ValidationHelper::validateField($fieldKey, $fieldConfig, $data, $this->templateConfig, $this->languageCode);
                    if (!empty($fieldErrors)) {
                        $errors = array_merge($errors, $fieldErrors);
                    }
                    
                    $label = $this->languageHelper->get('fields.' . $fieldKey . '.label');
                    $emailContent[$label] = match(true) {
                        $fieldConfig['type'] === 'date-range' && isset($data[$fieldKey]) && is_array($data[$fieldKey]) => 
                            sprintf(
                                '%s - %s',
                                htmlspecialchars($data[$fieldKey]['start'] ?? $this->languageHelper->get('validation.template.not_specified')),
                                htmlspecialchars($data[$fieldKey]['end'] ?? $this->languageHelper->get('validation.template.not_specified'))
                            ),
                        isset($data[$fieldKey]) && is_array($data[$fieldKey]) => 
                            implode(', ', array_map('htmlspecialchars', $data[$fieldKey])),
                        default => 
                            htmlspecialchars($data[$fieldKey] ?? $this->languageHelper->get('validation.template.not_specified'))
                    };

                    
                }

                if ($this->contentWrapper->gdpr_checkbox()->toBool() && empty($data['gdpr'])) {
                    $errors['gdpr'] = $this->languageHelper->get('validation.system.gdpr_required');
                }

                if (!empty($errors)) {
                    error_log('ERROR');
                    $alert['type'] = 'error';
                    $alert['message'] = $this->languageHelper->get('validation.template.validation_error');
                    $alert['errors'] = $errors;
                } else {
                    try {
                        EmailHelper::sendEmail($this->kirby, $this->contentWrapper, $this->page, $this->templateConfig, $data, $this->languageCode, $attachments, $subject);

                        $alert['type'] = 'success';
                        $alert['message'] = $this->languageHelper->get('form.success');
                        
                        $successMessage = SuccessMessageHelper::getSuccessMessage($this->contentWrapper, $data, $this->languageCode);
                        $session->set('form.success', $successMessage);
                    
                        go($this->page->url());
                    } catch (Exception $e) {
                        $alert = ExceptionHelper::handleException($e, $this->languageHelper);
                        LogHelper::logError($e);
                    }
                }
            } catch (Exception $e) {
                $alert = ExceptionHelper::handleException($e, $this->languageHelper);
                LogHelper::logError($e);
            }
        }
        return ['alert' => $alert, 'data' => $data ?? []];
    }
}
