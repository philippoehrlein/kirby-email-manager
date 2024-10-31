<?php

namespace KirbyEmailManager\Helpers;

class FieldAttributeHelper
{
  /**
   * Generiert die Basis-Attribute für ein Formularfeld
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
    ]);
  }

  /**
   * Fügt feldspezifische Attribute hinzu
   */
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
    }
    
    return $attributes;
  }
}