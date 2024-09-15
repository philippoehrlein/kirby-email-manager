<?php
$attributes = [
  'id' => $fieldKey,
  'name' => $fieldKey,
  'class' => $inputClass,
  'required' => $fieldConfig['required'] ?? false
];

$isRequired = $fieldConfig['required'] ?? false;

?>

<select <?= Html::attr($attributes) ?>>
  <?php if (isset($fieldConfig['placeholder'])): ?>
    <option value="" <?= ($value === '') ? 'selected' : '' ?> 
            <?= $isRequired ? 'disabled' : '' ?> 
            class="placeholder">
      <?= $fieldConfig['placeholder'][$languageCode] ?? '' ?>
    </option>
  <?php endif; ?>

  <?php foreach ($fieldConfig['options'] as $optionValue => $optionLabel): ?>
    <option value="<?= $optionValue ?>" <?= $optionValue === ($value ?? '') ? 'selected' : '' ?>>
      <?= is_array($optionLabel) ? ($optionLabel[$languageCode] ?? $optionValue) : $optionLabel ?>
    </option>
  <?php endforeach; ?>
</select>