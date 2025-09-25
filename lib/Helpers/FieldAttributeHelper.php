<?php
/**
 * Helper class for managing form field attributes
 * 
 * This class provides methods to generate and manage HTML attributes
 * for form fields like inputs, selects etc.
 * 
 * @author Philipp Oehrlein
 * @version 1.0.0
 */

namespace KirbyEmailManager\Helpers;

class FieldAttributeHelper
{
  /**
   * Retrieves the base attributes for a form field.
   *
   * @param string $fieldKey The key of the field.
   * @param array $fieldConfig The configuration for the field.
   * @param string $inputClass The class for the input element.
   * @param array $commonAttributes Additional common attributes.
   * @return array The base attributes for the field.
   */
  public static function getBaseAttributes(
    string $fieldKey,
    array $fieldConfig,
    string $inputClass,
    array $commonAttributes = []
  ): array {
    return array_merge($commonAttributes, [
      'id' => $fieldKey,
      'name' => $fieldKey,
      'class' => $inputClass,
      'required' => $fieldConfig['required'] ?? false,
      'autofocus' => $fieldConfig['autofocus'] ?? false,
    ]);
  }
  
  /**
   * Retrieves the attributes for a form field based on its type.
   *
   * @param string $type The type of the field.
   * @param array $baseAttributes The base attributes for the field.
   * @param array $fieldConfig The configuration for the field.
   * @param mixed $value The value of the field.
   * @param string $placeholder The placeholder for the field.
   * @return array The attributes for the field.
   */
  public static function getFieldAttributes(
    string $type,
    array $baseAttributes,
    array $fieldConfig,
    $value = null,
    ?string $placeholder = null,
  ): array {
    $attributes = $baseAttributes;
    
    switch($type) {
      case 'email':
        $attributes['type'] = 'email';
        $attributes['placeholder'] = $placeholder;
        $attributes['value'] = $value;
        $attributes['spellcheck'] = 'false';
        if (isset($fieldConfig['validate']) && $fieldConfig['validate'] === 'email') {
          $attributes['pattern'] = '[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}';
        }
        break;

      case 'text':
        $attributes['type'] = 'text';
        $attributes['placeholder'] = $placeholder;
        $attributes['value'] = $value;
        $attributes['spellcheck'] = 'true';
        break;

      case 'tel':
        $attributes['type'] = 'tel';
        $attributes['placeholder'] = $placeholder;
        $attributes['value'] = $value;
        $attributes['pattern'] = $fieldConfig['pattern'] ?? '^[+]?[(]?[0-9]{1,4}[)]?[-\s.]?[0-9]{1,4}[-\s.]?[0-9]{1,9}$';
        break;

      case 'secret':
        $attributes['type'] = 'password';
        $attributes['placeholder'] = $placeholder;
        if (!empty($fieldConfig['validate'])) {
          if (strpos($fieldConfig['validate'], 'minlength:') === 0) {
            $attributes['minlength'] = intval(substr($fieldConfig['validate'], 10));
          }
        }
        break;

      case 'number':
        $attributes['type'] = 'number';
        $attributes['placeholder'] = $placeholder;
        $attributes['value'] = $value;
        if (isset($fieldConfig['min'])) $attributes['min'] = $fieldConfig['min'];
        if (isset($fieldConfig['max'])) $attributes['max'] = $fieldConfig['max'];
        if (isset($fieldConfig['step'])) $attributes['step'] = $fieldConfig['step'];
        break;

      case 'date':
        $attributes['type'] = 'date';
        $attributes['value'] = $value;
        if (isset($fieldConfig['min'])) $attributes['min'] = $fieldConfig['min'];
        if (isset($fieldConfig['max'])) $attributes['max'] = $fieldConfig['max'];
        break;

      case 'time':
        $attributes['type'] = 'time';
        $attributes['value'] = $value;
        if (isset($fieldConfig['min'])) $attributes['min'] = $fieldConfig['min'];
        if (isset($fieldConfig['max'])) $attributes['max'] = $fieldConfig['max'];
        if (isset($fieldConfig['step'])) $attributes['step'] = $fieldConfig['step'];
        break;

      case 'file':
        $attributes['type'] = 'file';
        
        // Support extensions for accept attribute
        if (isset($fieldConfig['extension']) && is_array($fieldConfig['extension'])) {
          // Convert file extensions to accept attribute format
          $acceptTypes = [];
          foreach ($fieldConfig['extension'] as $type) {
            $type = strtolower(trim($type));
            // Add dot prefix for accept attribute
            $acceptTypes[] = '.' . $type;
          }
          $attributes['accept'] = implode(',', $acceptTypes);
        }
        
        $attributes['data-max-files'] = $fieldConfig['max'] ?? 1;
        $attributes['data-max-size'] = $fieldConfig['maxsize'] ?? 5242880;
        if (($fieldConfig['max'] ?? 1) > 1) {
          $attributes['multiple'] = true;
          $attributes['name'] .= '[]';
        }
        break;

      case 'radio':
        $attributes['type'] = 'radio';
        if ($fieldConfig['required'] && empty($value)) {
          $value = array_key_first($fieldConfig['options']);
        }
        $attributes['value'] = $value;
        if (isset($attributes['value']) && $value === $attributes['value']) {
          $attributes['checked'] = true;
        }
        break;

      case 'checkbox':
        $attributes['type'] = 'checkbox';
        $attributes['name'] .= '[]';
        $attributes['value'] = $value;
        if (isset($attributes['value']) && is_array($value) && in_array($attributes['value'], $value)) {
          $attributes['checked'] = true;
        }
        break;

      case 'select':
        if (!$fieldConfig['required']) {
          $attributes['placeholder'] = $placeholder;
        }
        break;
      
      case 'date-range':
        $attributes['type'] = 'date';
        if (isset($fieldConfig['min'])) $attributes['min'] = $fieldConfig['min'];
        if (isset($fieldConfig['max'])) $attributes['max'] = $fieldConfig['max'];
        
        $attributes['start'] = [
          'id' => $attributes['id'] . '[start]',
          'name' => $attributes['name'] . '[start]',
          'value' => $value['start'] ?? '',
          'type' => 'date',
          'class' => $attributes['class'],
          'required' => $attributes['required'],
          'min' => $attributes['min'] ?? null,
          'max' => $attributes['max'] ?? null
        ];
        
        $attributes['end'] = [
          'id' => $attributes['id'] . '[end]',
          'name' => $attributes['name'] . '[end]',
          'value' => $value['end'] ?? '',
          'type' => 'date',
          'class' => $attributes['class'],
          'required' => $attributes['required'],
          'min' => $attributes['min'] ?? null,
          'max' => $attributes['max'] ?? null
        ];
        break;
      
      case 'textarea':
        $attributes['placeholder'] = $placeholder;
        $attributes['rows'] = $fieldConfig['rows'] ?? 6;
        $attributes['spellcheck'] = 'true';

        $resizeStyle = $fieldConfig['resizable'] ?? 'none';
        $attributes['style'] = "resize: $resizeStyle;";
        
        $attributes['value'] = $value;
        break;
    }
    
    return $attributes;
  }
}