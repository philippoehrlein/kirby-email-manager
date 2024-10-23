<?php
foreach ($fieldConfig['options'] as $optionValue => $optionLabel):
  $attributes = [
    'type' => 'radio',
    'id' => $fieldKey . '_' . $optionValue,
    'name' => $fieldKey,
    'class' => $inputClass,
    'value' => $optionValue,
    'required' => $fieldConfig['required'] ?? false
  ];

  if ($fieldConfig['required'] && empty($value)) {
    $value = array_key_first($fieldConfig['options']);
  }
  
  if ($optionValue === $value) {
    $attributes['checked'] = true;
  }
?>
  <div class="radio-option">
    <input <?= Html::attr($attributes) ?> tabindex="0">
    <label for="<?= $fieldKey . '_' . $optionValue ?>">
      <?= is_array($optionLabel) ? ($optionLabel[$languageCode] ?? $optionValue) : $optionLabel ?>
    </label>
  </div>
<?php endforeach; ?>