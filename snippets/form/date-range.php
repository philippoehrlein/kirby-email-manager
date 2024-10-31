<?php
use KirbyEmailManager\Helpers\FormHelper;

$attributes = [
  'type' => 'date',
  'class' => $inputClass,
  'required' => $fieldConfig['required'] ?? false
];

if (isset($fieldConfig['min'])) {
  $attributes['min'] = $fieldConfig['min'];
}

if (isset($fieldConfig['max'])) {
  $attributes['max'] = $fieldConfig['max'];
}

$startAttributes = array_merge($attributes, [
  'id' => $fieldKey . '_start',
  'name' => $fieldKey . '_start',
  'placeholder' => $fieldConfig['placeholder_start'] ?? '',
  'value' => $value['start'] ?? ''
]);

$endAttributes = array_merge($attributes, [
  'id' => $fieldKey . '_end',
  'name' => $fieldKey . '_end', 
  'placeholder' => $fieldConfig['placeholder_end'] ?? '',
  'value' => $value['end'] ?? ''
]);
?>

<div class="<?= FormHelper::getClassName('daterange-wrapper', $config) ?>">
  <input <?= Html::attr($startAttributes) ?> />
  <input <?= Html::attr($endAttributes) ?> />
</div>