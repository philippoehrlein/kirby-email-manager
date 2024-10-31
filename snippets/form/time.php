<?php
$attributes = array_merge($commonAttributes, [
  'type' => 'time',
  'id' => $fieldKey,
  'name' => $fieldKey,
  'class' => $inputClass,
  'value' => $value,
  'required' => $fieldConfig['required'] ?? false,
  'min' => $fieldConfig['min'] ?? null,
  'max' => $fieldConfig['max'] ?? null,
  'step' => $fieldConfig['step'] ?? null,
]);
?>

<input <?= Html::attr($attributes) ?> />