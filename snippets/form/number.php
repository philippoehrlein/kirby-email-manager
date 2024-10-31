<?php
$attributes = array_merge($commonAttributes, [
  'type' => $type,
  'id' => $fieldKey,
  'name' => $fieldKey,
  'class' => $inputClass,
  'value' => $value,
  'placeholder' => $placeholder,
  'required' => $fieldConfig['required'] ?? false,
]);

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