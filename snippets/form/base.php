<?php
use KirbyEmailManager\Helpers\FormHelper;
use KirbyEmailManager\Helpers\FieldAttributeHelper;

$config = $config ?? [];

$fieldClass = FormHelper::getClassName('field', $config, $fieldConfig['type']);
$labelClass = FormHelper::getClassName('label', $config);
$inputClass = FormHelper::getClassName('input', $config);

$inputClass = FormHelper::getClassName('input', $config);

if ($fieldConfig['type'] === 'text' || $fieldConfig['type'] === 'email' || $fieldConfig['type'] === 'tel') {
    $inputClass .= ' ' . FormHelper::getClassName('input', $config, $fieldConfig['type']);
} elseif ($fieldConfig['type'] === 'select' || $fieldConfig['type'] === 'textarea') {
    $inputClass = FormHelper::getClassName($fieldConfig['type'], $config);
}
if ($fieldConfig['type'] === 'textarea') {
    $inputClass .= ' ' . FormHelper::getClassName('input', $config);
}

$span = FormHelper::getResponsiveSpan($fieldConfig['width'] ?? '1/1');
$spanStyle = FormHelper::generateSpanStyles($span);

$isRequired = $fieldConfig['required'] ?? false;
$requiredClass = $isRequired ? 'is-required' : '';

$commonAttributes = [];
if(isset($fieldConfig['title'])) {
  $commonAttributes['title'] = $fieldConfig['title'][$languageCode] ?? $fieldConfig['error_message'][$languageCode] ?? '';
}

if(isset($fieldConfig['aria-label'])) {
  $commonAttributes['aria-label'] = $fieldConfig['aria-label'][$languageCode] ?? $fieldConfig['error_message'][$languageCode] ?? '';
}

$attributes = FieldAttributeHelper::getFieldAttributes(
  $fieldConfig['type'],
  FieldAttributeHelper::getBaseAttributes($fieldKey, $fieldConfig, $inputClass, $commonAttributes),
  $fieldConfig,
  $value,
  $placeholder,
  $languageCode
);

?>

<div class="<?= $fieldClass ?>" style="<?= $spanStyle ?>">
  <label for="<?= $fieldKey ?>" class="<?= $labelClass . ' ' . $requiredClass ?>">
    <?= $fieldConfig['label'][$languageCode] ?>
  </label>
  <?php snippet('email-manager/form/' . $fieldConfig['type'], [
    'attributes' => $attributes,
    'fieldConfig' => $fieldConfig,
    'languageCode' => $languageCode,
    'config' => $config
  ]); ?>

  <?php if (isset($fieldConfig['helper_text'][$languageCode])): ?>
    <p class="<?= FormHelper::getClassName('helper-text', $config) ?>">
      <?= $fieldConfig['helper_text'][$languageCode] ?>
    </p>
  <?php endif; ?>
  
  <?php if (isset($error)): ?>
    <p class="<?= FormHelper::getClassName('error', $config) ?>"><?= $error ?></p>
  <?php endif ?>
</div>