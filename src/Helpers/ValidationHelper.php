<?php

namespace KirbyEmailManager\Helpers;

use Kirby\Toolkit\V;

/**
 * ValidationHelper class provides methods to validate form fields.
 * Author: Philipp Oehrlein
 * Version: 1.0.0
 */
class ValidationHelper
{
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
  public static function validateField($fieldKey, $fieldConfig, $data, $translations, $languageCode)
  {
    $errors = [];

    $isRequired = (!empty($fieldConfig['required']) && (!isset($fieldConfig['validation']) || $fieldConfig['validation'] === true));
    if ($isRequired && empty($data[$fieldKey])) {
        $errors[$fieldKey] = $translations['error_messages']['required'];
        return $errors;
    }

    if (!empty($fieldConfig['validate'])) {
      switch ($fieldConfig['validate']) {
        case 'text':
        case 'textarea':
        case 'secret':
          if (!empty($data[$fieldKey]) && !empty($fieldConfig['minlength'])) {
            if (strlen($data[$fieldKey]) < $fieldConfig['minlength']) {
              $errors[$fieldKey] = str_replace(
                ':minLength', 
                $fieldConfig['minlength'], 
                $translations['error_messages']['message_too_short']
              );
            }
          }
          break;

        case 'email':
          if (!empty($data[$fieldKey]) && !v::email($data[$fieldKey])) {
            $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode]
              ?? $translations['error_messages']['invalid_email'];
          }
          return $errors;

        case 'date':
          $dateValue = $data[$fieldKey] ?? null;

          $minDateConfig = $fieldConfig['min'] ?? null;
          $maxDateConfig = $fieldConfig['max'] ?? null;

          $minDate = null;
          if ($minDateConfig) {
            if (preg_match('/^([+-]?\d+)(months?|days?)$/', $minDateConfig, $matches)) {
              $num = (int)$matches[1];
              $unit = $matches[2];
              $minDate = date('Y-m-d', strtotime("$num $unit"));
            } elseif ($minDateConfig === 'today') {
              $minDate = date('Y-m-d');
            } else {
              $minDate = date('Y-m-d', strtotime($minDateConfig));
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
            } else {
              $maxDate = date('Y-m-d', strtotime($maxDateConfig));
            }
          }

          if ($dateValue !== null) {
            if (!v::date($dateValue)) {
              $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode]
                ?? $translations['error_messages']['invalid_date'];
            } else {

              if ($minDate && $dateValue < $minDate) {
                $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode]
                  ?? $translations['error_messages']['min_date']
                  ?? 'Datum liegt vor dem zulässigen Bereich.';
              }

              if ($maxDate && $dateValue > $maxDate) {
                $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode]
                  ?? $translations['error_messages']['max_date']
                  ?? 'Datum liegt nach dem zulässigen Bereich.';
              }
            }
          }
          break;

        case 'select':
          $errors = array_merge($errors, self::validateRequired($fieldKey, $fieldConfig, $data, $translations, $languageCode));
          $selectValue = $data[$fieldKey] ?? null;

          if (!empty($selectValue) && !array_key_exists($selectValue, $fieldConfig['options'])) {
            $errors[$fieldKey] = $translations['error_messages']['invalid_option']
              ?? 'Invalid option selected.';
          }
          break;

        case 'tel':
          $telValue = $data[$fieldKey] ?? null;
          
          if (!empty($telValue)) {
            $pattern = $fieldConfig['pattern'] ?? '^[0-9+\-\s()]{8,}$';
            if (!preg_match("/$pattern/", $telValue)) {
              $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode]
                  ?? $translations['error_messages']['invalid_phone'];
            }
          }
          return $errors;

        case 'file':
          if (!empty($data[$fieldKey]) && is_array($data[$fieldKey])) {
            $file = $data[$fieldKey];
            
            // Prüfe Dateigröße
            $maxSize = isset($fieldConfig['max_size']) ? (int)$fieldConfig['max_size'] : 5242880;
            if ($file['size'] > $maxSize) {
              $errors[$fieldKey] = str_replace(
                ':maxSize',
                round($maxSize / 1048576, 2),
                $fieldConfig['error_message'][$languageCode] ?? $translations['error_messages']['file_too_large']
              );
              return $errors;
            }

            // Prüfe Dateityp
            $allowedTypes = $fieldConfig['allowed_types'] ?? [];
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!empty($allowedTypes) && !in_array($fileExtension, $allowedTypes)) {
              $errors[$fieldKey] = str_replace(
                ':allowedTypes',
                implode(', ', $allowedTypes),
                $fieldConfig['error_message'][$languageCode] ?? $translations['error_messages']['invalid_file_type']
              );
              return $errors;
            }

            // Prüfe MIME-Type
            if (!empty($fieldConfig['allowed_mimes'])) {
              $finfo = new \finfo(FILEINFO_MIME_TYPE);
              $mimeType = $finfo->file($file['tmp_name']);
              if (!in_array($mimeType, $fieldConfig['allowed_mimes'])) {
                $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode] ?? 
                                     $translations['error_messages']['invalid_file_type'];
                return $errors;
              }
            }
          }
          return $errors;

        case 'number':
          $numberValue = $data[$fieldKey] ?? null;
          
          if (!empty($numberValue)) {
            if (!is_numeric($numberValue)) {
              $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode]
                  ?? $translations['error_messages']['invalid_number'];
              return $errors;
            }

            $number = floatval($numberValue);
            if (isset($fieldConfig['min']) && $number < $fieldConfig['min']) {
              $errors[$fieldKey] = str_replace(
                ':min',
                $fieldConfig['min'],
                $translations['error_messages']['number_too_small']
              );
            }
            if (isset($fieldConfig['max']) && $number > $fieldConfig['max']) {
              $errors[$fieldKey] = str_replace(
                ':max',
                $fieldConfig['max'],
                $translations['error_messages']['number_too_large']
              );
            }
          }
          return $errors;

        case 'radio':
          $radioValue = $data[$fieldKey] ?? null;
          
          if (!empty($radioValue)) {
              if (!array_key_exists($radioValue, $fieldConfig['options'])) {
                  $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode]
                      ?? $translations['error_messages']['invalid_option'];
              }
          }
          return $errors;

        case 'date-range':
          $errors = array_merge($errors, self::validateRequired($fieldKey, $fieldConfig, $data, $translations, $languageCode));
          
          $startDate = $data[$fieldKey]['start'] ?? null;
          $endDate = $data[$fieldKey]['end'] ?? null;

          if (!empty($startDate) || !empty($endDate)) {
              if (!v::date($startDate) || !v::date($endDate)) {
                  $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode] 
                      ?? $translations['error_messages']['invalid_date'];
                  return $errors;
              }

              if (strtotime($startDate) > strtotime($endDate)) {
                  $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode]
                      ?? $translations['error_messages']['invalid_date_range'];
                  return $errors;
              }

              if (isset($fieldConfig['min'])) {
                  $minDate = date('Y-m-d', strtotime($fieldConfig['min']));
                  if (strtotime($startDate) < strtotime($minDate)) {
                      $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode]
                          ?? $translations['error_messages']['date_too_early'];
                  }
              }

              if (isset($fieldConfig['max'])) {
                  $maxDate = date('Y-m-d', strtotime($fieldConfig['max']));
                  if (strtotime($endDate) > strtotime($maxDate)) {
                      $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode]
                          ?? $translations['error_messages']['date_too_late'];
                  }
              }
          }
          return $errors;

        case 'time':
          $errors = array_merge($errors, self::validateRequired($fieldKey, $fieldConfig, $data, $translations, $languageCode));
          $timeValue = $data[$fieldKey] ?? null;

          if (!empty($timeValue)) {
              if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $timeValue)) {
                  $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode]
                      ?? $translations['error_messages']['invalid_time'];
                  return $errors;
              }

              if (isset($fieldConfig['min'])) {
                  $minTime = strtotime('1970-01-01 ' . $fieldConfig['min']);
                  $currentTime = strtotime('1970-01-01 ' . $timeValue);
                  if ($currentTime < $minTime) {
                      $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode]
                          ?? $translations['error_messages']['time_too_early'];
                  }
              }

              if (isset($fieldConfig['max'])) {
                  $maxTime = strtotime('1970-01-01 ' . $fieldConfig['max']);
                  $currentTime = strtotime('1970-01-01 ' . $timeValue);
                  if ($currentTime > $maxTime) {
                      $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode]
                          ?? $translations['error_messages']['time_too_late'];
                  }
              }
          }
          return $errors;

        case 'url':
          if (!empty($data[$fieldKey])) {
              if (!v::url($data[$fieldKey])) {
                  $errors[$fieldKey] = $fieldConfig['error_message'][$languageCode]
                      ?? $translations['error_messages']['invalid_url'];
              }
          }
          return $errors;
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
  public static function validateRequired($fieldKey, $fieldConfig, $data, $translations, $languageCode)
  {
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
  public static function validateMinLength($fieldKey, $fieldConfig, $data, $translations, $languageCode)
  {
    $errors = [];
    if (!empty($data[$fieldKey]) && !empty($fieldConfig['minlength'])) {
      $minLength = $fieldConfig['minlength'];
      if (strlen($data[$fieldKey]) < $minLength) {
        $errors[$fieldKey] = str_replace(':minLength', $minLength, $translations['error_messages']['message_too_short']);
      }
    }
    return $errors;
  }
}
