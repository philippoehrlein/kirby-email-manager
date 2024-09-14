<?php
  snippet('email-templates/form/' . $fieldConfig['type'], [
    'fieldKey' => $fieldKey,
    'fieldConfig' => $fieldConfig,
    'value' => $value,
    'placeholder' => $placeholder,
    'languageCode' => $kirby->language()->code() ?? 'en'
  ]);
?>