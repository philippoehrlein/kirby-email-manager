<?php
use KirbyEmailManager\Helpers\FormHelper;
$maxFiles = $fieldConfig['max_files'] ?? 1;
$maxSize = $fieldConfig['max_size'] ?? 5242880; // Default to 5MB if not specified
$allowedMimes = $fieldConfig['allowed_mimes'] ?? [];

$attributes = array_merge($commonAttributes, [
  'type' => 'file',
  'id' => $fieldKey,
  'name' => $fieldKey . ($maxFiles > 1 ? '[]' : ''),
  'class' => 'input-file',
  'accept' => implode(',', $allowedMimes),
  'required' => $fieldConfig['required'] ?? false,
  'data-max-files' => $maxFiles,
  'data-max-size' => $maxSize,
]);

if ($maxFiles > 1) {
  $attributes['multiple'] = true;
}
?>

<input <?= Html::attr($attributes) ?> />