<?php
use KirbyEmailManager\Helpers\FormHelper;

foreach ($fieldConfig['options'] as $optionValue => $optionLabel):
  $attributes = [
    'type' => 'checkbox',
    'id' => $fieldKey . '_' . $optionValue,
    'name' => $fieldKey . '[]',
    'class' => $inputClass,
    'value' => $optionValue,
    'required' => $fieldConfig['required'] ?? false,
  ];
  
  if (is_array($value) && in_array($optionValue, $value)) {
    $attributes['checked'] = true;
  }
?>
  <div <?= FormHelper::getClassName('checkbox-option', $config) ?>>
    <input <?= Html::attr($attributes) ?> tabindex="0">
    <label for="<?= $fieldKey . '_' . $optionValue ?>">
      <?= is_array($optionLabel) ? ($optionLabel[$languageCode] ?? $optionValue) : $optionLabel ?>
    </label>
  </div>
<?php endforeach; ?>