<select <?= Html::attr($attributes) ?>>
  <?php if (!$fieldConfig['required']): ?>
    <option value=""></option>
  <?php endif; ?>

  <?php foreach ($fieldConfig['options'] as $optionValue => $optionLabel): ?>
    <option value="<?= $optionValue ?>" <?= $optionValue === ($value ?? '') ? 'selected' : '' ?>>
      <?= is_array($optionLabel) ? ($optionLabel[$languageCode] ?? $optionValue) : $optionLabel ?>
    </option>
  <?php endforeach; ?>
</select>