<?php
$type = 'number';
$base_class = 'input';
$modifier = $base_class . '--' . $type;

$attributes = [
  'type' => $type,
  'id' => $fieldKey,
  'name' => $fieldKey,
  'class' => $base_class . ' ' . $modifier,
  'value' => $value,
  'placeholder' => $placeholder,
  'required' => $fieldConfig['required'] ?? false
];

if (isset($fieldConfig['min'])) {
  $attributes['min'] = $fieldConfig['min'];
}

if (isset($fieldConfig['max'])) {
  $attributes['max'] = $fieldConfig['max'];
}

if (isset($fieldConfig['step'])) {
  $attributes['step'] = $fieldConfig['step'];
}
?>

<input <?= Html::attr($attributes) ?> />