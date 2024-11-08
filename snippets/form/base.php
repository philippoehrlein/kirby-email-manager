<?php
use KirbyEmailManager\Helpers\FormHelper;
use KirbyEmailManager\Helpers\FieldHelper;


$config = $config ?? [];

// Set up CSS classes based on configuration
$fieldClass = FieldHelper::getFieldClassName('field', $config, $fieldConfig['type']);
$labelClass = FieldHelper::getFieldClassName('label', $config);
$inputClass = FieldHelper::getFieldClassName('input', $config);

// Add specific classes for text fields
if (in_array($fieldConfig['type'], ['text', 'email', 'tel'])) {
    $inputClass .= ' ' . FieldHelper::getFieldClassName('input', $config, $fieldConfig['type']);
} elseif (in_array($fieldConfig['type'], ['select', 'textarea'])) {
    $inputClass = FieldHelper::getFieldClassName($fieldConfig['type'], $config);
}

// Define layout and span styles
$span = FormHelper::getResponsiveSpan($fieldConfig['width'] ?? '1/1');
$spanStyle = FormHelper::generateSpanStyles($span);

// Add required field class
$isRequired = $fieldConfig['required'] ?? false;
$requiredClass = $isRequired ? 'is-required' : '';

// Prepare additional attributes
$commonAttributes = [];
if (isset($fieldConfig['title'])) {
    $commonAttributes['title'] = $fieldConfig['title'][$languageCode] ?? $fieldConfig['error_message'][$languageCode] ?? '';
}
if (isset($fieldConfig['aria-label'])) {
    $commonAttributes['aria-label'] = $fieldConfig['aria-label'][$languageCode] ?? $fieldConfig['error_message'][$languageCode] ?? '';
}

// Generate attributes
$attributes = FieldHelper::prepareFieldAttributes(
    $fieldConfig,
    $fieldKey,
    $inputClass,
    $value,
    $placeholder,
    $languageCode,
    $commonAttributes
);
?>

<div class="<?= $fieldClass ?>" style="<?= $spanStyle ?>">
  <label for="<?= $fieldKey ?>" class="<?= $labelClass . ' ' . $requiredClass ?>">
    <?= $fieldConfig['label'][$languageCode] ?>
  </label>

  <?php snippet('email-manager/form/' . $fieldConfig['type'], [
    'field' => $attributes,
    'value' => $value,
    'options' => $attributes->options ?? []
  ]); ?>

  <?php if (isset($fieldConfig['helper_text'][$languageCode])): ?>
    <p class="<?= FieldHelper::getFieldClassName('helper-text', $config) ?>">
      <?= $fieldConfig['helper_text'][$languageCode] ?>
    </p>
  <?php endif; ?>

  <?php if (isset($error)): ?>
    <p class="<?= FieldHelper::getFieldClassName('error', $config) ?>"><?= $error ?></p>
  <?php endif ?>
</div>