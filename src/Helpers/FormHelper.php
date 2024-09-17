<?php

namespace KirbyEmailManager\Helpers;

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
        return round(($numerator / $denominator) * 12);
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
          $span = self::calculateSpan($width ?? '1/1');
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
        if (isset($spans['default']) && $spans['default'] !== 12) {
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
    public static function getClassName(string $element, array $config = [], string $modifier = null): string
    {
        $defaultClasses = [
            'form'    => 'form',
            'grid'    => 'grid',
            'field'   => 'field',
            'label'   => 'label',
            'input'   => 'input',
            'error'   => 'error',
            'button'  => 'button',
            'select'  => 'select',
            'textarea' => 'textarea',
        ];

        $prefix = $config['classPrefix'] ?? 'kem-';
        $customClasses = $config['classes'] ?? [];
        $additionalClasses = $config['additionalClasses'][$element] ?? '';

        $className = $customClasses[$element] ?? $defaultClasses[$element] ?? '';

        if ($prefix !== false && !empty($className) && !in_array($element, $config['noPrefixElements'] ?? [])) {
            $className = $prefix . $className;
        }

        if ($modifier) {
            $className .= ' ' . $className . '--' . $modifier;
        }

        if (!empty($additionalClasses)) {
            $className .= ' ' . $additionalClasses;
        }

        return trim($className);
    }
}