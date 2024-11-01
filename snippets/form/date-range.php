<?php use KirbyEmailManager\Helpers\FormHelper; ?>

<div class="<?= FormHelper::getClassName('daterange-wrapper', $config) ?>">
  <input <?= Html::attr($attributes['start']) ?> />
  <input <?= Html::attr($attributes['end']) ?> />
</div>