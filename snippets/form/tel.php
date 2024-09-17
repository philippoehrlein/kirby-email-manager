<?php
$defaultPattern = '^[+]?[(]?[0-9]{1,4}[)]?[-\s.]?[0-9]{1,4}[-\s.]?[0-9]{1,9}$';

$attributes = [
  'type' => 'tel',
  'id' => $fieldKey,
  'name' => $fieldKey,
  'class' => $inputClass,
  'value' => $value,
  'placeholder' => $placeholder,
  'required' => $fieldConfig['required'] ?? false,
  'pattern' => $fieldConfig['pattern'] ?? $defaultPattern
];

if (!empty($fieldConfig['pattern'])) {
  $attributes['pattern'] = $fieldConfig['pattern'];
}
?>

<input <?= Html::attr($attributes) ?> />