<?php
$type = 'file';
$base_class = 'input';
$modifier = $base_class . '--' . $type;

$attributes = [
  'type' => $type,
  'id' => $fieldKey,
  'name' => $fieldKey,
  'class' => $base_class . ' ' . $modifier,
  'accept' => implode(',', $fieldConfig['allowed_mimes'] ?? []),
  'required' => $fieldConfig['required'] ?? false
];

$maxSize = $fieldConfig['max_size'] ?? 5242880; // Default to 5MB if not specified
?>

<input <?= Html::attr($attributes) ?> />
<small><?= t('max_file_size', null, 'Max file size: ') . (round($maxSize / 1048576, 2)) . ' MB' ?></small>