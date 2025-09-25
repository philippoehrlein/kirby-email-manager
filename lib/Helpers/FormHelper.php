<?php

namespace KirbyEmailManager\Helpers;

use KirbyEmailManager\Helpers\LanguageHelper;

/**
 * FormHelper
 *
 * @author    Philipp Oehrlein
 * @version   1.0.0
 *
 */
class FormHelper
{
    /**
     * Calculate the span for the grid based on the width
     *
     * @param string $width
     * @return int
     */
    public static function calculateSpan(string $width): int
    {
        $fractions = explode('/', $width);
        if (count($fractions) !== 2) {
            return 12; // Default to full width
        }
        $numerator = (int)$fractions[0];
        $denominator = (int)$fractions[1];
        if ($denominator === 0) {
            return 12; // Avoid division by zero
        }
        return (int)round(($numerator / $denominator) * 12);
    }

    /**
     * Get the responsive span for the grid based on the width
     *
     * @param string|array $width
     * @return array
     */
    public static function getResponsiveSpan($width): array
    {
      $breakpoints = ['default', 'medium', 'large'];
      $spans = [];
  
      if (is_array($width)) {
          foreach ($breakpoints as $breakpoint) {
              $spans[$breakpoint] = self::calculateSpan($width[$breakpoint] ?? $width['default'] ?? '1/1');
          }
      } else {
          $span = self::calculateSpan($width);
          foreach ($breakpoints as $breakpoint) {
              $spans[$breakpoint] = $span;
          }
      }
  
      return $spans;
    }

    /**
     * Generate the span styles for the grid
     *
     * @param array $spans
     * @return string
     */
    public static function generateSpanStyles(array $spans): string
    {
        $styles = [];
        if (isset($spans['default'])) {
            $styles[] = "--span:" . $spans['default'];
        }
        if (isset($spans['medium']) && $spans['medium'] !== $spans['default']) {
            $styles[] = "--span-medium:" . $spans['medium'];
        }
        if (isset($spans['large']) && $spans['large'] !== $spans['medium']) {
            $styles[] = "--span-large:" . $spans['large'];
        }

        return implode('; ', $styles);
    }

    /**
     * Get configurable class names
     *
     * @param string $element
     * @param array $config
     * @return string
     */
    public static function getClassName(string $element, array $config = [], ?string $modifier = null): string
    {
        $prefix = $config['classPrefix'] ?? 'kem-';
        $className = $config['classes'][$element] ?? $element;
        $additionalClasses = $config['additionalClasses'][$element] ?? '';
        
        if ($prefix !== false) {
            $className = $prefix . $className;
        }
        if ($modifier !== null) {
            $modifier = $config['classModifiers'][$element][$modifier] ?? $modifier;
            $className .= ' ' . $className . '--' . $modifier;
        }

        if (!empty($additionalClasses)) {
            $className .= ' ' . $additionalClasses;
        }

        return trim($className);
    }

    /**
     * Get the display value for a select field
     *
     * @param string $fieldKey The field key
     * @param string $selectedValue The selected value
     * @param array $templateConfig The template configuration
     * @param LanguageHelper $languageHelper The language helper
     * @return string The display value
     */
    public static function getSelectDisplayValue(string $fieldKey, string $selectedValue, array $templateConfig, LanguageHelper $languageHelper): string
    {
        // Get options from template config
        $options = $templateConfig['fields'][$fieldKey]['options'] ?? [];
        
        if (empty($options) || !isset($options[$selectedValue])) {
            return $selectedValue; // Fallback to original value
        }

        // Get the label (can be string or map)
        $label = $options[$selectedValue];
        
        // Use LanguageHelper's getTranslatedValue to handle string/map
        return LanguageHelper::getTranslatedValue($label, $languageHelper->getLanguage(), 'en') ?? $selectedValue;
    }
}