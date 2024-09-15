<?php
$attributes = [
  'type' => $type,
  'class' => $base_class . ' ' . $modifier,
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
  'placeholder' => $placeholder . ' (Start)',
  'value' => $value['start'] ?? '',
  'class' => $inputClass
]);

$endAttributes = array_merge($attributes, [
  'id' => $fieldKey . '_end',
  'name' => $fieldKey . '_end',
  'placeholder' => $placeholder . ' (End)',
  'value' => $value['end'] ?? '',
  'class' => $inputClass
]);
?>

<div class="daterange-wrapper">
  <input <?= Html::attr($startAttributes) ?> />
  <input <?= Html::attr($endAttributes) ?> />
</div>