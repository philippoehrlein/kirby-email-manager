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
  /**
   * Validates the template configuration.
   *
   * @param array $templateConfig The template configuration to validate.
   * @throws Exception If the template configuration is empty or missing required keys.
   */
  public static function validateTemplateConfig($templateConfig)
  {
      if (empty($templateConfig)) {
          throw new Exception(t('error_messages.template_config_empty'));
      }

      $requiredKeys = ['fields', 'templates', 'buttons', 'emails'];
      foreach ($requiredKeys as $key) {
          if (!isset($templateConfig[$key])) {
              $message = t('error_messages.missing_required_key');
              $message = str_replace('{key}', $key, $message);
              throw new Exception($message);
          }
      }

      self::validateFields($templateConfig['fields']);
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
                    throw new Exception(t('error_messages.field_missing_property', "Missing '{$property}' for field '{$fieldKey}' in template configuration."));
                }
            }
        }
    }
}