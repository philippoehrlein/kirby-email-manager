<?php
use KirbyEmailManager\Helpers\FormHelper;

foreach ($fieldConfig['options'] as $optionValue => $optionLabel):
  $attributes['id'] = $attributes['id'] . '_' . $optionValue;
  $attributes['value'] = $optionValue;
?>
  <div <?= FormHelper::getClassName('radio-option', $config) ?>>
    <input <?= Html::attr($attributes) ?> tabindex="0">
    <label for="<?= $attributes['id'] ?>">
      <?= is_array($optionLabel) ? ($optionLabel[$languageCode] ?? $optionValue) : $optionLabel ?>
    </label>
  </div>
<?php endforeach; ?>