<?php
namespace KirbyEmailManager\PageMethods;

use KirbyEmailManager\Helpers\LanguageHelper;
use KirbyEmailManager\Helpers\EmailHelper;
use Kirby\Data\Data;
use Exception;

use Kirby\Toolkit\V;
use Kirby\Filesystem\F;

use KirbyEmailManager\Helpers\ExceptionHelper;
use KirbyEmailManager\Helpers\ValidationHelper;
use KirbyEmailManager\Helpers\LogHelper;
use KirbyEmailManager\Helpers\ConfigHelper;
use KirbyEmailManager\Helpers\SuccessMessageHelper;


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
        $this->languageCode = LanguageHelper::getCurrentLanguageCode();
        
        // Load template configuration
        $this->loadTemplateConfig();
        
        // Load translations
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
            throw new Exception(t('error_messages.no_template', 'No email template selected.'));
        }

        $templates = $this->kirby->option('philippoehrlein.kirby-email-manager.templates');
        $this->templateConfig = $templates[$selectedTemplateId] ?? [];

        if (empty($this->templateConfig)) {
            throw new Exception(t('error_messages.template_not_found', 'Selected email template configuration not found.'));
        }

        $configPath = $this->kirby->root('site') . '/templates/emails/' . $selectedTemplateId . '/config.yml';
        if (!file_exists($configPath)) {
            throw new Exception(t('error_messages.config_file_not_found', 'Configuration file not found: ') . $configPath);
        }
        $this->templateConfig = Data::read($configPath);

        // Add template path to configuration
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
                            error_log(t('error_messages.file_move_error', 'Error moving file. PHP error: ') . error_get_last()['message']);
                        }
                    }
                }
            }

            
            if (csrf(get('csrf')) !== true) {
                LogHelper::logError(t('error_messages.csrf_error', 'Invalid CSRF token.'));
                throw new Exception(t('error_messages.csrf_error', 'Invalid CSRF token.'));
            }
    
            $submissionTime = (int)($data['timestamp'] ?? 0);
            $currentTime = time();
            $timeDifference = abs($currentTime - $submissionTime);

            $minTime = $this->templateConfig['form_submission']['min_time'] ?? 10;
            $maxTime = $this->templateConfig['form_submission']['max_time'] ?? 7200;

            if ($timeDifference < $minTime) { 
                $alert['type'] = 'warning';
                $alert['message'] = $this->translations['error_messages']['submission_time_too_fast'] ?? 'Form was submitted too quickly. Please try again.';
                return [
                    'alert' => $alert,
                    'data' => $data
                ];
            } elseif ($timeDifference > $maxTime) {
                $alert['type'] = 'warning';
                $alert['message'] = $this->translations['error_messages']['submission_time_warning'] ?? 'Submission time has expired. Please check your inputs and submit the form again.';
                return [
                    'alert' => $alert,
                    'data' => $data
                ];
            }

            try {
                $errors = [];
                $emailContent = [];
                $subject = LanguageHelper::getTranslatedValue(
                    $this->templateConfig['emails']['subject']['default'] ?? [], 
                    null, 
                    $this->languageCode
                ) ?? $this->translations['email_subject'];

                if (isset($data['topic'])) {
                    $topicSubject = LanguageHelper::getTranslatedValue(
                        $this->templateConfig['emails']['subject']['topic'] ?? [], 
                        null, 
                        $this->languageCode
                    ) ?? $this->translations['topic_subject'];
                    
                    if ($topicSubject) {
                        $topicLabel = LanguageHelper::getTranslatedValue(
                            $this->templateConfig['fields']['topic']['options'][$data['topic']], 
                            null, 
                            $this->languageCode
                        );
                        $subject = str_replace(':topic', $topicLabel, $topicSubject);
                    }
                }

                foreach ($this->templateConfig['fields'] as $fieldKey => $fieldConfig) {
                    if ($fieldConfig['type'] === 'file') {
                        continue;
                    }

                    $fieldErrors = ValidationHelper::validateField($fieldKey, $fieldConfig, $data, $this->translations, $this->languageCode);
                    if (!empty($fieldErrors)) {
                        $errors = array_merge($errors, $fieldErrors);
                    }

                    $label = LanguageHelper::getTranslatedValue($fieldConfig, 'label', $fieldKey);
                    $emailContent[$label] = match(true) {
                        $fieldConfig['type'] === 'date-range' && isset($data[$fieldKey]) && is_array($data[$fieldKey]) => 
                            sprintf(
                                '%s - %s',
                                htmlspecialchars($data[$fieldKey]['start'] ?? $this->translations['not_specified']),
                                htmlspecialchars($data[$fieldKey]['end'] ?? $this->translations['not_specified'])
                            ),
                        isset($data[$fieldKey]) && is_array($data[$fieldKey]) => 
                            implode(', ', array_map('htmlspecialchars', $data[$fieldKey])),
                        default => 
                            htmlspecialchars($data[$fieldKey] ?? $this->translations['not_specified'])
                    };

                    
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
                        
                        EmailHelper::sendEmail($this->kirby, $this->contentWrapper, $this->page, $this->templateConfig, $emailContent, $data, $this->languageCode, $attachments, $subject);
                        
                        // Send confirmation email if configured
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
