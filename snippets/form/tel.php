<?php
$type = 'tel';
$base_class = 'input';
$modifier = $base_class . '--' . $type;

$defaultPattern = '^[+]?[(]?[0-9]{1,4}[)]?[-\s.]?[0-9]{1,4}[-\s.]?[0-9]{1,9}$';

$attributes = [
  'type' => $type,
  'id' => $fieldKey,
  'name' => $fieldKey,
  'class' => $base_class . ' ' . $modifier,
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