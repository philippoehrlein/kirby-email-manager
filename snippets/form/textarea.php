<?php
$attributes = array_merge($commonAttributes, [
  'id' => $fieldKey,
  'name' => $fieldKey,
  'class' => $inputClass,
  'placeholder' => $placeholder,
  'required' => $fieldConfig['required'] ?? false,
  'rows' => $fieldConfig['rows'] ?? 6,
]);

$resizeStyle = $fieldConfig['resizable'] ?? 'none';
$attributes['style'] = "resize: $resizeStyle;";

if (!empty($fieldConfig['validate'])) {
  if (strpos($fieldConfig['validate'], 'minLength:') === 0) {
    $minLength = intval(substr($fieldConfig['validate'], 10));
    $attributes['minlength'] = $minLength;
  }
}
?>

<textarea <?= Html::attr($attributes) ?>><?= htmlspecialchars($value) ?></textarea>