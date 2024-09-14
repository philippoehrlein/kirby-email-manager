<?php
  $base_class = 'input';
  $type = 'email';
  $modifier = $base_class . '--' . $type;
  $attributes = [
    'type' => $type,
    'id' => $fieldKey,
    'name' => $fieldKey,
    'placeholder' => $placeholder,
    'class' => $base_class . ' ' . $modifier,
    'value' => $value,
    'required' => $fieldConfig['required'] ?? false
  ];

  if (isset($fieldConfig['validate']) && $fieldConfig['validate'] === 'email') {
    $attributes['pattern'] = '[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}';
  }
?>

<input <?= Html::attr($attributes) ?> />