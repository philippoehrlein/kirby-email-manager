<?php
namespace KirbyEmailManager\Helpers;

use Kirby\Toolkit\V;

/**
 * ValidationHelper class provides methods to validate form fields.
 * Author: Philipp Oehrlein
 * Version: 1.0.0
 */
class ValidationHelper {
    /**
     * Validates a field based on its configuration and data.
     *
     * @param string $fieldKey The key of the field to validate.
     * @param array $fieldConfig The configuration of the field.
     * @param array $data The form data.
     * @param array $translations The translations array.
     * @param string $languageCode The language code.
     * @return array The validation errors.
     */
    public static function validateField($fieldKey, $fieldConfig, $data, $translations, $languageCode) {
        $errors = [];

    if (!empty($fieldConfig['validate'])) {
        switch ($fieldConfig['validate']) {
            case 'text':
            case 'textarea':
            case 'secret':
                $errors = array_merge($errors, self::validateRequired($fieldKey, $fieldConfig, $data, $translations, $languageCode));
                $errors = array_merge($errors, self::validateMinLength($fieldKey, $fieldConfig, $data, $translations, $languageCode));
                break;
        
            case 'email':
                $errors = array_merge($errors, self::validateRequired($fieldKey, $fieldConfig, $data, $translations, $languageCode));
                if (!v::email($data[$fieldKey])) {
                    $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode] 
                        ?? $translations['error_messages']['invalid_email'];
                }
                break;
        
            case 'date':
                $errors = array_merge($errors, self::validateRequired($fieldKey, $fieldConfig, $data, $translations, $languageCode));
                $dateValue = $data[$fieldKey] ?? null;
        
                // Date format check
                if (!empty($dateValue) && !v::date($dateValue, 'Y-m-d')) {
                    $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode] 
                        ?? $translations['error_messages']['invalid_date'];
                }
        
                // Min/Max date validation
                if (!empty($fieldConfig['min']) && $dateValue < $fieldConfig['min']) {
                    $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode] 
                        ?? $translations['error_messages']['min_date'] 
                        ?? 'Date is earlier than allowed.';
                }
        
                if (!empty($fieldConfig['max']) && $dateValue > $fieldConfig['max']) {
                    $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode] 
                        ?? $translations['error_messages']['max_date'] 
                        ?? 'Date is later than allowed.';
                    }
                    break;
        
            case 'select':
                $errors = array_merge($errors, self::validateRequired($fieldKey, $fieldConfig, $data, $translations, $languageCode));
                $selectValue = $data[$fieldKey] ?? null;
        
                // Valid option check
                if (!empty($selectValue) && !array_key_exists($selectValue, $fieldConfig['options'])) {
                    $errors[$fieldKey] = $translations['error_messages']['invalid_option'] 
                        ?? 'Invalid option selected.';
                }
                break;
        
            case 'tel':
                $errors = array_merge($errors, self::validateRequired($fieldKey, $fieldConfig, $data, $translations, $languageCode));
                $telValue = $data[$fieldKey] ?? null;
                $pattern = $fieldConfig['pattern'] ?? '^[+]?[(]?[0-9]{1,4}[)]?[-\s.]?[0-9]{1,4}[-\s.]?[0-9]{1,9}$'; 
        
                // Pattern validation (if a value is provided)
                if (!empty($telValue) && !preg_match("/$pattern/", $telValue)) {
                    $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode] 
                        ?? $translations['error_messages']['invalid_phone'] 
                        ?? 'Please enter a valid phone number.';
                }
                break;
            }
        }

        return $errors;
    }

    /**
     * Validates if a field is required.
     *
     * @param string $fieldKey The key of the field to validate.
     * @param array $fieldConfig The configuration of the field.
     * @param array $data The form data.
     * @param array $translations The translations array.
     * @param string $languageCode The language code.
     * @return array The validation errors. 
     */
    public static function validateRequired($fieldKey, $fieldConfig, $data, $translations, $languageCode) {
        $errors = [];
        if (!empty($fieldConfig['required']) && empty($data[$fieldKey])) {
            $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode] 
                ?? $translations['error_messages']['required'];
        }
        return $errors;
    }
    
    /**
     * Validates the minimum length of a field.
     *
     * @param string $fieldKey The key of the field to validate.
     * @param array $fieldConfig The configuration of the field.
     * @param array $data The form data.
     * @param array $translations The translations array.
     * @param string $languageCode The language code.
     * @return array The validation errors. 
     */
    public static function validateMinLength($fieldKey, $fieldConfig, $data, $translations, $languageCode) {
        $errors = [];
        if (!empty($data[$fieldKey]) && !empty($fieldConfig['validate']) && strpos($fieldConfig['validate'], 'minLength:') === 0) {
            $minLength = intval(substr($fieldConfig['validate'], 10));
            if (strlen($data[$fieldKey]) < $minLength) {
                $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode] 
                    ?? str_replace(':minLength', $minLength, $translations['error_messages']['message_too_short']);
            }
        }

        return $errors;
    }
    
}