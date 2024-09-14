<?php
$type = 'textarea';
$base_class = $type;
$resizeStyle = $fieldConfig['resizable'] ?? 'none';

$attributes = [
  'id' => $fieldKey,
  'name' => $fieldKey,
  'class' => $base_class,
  'placeholder' => $placeholder,
  'required' => $fieldConfig['required'] ?? false,
  'rows' => $fieldConfig['rows'] ?? 6,
  'style' => "resize: $resizeStyle;"
];

if (!empty($fieldConfig['validate'])) {
  if (strpos($fieldConfig['validate'], 'minLength:') === 0) {
    $minLength = intval(substr($fieldConfig['validate'], 10));
    $attributes['minlength'] = $minLength;
  }
}
?>

<textarea <?= Html::attr($attributes) ?>><?= htmlspecialchars($value) ?></textarea>