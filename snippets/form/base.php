<?php
use KirbyEmailManager\Helpers\FormHelper;

// Annahme: $config wird von form-builder.php übergeben
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

?>

<div class="<?= $fieldClass ?>" style="<?= $spanStyle ?>">
  <label for="<?= $fieldKey ?>" class="<?= $labelClass . ' ' . $requiredClass ?>">
    <?= $fieldConfig['label'][$languageCode] ?>
  </label>
  <?php snippet('email-manager/form/' . $fieldConfig['type'], [
    'fieldKey' => $fieldKey,
    'fieldConfig' => $fieldConfig,
    'value' => $value,
    'placeholder' => $placeholder,
    'languageCode' => $languageCode,
    'inputClass' => $inputClass
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