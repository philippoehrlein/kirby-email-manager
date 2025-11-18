<?php

namespace KirbyEmailManager\Helpers;

use KirbyEmailManager\Helpers\SecurityHelper;
use KirbyEmailManager\Helpers\LanguageHelper;
use KirbyEmailManager\Helpers\FileValidationHelper;
use DateTime;

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
   * @param array $templateConfig The template configuration.
   * @param string $languageCode The language code.
   * @return array The validation errors.
   */
  public static function validateField($fieldKey, $fieldConfig, $data, $templateConfig, $languageCode)
  {
    $errors = [];
    $languageHelper = new LanguageHelper($languageCode, $templateConfig);

    // Sanitize input
    $data[$fieldKey] = SecurityHelper::sanitize($data[$fieldKey] ?? null);

    // 1. General validations
    $errors = array_merge(
        $errors,
        self::validateRequired($fieldKey, $fieldConfig, $data, $languageHelper),
        self::validateMinLength($fieldKey, $fieldConfig, $data, $languageHelper),
        self::validateMaxLength($fieldKey, $fieldConfig, $data, $languageHelper),
        self::validateDataType($fieldKey, $data[$fieldKey], $fieldConfig['type'], $languageHelper)
    );

    // If errors are found, stop further validation
    if (!empty($errors)) {
        return $errors;
    }

    // CAPTCHA validation (only if configured)
    if (isset($templateConfig['captcha']) && 
        isset($templateConfig['captcha']['frontend']['fieldname']) && 
        $fieldKey === $templateConfig['captcha']['frontend']['fieldname']) {
        
        $captchaErrors = self::validateCaptcha($data, $templateConfig, $languageHelper);
        
        // If CAPTCHA errors are found, stop further validation
        if (!empty($captchaErrors)) {
            return $captchaErrors;
        }
        
        // If CAPTCHA is valid, no further validation for this field
        return [];
    }

    // 2. Type-specific validations
    switch ($fieldConfig['type']) {
      case 'email':
        if (!empty($data[$fieldKey]) && !SecurityHelper::validateEmail($data[$fieldKey])) {
          $errors[$fieldKey] = $languageHelper->get('validation.fields.email');
        }
        break;

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
          if (!self::isValidDate($dateValue)) {
            $errors[$fieldKey] = $languageHelper->get('validation.fields.date.invalid');
          } else {
            if ($minDate && $dateValue < $minDate) {
              $errors[$fieldKey] = $languageHelper->get('validation.fields.date.min');
            }

            if ($maxDate && $dateValue > $maxDate) {
              $errors[$fieldKey] = $languageHelper->get('validation.fields.date.max');
            }
          }
        }
        break;

      case 'select':
        $selectValue = $data[$fieldKey] ?? null;

        if (!empty($selectValue) && !array_key_exists($selectValue, $fieldConfig['options'])) {
          $errors[$fieldKey] = $languageHelper->get('validation.fields.invalid_option');
        }
        break;

      case 'tel':
        $telValue = $data[$fieldKey] ?? null;
        
        if (!empty($telValue)) {
          $pattern = $fieldConfig['pattern'] ?? '^[0-9+\-\s()]{8,}$';
          if (!preg_match("/$pattern/", $telValue)) {
            $errors[$fieldKey] = $languageHelper->get('validation.fields.phone');
          }
        }
        break;

      case 'file':
        if (!empty($data[$fieldKey]) && is_array($data[$fieldKey])) {
            foreach ($data[$fieldKey] as $file) {
                $fileErrors = FileValidationHelper::validateFile($file, $fieldConfig, $languageCode);
                if (!empty($fileErrors)) {
                    $errors[$fieldKey] = $fileErrors['error'] ?? $languageHelper->get('validation.fields.file.unknown_error');
                    break;
                }
            }
        } else {
            $errors[$fieldKey] = $languageHelper->get('validation.fields.file.no_file_uploaded');
        }
        break;

      case 'number':
        $numberValue = $data[$fieldKey] ?? null;
        
        if (!empty($numberValue)) {
          if (!is_numeric($numberValue)) {
            $errors[$fieldKey] = $languageHelper->get('validation.fields.number.invalid');
            break;
          }

          $number = floatval($numberValue);
          if (isset($fieldConfig['min']) && $number < $fieldConfig['min']) {
            $errors[$fieldKey] = str_replace(
              ':min',
              $fieldConfig['min'],
              $languageHelper->get('validation.fields.number.too_small')
            );
          }
          if (isset($fieldConfig['max']) && $number > $fieldConfig['max']) {
            $errors[$fieldKey] = str_replace(
              ':max',
              $fieldConfig['max'],
              $languageHelper->get('validation.fields.number.too_large')
            );
          }
        }
        break;

      case 'radio':
        $radioValue = $data[$fieldKey] ?? null;
        
        if (!empty($radioValue)) {
            if (!array_key_exists($radioValue, $fieldConfig['options'])) {
                $errors[$fieldKey] = $languageHelper->get('validation.fields.option');
            }
        }
        break;

      case 'date-range':
        $startDate = $data[$fieldKey]['start'] ?? null;
        $endDate = $data[$fieldKey]['end'] ?? null;

        if (!empty($startDate) || !empty($endDate)) {
            if (!self::isValidDate($startDate) || !self::isValidDate($endDate)) {
                $errors[$fieldKey] = $languageHelper->get('validation.fields.date.invalid');
                break;
            }

            if (strtotime($startDate) > strtotime($endDate)) {
                $errors[$fieldKey] = $languageHelper->get('validation.fields.date.invalid_range');
                break;
            }

            if (isset($fieldConfig['min'])) {
                $minDate = date('Y-m-d', strtotime($fieldConfig['min']));
                if (strtotime($startDate) < strtotime($minDate)) {
                    $errors[$fieldKey] = $languageHelper->get('validation.fields.date.before_min');
                }
            }

            if (isset($fieldConfig['max'])) {
                $maxDate = date('Y-m-d', strtotime($fieldConfig['max']));
                if (strtotime($endDate) > strtotime($maxDate)) {
                    $errors[$fieldKey] = $languageHelper->get('validation.fields.date.after_max');
                }
            }
        }
        break;

      case 'time':
        $timeValue = $data[$fieldKey] ?? null;

        if (!empty($timeValue)) {
            if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $timeValue)) {
                $errors[$fieldKey] = $languageHelper->get('validation.fields.time.invalid');
                break;
            }

            if (isset($fieldConfig['min'])) {
                $minTime = strtotime('1970-01-01 ' . $fieldConfig['min']);
                $currentTime = strtotime('1970-01-01 ' . $timeValue);
                if ($currentTime < $minTime) {
                    $errors[$fieldKey] = $languageHelper->get('validation.fields.time.before_min');
                }
            }

            if (isset($fieldConfig['max'])) {
                $maxTime = strtotime('1970-01-01 ' . $fieldConfig['max']);
                $currentTime = strtotime('1970-01-01 ' . $timeValue);
                if ($currentTime > $maxTime) {
                    $errors[$fieldKey] = $languageHelper->get('validation.fields.time.after_max');
                }
            }
        }
        break;

      case 'url':
        if (!empty($data[$fieldKey])) {
            if (!SecurityHelper::validateUrl($data[$fieldKey])) {
                $errors[$fieldKey] = $languageHelper->get('validation.fields.url');
            }
        }
        break;
    }

    return $errors;
  }

  /**
   * Validates if a field is required.
   *
   * @param string $fieldKey The key of the field to validate.
   * @param array $fieldConfig The configuration of the field.
   * @param array $data The form data.
   * @param LanguageHelper $languageHelper The language helper instance.
   * @return array The validation errors. 
   */
  public static function validateRequired($fieldKey, $fieldConfig, $data, $languageHelper)
  {
    $errors = [];
    if (!empty($fieldConfig['required']) && empty($data[$fieldKey])) {
      $errors[$fieldKey] = $languageHelper->get('validation.fields.required');
    }
    return $errors;
  }

  /**
   * Validates the minimum length of a field.
   *
   * @param string $fieldKey The key of the field to validate.
   * @param array $fieldConfig The configuration of the field.
   * @param array $data The form data.
   * @param LanguageHelper $languageHelper The language helper instance.
   * @return array The validation errors. 
   */
  public static function validateMinLength($fieldKey, $fieldConfig, $data, $languageHelper)
  {
    $errors = [];
    if (!empty($data[$fieldKey]) && !empty($fieldConfig['minlength'])) {
      $minLength = $fieldConfig['minlength'];
      if (strlen($data[$fieldKey]) < $minLength) {
        $errors[$fieldKey] = str_replace(':minlength', $minLength, $languageHelper->get('validation.fields.message.too_short'));
      }
    }
    return $errors;
  }

  /**
   * Validates the maximum length of a field.
   *
   * @param string $fieldKey The key of the field to validate.
   * @param array $fieldConfig The configuration of the field.
   * @param array $data The form data.
   * @param LanguageHelper $languageHelper The language helper instance.
   * @return array The validation errors. 
   */
  public static function validateMaxLength($fieldKey, $fieldConfig, $data, $languageHelper)
  {
    $errors = [];
    if (!empty($data[$fieldKey]) && !empty($fieldConfig['maxlength'])) {
      $maxLength = $fieldConfig['maxlength'];
      if (strlen($data[$fieldKey]) > $maxLength) {
        $errors[$fieldKey] = str_replace(':maxlength', $maxLength, $languageHelper->get('validation.fields.message.too_long'));
      }
    }
    return $errors;
  }

  /**
   * Validates the CAPTCHA.
   *
   * @param array $data The form data.
   * @param array $templateConfig The template configuration.
   * @param LanguageHelper $languageHelper The language helper instance.
   * @return array The validation errors.
   */
  public static function validateCaptcha($data, $templateConfig, $languageHelper) 
  {
    $errors = [];
    
    if (!isset($templateConfig['captcha'])) {
        return $errors;
    }

    $captchaConfig = $templateConfig['captcha'];
    $fieldName = $captchaConfig['frontend']['fieldname'] ?? 'captcha-response';

    if (empty($data[$fieldName])) {
        $errors[$fieldName] = $languageHelper->get('captcha.error.missing');
        return $errors;
    }

    if (!kirby()->option('philippoehrlein.kirby-email-manager.captcha.callback')) {
        error_log('CAPTCHA validation callback not configured');
        return $errors; 
    }
    $validateCallback = kirby()->option('philippoehrlein.kirby-email-manager.captcha.callback');
    
    if (!is_callable($validateCallback)) {
        return $errors;
    }

    if (!$validateCallback($data[$fieldName], $captchaConfig)) {
        $errors[$fieldName] = $languageHelper->get('captcha.error.invalid');
    }

    return $errors;
  }

  private static function isValidDate($date): bool
  {
    if (!is_string($date)) {
        return false;
    }
    
    $dateFormat = kirby()->option('date.handler.format', 'Y-m-d');
    
    $d = DateTime::createFromFormat($dateFormat, $date);
    return $d && $d->format($dateFormat) === $date;
  }

  private static function validateDataType(string $fieldKey, $value, string $type, LanguageHelper $languageHelper): array 
  {
    $errors = [];
    
    switch($type) {
        case 'email':
            if (!is_string($value) || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$fieldKey] = $languageHelper->get('validation.fields.email');
            }
            break;
            
        case 'number':
            if (!is_numeric($value)) {
                $errors[$fieldKey] = $languageHelper->get('validation.fields.number');
            }
            break;
            
        case 'tel':
            if (!is_string($value) || !preg_match('/^[+\d\s()\-]+$/', $value)) {
                $errors[$fieldKey] = $languageHelper->get('validation.fields.tel');
            }
            break;
            
        case 'date':
            if (!self::isValidDate($value)) {
                $errors[$fieldKey] = $languageHelper->get('validation.fields.date.invalid');
            }
            break;
            
        case 'date-range':
            if (!is_array($value) || 
                !isset($value['start'], $value['end']) || 
                !self::isValidDate($value['start']) || 
                !self::isValidDate($value['end'])) {
                $errors[$fieldKey] = $languageHelper->get('validation.fields.date_range.invalid');
            } elseif ($value['start'] > $value['end']) {
                $errors[$fieldKey] = $languageHelper->get('validation.fields.date_range.start_after_end');
            }
            break;
            
        case 'select':
            if (!is_string($value) && !is_array($value)) {
                $errors[$fieldKey] = $languageHelper->get('validation.fields.select');
            }
            break;
            
        case 'radio':
            if (!is_string($value)) {
                $errors[$fieldKey] = $languageHelper->get('validation.fields.radio');
            }
            break;
            
        case 'textarea':
        case 'text':
            if (!is_string($value)) {
                $errors[$fieldKey] = $languageHelper->get('validation.fields.text');
            }
            break;
            
        case 'url':
            if (!is_string($value) || !filter_var($value, FILTER_VALIDATE_URL)) {
                $errors[$fieldKey] = $languageHelper->get('validation.fields.url');
            }
            break;
    }
    
    return $errors;
  }
}
