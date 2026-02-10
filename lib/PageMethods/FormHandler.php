<?php
namespace KirbyEmailManager\PageMethods;

use KirbyEmailManager\Helpers\LanguageHelper;
use KirbyEmailManager\Helpers\EmailHelper;
use Kirby\Data\Data;
use Exception;

use KirbyEmailManager\Helpers\ExceptionHelper;
use KirbyEmailManager\Helpers\ValidationHelper;
use KirbyEmailManager\Helpers\LogHelper;
use KirbyEmailManager\Helpers\ConfigHelper;
use KirbyEmailManager\Helpers\SuccessMessageHelper;
use KirbyEmailManager\Helpers\SecurityHelper;
use KirbyEmailManager\Helpers\WebhookHelper;
use KirbyEmailManager\Helpers\RateLimitHelper;
use KirbyEmailManager\Helpers\AttachmentHelper;
use KirbyEmailManager\Helpers\BlacklistHelper;

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
        
        // Load and validate config
        $this->loadTemplateConfig();
        ConfigHelper::validateTemplateConfig($this->templateConfig);
        
        // Initialize LanguageHelper only once
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
        
        $blueprintPath = $this->kirby->root('blueprints') . '/emails/' . $selectedTemplateId . '.yml';
        if (!file_exists($blueprintPath)) {
            throw new Exception('Blueprint not found: ' . $blueprintPath);
        }

        $this->templateConfig = Data::read($blueprintPath);
        if (!is_array($this->templateConfig)) {
            throw new Exception('Invalid blueprint configuration');
        }
        
        // Add template ID to config
        $this->templateConfig['id'] = $selectedTemplateId;
        
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

        // Perform CAPTCHA validation
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

            // Validate CSRF token
            if (!SecurityHelper::validateCSRFToken(get('csrf'))) {
                return [
                    'alert' => [
                        'type' => 'error',
                        'message' => $this->languageHelper->get('validation.system.csrf')
                    ],
                    'data' => $data
                ];
            }

            // Clean form data
            $data = SecurityHelper::sanitizeAndValidateFormData($data);

            // Blacklist check (silent rejection like honeypot)
            $blacklistResult = BlacklistHelper::check($data);
            if ($blacklistResult['blocked']) {
                WebhookHelper::trigger('form.blocked', [
                    'reason' => 'blacklist',
                    'matched' => $blacklistResult['matched'],
                    'data' => $data
                ], $this->templateConfig);
                go($this->page->url());
            }

            // Process file uploads early to get validation errors
            $uploads = $this->kirby->request()->files()->toArray();
            $uploadResult = AttachmentHelper::processUploads($uploads, $this->templateConfig, $this->languageCode);
            $attachments = $uploadResult['attachments'];
            $fileData = $uploadResult['fileData'];

            // Email validation for email fields
            if (isset($data['email']) && !SecurityHelper::validateEmail($data['email'])) {
                return [
                    'alert' => [
                        'type' => 'error',
                        'message' => $this->languageHelper->get('validation.fields.email')
                    ],
                    'data' => $data
                ];
            }

            $submissionTime = (int)($data['timestamp'] ?? 0);
            $currentTime = time();
            $timeDifference = abs($currentTime - $submissionTime);

            $minTime = $this->templateConfig['formsubmission']['mintime'] ?? 10;
            $maxTime = $this->templateConfig['formsubmission']['maxtime'] ?? 7200;

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
                // Rate Limit Check vor der Formularverarbeitung
                if (!RateLimitHelper::checkRateLimit($this->templateConfig)) {
                    return [
                        'alert' => [
                            'type' => 'error',
                            'message' => $this->languageHelper->get('error.rate_limit_exceeded')
                        ],
                        'data' => $data
                    ];
                }

                $errors = [];
                $emailContent = [];

                // Validate topic field if send_to_more is enabled
                if ($this->contentWrapper->send_to_more()->toBool() && isset($data['topic'])) {
                    $allowedTopics = [];
                    foreach ($this->contentWrapper->send_to_structure()->toStructure() as $item) {
                        $allowedTopics[] = $item->topic()->value();
                    }
                    
                    if (!in_array($data['topic'], $allowedTopics, true)) {
                        return [
                            'alert' => [
                                'type' => 'error',
                                'message' => $this->languageHelper->get('validation.fields.option')
                            ],
                            'data' => $data
                        ];
                    }
                }

                if(isset($data['topic'])) {
                    $subject = $this->languageHelper->get('emails.subject.topic', ['topic' => $data['topic']]);
                } else {
                    $subject = $this->languageHelper->get('emails.subject.default');
                }

                // Add file data to form data
                $data = array_merge($data, $fileData);

                // Add file validation errors
                $errors = array_merge($errors, $uploadResult['errors']);

                foreach ($this->templateConfig['fields'] as $fieldKey => $fieldConfig) {
                    // Skip file fields (already processed by AttachmentHelper)
                    if ($fieldConfig['type'] === 'file') {
                        continue;
                    }

                    // Normal validation for all other field types
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
                    $alert['type'] = 'error';
                    $alert['message'] = $this->languageHelper->get('validation.template.validation_error');
                    $alert['errors'] = $errors;
                } else {
                    try {
                        EmailHelper::sendEmail($this->kirby, $this->contentWrapper, $this->page, $this->templateConfig, $data, $this->languageCode, $attachments, $subject);

                        $alert['type'] = 'success';
                        $alert['message'] = $this->languageHelper->get('form.status.success');
                        
                        $successMessage = SuccessMessageHelper::getSuccessMessage($this->contentWrapper, $data, $this->languageCode);
                        $session->set('form.success', $successMessage);
                        
                        // Webhook mit Template-Konfiguration triggern
                        WebhookHelper::trigger('form.success', $data, $this->templateConfig);
                        
                        go($this->page->url());
                    } catch (Exception $e) {
                        $alert = ExceptionHelper::handleException($e, $this->languageHelper);
                        LogHelper::logError($e);
                        // Webhook mit Template-Konfiguration triggern
                        WebhookHelper::trigger('form.error', [
                            'error' => $e->getMessage(),
                            'data' => $data
                        ], $this->templateConfig);
                    }
                }
            } catch (Exception $e) {
                $alert = ExceptionHelper::handleException($e, $this->languageHelper);
                LogHelper::logError($e);
                // Webhook mit Template-Konfiguration triggern
                WebhookHelper::trigger('form.error', [
                    'error' => $e->getMessage(),
                    'data' => $data
                ], $this->templateConfig);
            }
        }
        return ['alert' => $alert, 'data' => $data ?? []];
    }
}
