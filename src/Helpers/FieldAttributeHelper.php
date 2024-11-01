<?php

namespace KirbyEmailManager\Helpers;

class FieldAttributeHelper
{
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
    ]);
  }
  
  public static function getFieldAttributes(
    string $type,
    array $baseAttributes,
    array $fieldConfig,
    $value = null,
    ?string $placeholder = null,
    ?string $languageCode = null
  ): array {
    $attributes = $baseAttributes;
    
    switch($type) {
      case 'email':
        $attributes['type'] = 'email';
        $attributes['placeholder'] = $placeholder;
        $attributes['value'] = $value;
        if (isset($fieldConfig['validate']) && $fieldConfig['validate'] === 'email') {
          $attributes['pattern'] = '[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}';
        }
        break;

      case 'text':
        $attributes['type'] = 'text';
        $attributes['placeholder'] = $placeholder;
        $attributes['value'] = $value;
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
          if (strpos($fieldConfig['validate'], 'minLength:') === 0) {
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
        $attributes['accept'] = implode(',', $fieldConfig['allowed_mimes'] ?? []);
        $attributes['data-max-files'] = $fieldConfig['max_files'] ?? 1;
        $attributes['data-max-size'] = $fieldConfig['max_size'] ?? 5242880;
        if (($fieldConfig['max_files'] ?? 1) > 1) {
          $attributes['multiple'] = true;
          $attributes['name'] .= '[]';
        }
        break;

      case 'radio':
        $attributes['type'] = 'radio';
        if ($fieldConfig['required'] && empty($value)) {
          $value = array_key_first($fieldConfig['options']);
        }
        if (isset($attributes['value']) && $value === $attributes['value']) {
          $attributes['checked'] = true;
        }
        break;

      case 'checkbox':
        $attributes['type'] = 'checkbox';
        $attributes['name'] .= '[]';
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
        
        // Start-Attribute
        $attributes['start'] = [
          'id' => $attributes['id'] . '_start',
          'name' => $attributes['name'] . '_start',
          'value' => $value['start'] ?? '',
          'type' => 'date',
          'class' => $attributes['class'],
          'required' => $attributes['required'],
          'min' => $attributes['min'] ?? null,
          'max' => $attributes['max'] ?? null
        ];
        
        // End-Attribute
        $attributes['end'] = [
          'id' => $attributes['id'] . '_end',
          'name' => $attributes['name'] . '_end',
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
        
        $resizeStyle = $fieldConfig['resizable'] ?? 'none';
        $attributes['style'] = "resize: $resizeStyle;";
        
        if (!empty($fieldConfig['validate'])) {
          if (strpos($fieldConfig['validate'], 'minLength:') === 0) {
            $attributes['minlength'] = intval(substr($fieldConfig['validate'], 10));
          }
        }
        break;
    }
    
    return $attributes;
  }
}