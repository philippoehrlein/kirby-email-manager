<?php
$type = 'password';
$base_class = 'input';
$modifier = $base_class . '--' . $type;

$attributes = [
  'type' => $type,
  'id' => $fieldKey,
  'name' => $fieldKey,
  'class' => $base_class . ' ' . $modifier,
  'placeholder' => $placeholder,
  'required' => $fieldConfig['required'] ?? false
];

if (!empty($fieldConfig['validate'])) {
  if (strpos($fieldConfig['validate'], 'minLength:') === 0) {
    $minLength = intval(substr($fieldConfig['validate'], 10));
    $attributes['minlength'] = $minLength;
  }
}
?>

<input <?= Html::attr($attributes) ?> />