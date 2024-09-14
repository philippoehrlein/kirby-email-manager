<?php
$type = 'radio';
$base_class = $type;

foreach ($fieldConfig['options'] as $optionValue => $optionLabel):
  $attributes = [
    'type' => $type,
    'id' => $fieldKey . '_' . $optionValue,
    'name' => $fieldKey,
    'class' => $base_class,
    'value' => $optionValue,
    'required' => $fieldConfig['required'] ?? false
  ];

  if ($optionValue === ($value ?? '')) {
    $attributes['checked'] = true;
  }
?>
  <div class="radio-option">
    <input <?= Html::attr($attributes) ?>>
    <label for="<?= $fieldKey . '_' . $optionValue ?>">
      <?= is_array($optionLabel) ? ($optionLabel[$languageCode] ?? $optionValue) : $optionLabel ?>
    </label>
  </div>
<?php endforeach; ?>