<?php

namespace KirbyEmailManager\Helpers;

use Exception;

/**
 * ConfigHelper class provides methods to validate the template configuration.
 * @author Philipp Oehrlein
 * @version 1.0.0
 */
class ConfigHelper
{
    protected static string $prefix = 'philippoehrlein.kirby-email-manager.';

    /**
     * Translates a key by automatically adding the plugin prefix
     */
    protected static function t(string $key, ?string $fallback = null): string
    {
        if (!str_starts_with($key, self::$prefix)) {
            $key = self::$prefix . $key;
        }
        
        $translation = t($key, null);
        return $translation ?? $fallback ?? $key;
    }

    /**
     * Validates the template configuration.
     *
     * @param array $templateConfig The template configuration to validate.
     * @throws Exception If the template configuration is empty or missing required keys.
     */
    public static function validateTemplateConfig($templateConfig)
    {
        if (empty($templateConfig)) {
            throw new Exception(self::t('error.template_config_empty'));
        }

        $requiredKeys = ['fields', 'buttons', 'emails'];
        foreach ($requiredKeys as $key) {
            if (!isset($templateConfig[$key])) {
                $message = self::t('error.missing_required_key');
                $message = str_replace('{key}', $key, $message);
                throw new Exception($message);
            }
        }

        self::validateFields($templateConfig['fields']);
        self::validateEmails($templateConfig['emails']);
        self::validateFormSubmission($templateConfig['formsubmission'] ?? []);
        self::validateWebhooks($templateConfig['webhooks'] ?? []);
        self::validateCaptcha($templateConfig['captcha'] ?? []);
    }
  
   /**
     * Validates the fields in the template configuration.
     *
     * This method ensures that each field has the required properties:
     * 'type', 'label'.
     *
     * @param array $fields The fields to validate.
     * @throws Exception If a field is missing required properties.
     */
    private static function validateFields($fields)
    {
        foreach ($fields as $fieldKey => $fieldConfig) {
            $requiredProperties = ['type', 'label'];
            foreach ($requiredProperties as $property) {
                if (!isset($fieldConfig[$property])) {
                    throw new Exception(t('error.field_missing_property', "Missing '{$property}' for field '{$fieldKey}' in template configuration."));
                }
            }
        }
    }

    /**
     * Validates the emails in the template configuration.
     *
     * @param array $emails The emails to validate.
     * @throws Exception If the emails are missing required properties.
     */
    private static function validateEmails($emails)
    {
        if (!isset($emails['mail']['subject']) || !isset($emails['mail']['sender'])) {
            throw new Exception(self::t('error.missing_email_configuration'));
        }
        if (isset($emails['reply'])) {
            if (!isset($emails['reply']['subject']) || !isset($emails['reply']['sender'])) {
                throw new Exception(self::t('error.missing_reply_email_configuration'));
            }
        }
    }

    /**
     * Validates the form submission in the template configuration.
     *
     * @param array $formSubmission The form submission to validate.
     * @throws Exception If the form submission is missing required properties.
     */
    private static function validateFormSubmission($formSubmission)
    {
        if (isset($formSubmission['mintime']) && $formSubmission['mintime'] < 0) {
            throw new Exception(self::t('error.invalid_mintime'));
        }
        if (isset($formSubmission['maxtime']) && $formSubmission['maxtime'] < $formSubmission['mintime']) {
            throw new Exception(self::t('error.invalid_max_time'));
        }
    }

    /**
     * Validates the webhooks in the template configuration.
     *
     * @param array $webhooks The webhooks to validate.
     * @throws Exception If the webhooks are missing required properties.
     */
    private static function validateWebhooks($webhooks)
    {
        if(empty($webhooks)) {
            return;
        }

        foreach ($webhooks as $webhook) {
            if (!isset($webhook['handler']) || !isset($webhook['events'])) {
                throw new Exception(self::t('error.missing_webhook_configuration'));
            }
        }
    }

    /**
     * Validates the captcha in the template configuration.
     *
     * @param array $captcha The captcha to validate.
     * @throws Exception If the captcha is missing required properties.
     */
    private static function validateCaptcha($captcha)
    {
        if(empty($captcha)) {
            return;
        }
        if (!isset($captcha['frontend']['snippet'])) {
            throw new Exception(self::t('error.missing_captcha_snippet', 'Missing CAPTCHA snippet configuration'));
        }
        if (!isset($captcha['frontend']['fieldname'])) {
            throw new Exception(self::t('error.missing_captcha_field_name', 'Missing CAPTCHA field name'));
        }
        if (!isset($captcha['options'])) {
            throw new Exception(self::t('error.missing_captcha_options', 'Missing CAPTCHA options'));
        }
        if (!isset($captcha['errormessages'])) {
            throw new Exception(self::t('error.missing_captcha_error_messages', 'Missing CAPTCHA error messages'));
        }
    }
}