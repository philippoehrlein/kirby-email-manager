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
                error_log("Validierung für Datum '$fieldKey'");
            
                // Eingabewert für das Datum
                $dateValue = $data[$fieldKey] ?? null; 
                error_log("Eingabewert für 'date': " . ($dateValue !== null ? $dateValue : 'null'));
            
                // Berechne die Min- und Max-Daten basierend auf der Konfiguration
                $minDateConfig = $fieldConfig['min'] ?? null; // Min aus der Konfiguration
                $maxDateConfig = $fieldConfig['max'] ?? null; // Max aus der Konfiguration
            
                // Tatsächliche Min- und Max-Daten berechnen, wenn sie angegeben sind
                $minDate = null;
                if ($minDateConfig) {
                    if (preg_match('/^([+-]?\d+)(months?|days?)$/', $minDateConfig, $matches)) {
                        $num = (int)$matches[1];
                        $unit = $matches[2];
                        $minDate = date('Y-m-d', strtotime("$num $unit"));
                    } elseif ($minDateConfig === 'today') {
                        $minDate = date('Y-m-d');
                    }
                }
            
                $maxDate = null;
                if ($maxDateConfig) {
                    if (preg_match('/^([+-]?\d+)(months?|days?)$/', $maxDateConfig, $matches)) {
                        $num = (int)$matches[1];
                        $unit = $matches[2];
                        $maxDate = date('Y-m-d', strtotime("$num $unit"));
                    } elseif ($maxDateConfig === 'today') {
                        $maxDate = date('Y-m-d');
                    }
                }
            
                // Überprüfung der Min- und Max-Daten
                error_log("Min-Datum: " . ($minDate ?? 'nicht angegeben'));
                error_log("Max-Datum: " . ($maxDate ?? 'nicht angegeben'));
            
                // Validierung des Eingabewertes
                if ($dateValue !== null) {
                    // Prüfen des Datumsformats
                    if (!v::date($dateValue)) {
                        $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode] 
                            ?? $translations['error_messages']['invalid_date'];
                        error_log("Ungültiges Datum: $dateValue");
                    } else {
                        error_log("Validierung für Datum '$fieldKey': Eingabewert = $dateValue, Min = $minDate, Max = $maxDate");
            
                        // Min/Max-Validierung, nur durchführen, wenn min oder max definiert sind
                        if ($minDate && $dateValue < $minDate) {
                            $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode] 
                                ?? $translations['error_messages']['min_date'] 
                                ?? 'Datum liegt vor dem zulässigen Bereich.';
                            error_log("Fehler: Datum liegt vor dem zulässigen Bereich. Eingabewert: $dateValue");
                        }
            
                        if ($maxDate && $dateValue > $maxDate) {
                            $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode] 
                                ?? $translations['error_messages']['max_date'] 
                                ?? 'Datum liegt nach dem zulässigen Bereich.';
                            error_log("Fehler: Datum liegt nach dem zulässigen Bereich. Eingabewert: $dateValue");
                        }
                    }
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
                $defaultPattern = '^[+]?[(]?[0-9]{1,4}[)]?[-\s.]?[0-9]{1,4}[-\s.]?[0-9]{1,9}$';
                $pattern = $fieldConfig['pattern'] ?? $defaultPattern; 

                // Pattern validation (if a value is provided)
                if (!empty($telValue)) {
                    $isValid = preg_match("/$pattern/", $telValue);
                    if (!$isValid) {
                        $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode] 
                            ?? $translations['error_messages']['invalid_phone'] 
                            ?? 'Bitte geben Sie eine gültige Telefonnummer ein.';
                    }
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
