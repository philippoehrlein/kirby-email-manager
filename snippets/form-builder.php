<?php
use Kirby\Data\Data;
use KirbyEmailManager\Helpers\FormHelper;
use KirbyEmailManager\Helpers\SecurityHelper;
use KirbyEmailManager\Helpers\LanguageHelper;

// Form-Builder: Generates the form layout and structure
$languageCode = LanguageHelper::getCurrentLanguageCode();


// Load the selected template from the panel
$selectedTemplateId = $contentWrapper->email_templates()->value();
$templates = kirby()->option('philippoehrlein.kirby-email-manager.templates');
$templateConfig = $templates[$selectedTemplateId] ?? [];

if (empty($templateConfig)) {
    throw new Exception(t('error_messages.template_not_found', 'Selected email template configuration not found.'));
}

$configPath = kirby()->root('site') . '/templates/emails/' . $selectedTemplateId . '/config.yml';

if (!file_exists($configPath)) {
    throw new Exception(t('error_messages.config_file_not_found', 'Configuration file not found: ') . $configPath);
}

$templateConfig = Data::read($configPath);

if (empty($templateConfig)) {
    throw new Exception(t('error_messages.template_config_empty', 'Template configuration is empty.'));
}

if (!isset($templateConfig['fields']) || !is_array($templateConfig['fields'])) {
    throw new Exception(t('error_messages.template_fields_missing', 'Template configuration is missing the "fields" key or it is not an array.'));
}

$resetButton = $templateConfig['buttons']['reset'] ?? ['show' => true];
$resetButtonShow = $resetButton['show'] ?? true;

$sendButtonText = LanguageHelper::getTranslatedValue(
    $templateConfig['buttons']['send']['text'] ?? [],
    $languageCode,
    'Send'
);

$resetButtonText = LanguageHelper::getTranslatedValue(
    $templateConfig['buttons']['reset']['text'] ?? [],
    $languageCode,
    'Reset'
);


$pluginConfig = kirby()->option('philippoehrlein.kirby-email-manager.classConfig', []);
$config = [
  'classPrefix' => $pluginConfig['classPrefix'] ?? $prefix ?? 'kem-',
  'classes' => $pluginConfig['classes'] ?? [],
  'additionalClasses' => $pluginConfig['additionalClasses'] ?? [],
  'noPrefixElements' => $pluginConfig['noPrefixElements'] ?? []
];

$successMessage = $alert['successMessage'] ?? null;
$keepForm = $templateConfig['keep_form'] ?? false;
$fieldErrors = $alert['errors'] ?? [];

snippet('email-manager/styles/honeypot');
snippet('email-manager/styles/grid', ['pluginConfig' => $pluginConfig]);
?>


<form id="contactForm" method="post" enctype="multipart/form-data" action="<?= $page->url() ?>" class="<?= FormHelper::getClassName('form', $config) ?>">
  <?php if (isset($alert['message']) && $alert['type'] === 'error'): ?>
      <p class="<?= FormHelper::getClassName('error', $config, 'error') ?>"><?= $alert['message'] ?></p>
  <?php elseif (isset($alert['message']) && $alert['type'] === 'warning'): ?>
      <p class="<?= FormHelper::getClassName('error', $config, 'warning') ?>"><?= $alert['message'] ?></p>
  <?php endif ?>

  <div class="hp_field__sp" aria-hidden="true">
    <label for="website_hp_" tabindex="-1">
      <span class="visually-hidden"><?= t('honeypot_label') ?></span>
    </label>
    <input type="text" name="website_hp_" id="website_hp_" tabindex="-1" autocomplete="off">
  </div>

  <input type="hidden" name="timestamp" value="<?= time() ?>">

  <?php if ($contentWrapper->send_to_more()->toBool()): ?>
    <div class="<?= FormHelper::getClassName('field', $config, 'select') ?>" style="<?= FormHelper::generateSpanStyles([12]) ?>">
      <label class="<?= FormHelper::getClassName('label', $config) ?>" for="topic"><?= t('topic_label') ?></label>
      <?php
      $options = [];
      $inputClass = FormHelper::getClassName('select', $config);

      $span = FormHelper::getResponsiveSpan('1/1');
      $spanStyle = FormHelper::generateSpanStyles($span);
    
      foreach ($contentWrapper->send_to_structure()->toStructure() as $item) {
        $options[$item->topic()->value()] = $item->topic()->value();
      }
      snippet('email-manager/form/select', [
        'fieldKey' => 'topic',
        'fieldConfig' => [
          'type' => 'select',
          'required' => true,
          'placeholder' => [
            $languageCode => t('select_topic')
          ],
          'options' => $options
        ],
        'value' => $data['topic'] ?? '',
        'languageCode' => $languageCode,
        'inputClass' => $inputClass
      ]) ?>
    </div>
  <?php endif ?>

  <div class="<?= FormHelper::getClassName('grid', $config) ?>">
  <?php foreach ($templateConfig['fields'] as $fieldKey => $fieldConfig): ?>
    <?php
    $rawValue = $data[$fieldKey] ?? '';
    $value = is_array($rawValue) 
      ? array_map([SecurityHelper::class, 'escapeHtml'], $rawValue)
      : SecurityHelper::escapeHtml($rawValue);
    
    snippet('email-manager/form/base', [
        'fieldKey' => $fieldKey,
        'fieldConfig' => $fieldConfig,
        'value' => $value,
        'placeholder' => $fieldConfig['placeholder'][$languageCode] ?? '',
        'config' => $config,
        'languageCode' => $languageCode,
        'error' => $fieldErrors[$fieldKey] ?? null
      ] )?>
  <?php endforeach ?>
  </div>

  <!-- GDPR Checkbox -->
  <?php if ($contentWrapper->gdpr_checkbox()->toBool()): ?>
    <div class="<?= FormHelper::getClassName('field', $config, 'checkbox') ?>">
      <input type="checkbox" tabindex="0" class="<?= FormHelper::getClassName('input', $config) ?> <?= FormHelper::getClassName('input', $config) ?>--checkbox" id="gdpr" name="gdpr" <?= array_key_exists('gdpr', $data) ? 'checked' : '' ?> required>
      <?php
       $gdprText = $contentWrapper->gdpr_text()->kt()->permalinksToUrls();
      ?>
      <label for="gdpr"><?= $gdprText ?></label>
    </div>
  <?php endif; ?>

  <!-- Form Actions (Buttons) -->
  <div class="<?= FormHelper::getClassName('form', $config, 'actions') ?>">
    <?php if ($resetButtonShow): ?>
      <button type="reset" tabindex="0" class="<?= FormHelper::getClassName('button', $config, 'secondary') ?>"><?= $resetButtonText ?></button>
    <?php endif; ?>
    <input type="submit" tabindex="0" class="<?= FormHelper::getClassName('button', $config, 'primary') ?>" name="submit" value="<?= $sendButtonText ?>" />
  </div>

  <input type="hidden" name="csrf" value="<?= csrf() ?>">
  
</form>