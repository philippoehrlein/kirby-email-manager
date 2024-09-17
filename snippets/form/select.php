<?php
$attributes = [
  'id' => $fieldKey,
  'name' => $fieldKey,
  'class' => $inputClass,
];

if (isset($fieldConfig['required']) && $fieldConfig['required'] === true) {
  $attributes['required'] = true;
}

$isRequired = isset($fieldConfig['required']) && $fieldConfig['required'] === true;

?>

<select <?= Html::attr($attributes) ?>>
  <?php if (!$isRequired): ?>
    <option value=""></option>
  <?php endif; ?>

  <?php foreach ($fieldConfig['options'] as $optionValue => $optionLabel): ?>
    <option value="<?= $optionValue ?>" <?= $optionValue === ($value ?? '') ? 'selected' : '' ?>>
      <?= is_array($optionLabel) ? ($optionLabel[$languageCode] ?? $optionValue) : $optionLabel ?>
    </option>
  <?php endforeach; ?>
</select>