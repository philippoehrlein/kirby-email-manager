<?php
  $attributes = [
    'id' => $fieldKey,
    'name' => $fieldKey,
    'placeholder' => $placeholder,
    'class' => $inputClass,
    'value' => $value,
    'required' => $fieldConfig['required'] ?? false
  ];

  if (isset($fieldConfig['validate']) && $fieldConfig['validate'] === 'email') {
    $attributes['pattern'] = '[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}';
  }
?>

<input <?= Html::attr(array_map('htmlspecialchars', $attributes)) ?> />