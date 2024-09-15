<?php
use Kirby\Data\Data;
use KirbyEmailManager\Helpers\FormHelper;

// Form-Builder: Generates the form layout and structure

// Get the selected language code
$languageCode = $kirby->language()->code() ?? 'en';

// Load the selected template from the panel
$selectedTemplate = $page->email_templates()->value();

// Load the configuration file for the selected template
// Load the selected template from the panel
$selectedTemplateId = $page->email_templates()->value();
$templates = kirby()->option('philippoehrlein.kirby-email-manager.templates');
$templateConfig = $templates[$selectedTemplateId] ?? [];

if (empty($templateConfig)) {
    throw new Exception('Selected email template configuration not found.');
}

$configPath = $kirby->root('site') . '/templates/emails/' . $selectedTemplateId . '/config.yml';

if (!file_exists($configPath)) {
    throw new Exception('Configuration file not found: ' . $configPath);
}

$templateConfig = Data::read($configPath);
error_log('Debug: $templateConfig = ' . print_r($templateConfig, true));
error_log('Debug: Keys in $templateConfig = ' . implode(', ', array_keys($templateConfig)));

if (empty($templateConfig)) {
    throw new Exception('Template configuration is empty.');
}

if (!isset($templateConfig['fields']) || !is_array($templateConfig['fields'])) {
    throw new Exception('Template configuration is missing the "fields" key or it is not an array.');
}

$buttonTexts = $templateConfig['button_texts'] ?? [];
$buttonTranslations = t('button_texts');
$defaultButtonTexts = [
  'send' => $templateConfig['button_texts']['send'][$languageCode] 
            ?? ($buttonTranslations['send'] ?? 'Senden'),
  'reset' => $templateConfig['button_texts']['reset'][$languageCode] 
            ?? ($buttonTranslations['reset'] ?? 'Zurücksetzen'),
];
// Button text either from template config or fallback
$sendButtonText = $buttonTexts['send'][$languageCode] ?? $defaultButtonTexts['send'];
$resetButtonText = $buttonTexts['reset'][$languageCode] ?? $defaultButtonTexts['reset'];

// Fetch any alert or validation error data passed from the handler

$alert = $formHandler['alert'] ?? [];
$data = $formHandler['data'] ?? [];

snippet('email-templates/styles/honeypot');
snippet('email-templates/styles/grid');

$pluginConfig = kirby()->option('philippoehrlein.kirby-email-manager.classConfig', []);
$config = [
  'classPrefix' => $pluginConfig['classPrefix'] ?? $prefix ?? 'kem-',
  'classes' => $pluginConfig['classes'] ?? [],
  'additionalClasses' => $pluginConfig['additionalClasses'] ?? [],
  'noPrefixElements' => $pluginConfig['noPrefixElements'] ?? []
];

?>

<form method="post" action="<?= $page->url() ?>" class="<?= FormHelper::getClassName('form', $config) ?>">
    <?php if (isset($alert['message']) && $alert['type'] === 'error'): ?>
        <p class="<?= FormHelper::getClassName('error', $config, 'error') ?>"><?= $alert['message'] ?></p>
    <?php elseif (isset($alert['message']) && $alert['type'] === 'warning'): ?>
        <p class="<?= FormHelper::getClassName('error', $config, 'warning') ?>"><?= $alert['message'] ?></p>
    <?php endif ?>

    <div class="honeypot" aria-hidden="true">
    <label for="website" tabindex="-1">
      <span class="visually-hidden">Bitte nicht ausfüllen (Spamschutz)</span>
    </label>
    <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
  </div>

  <input type="hidden" name="timestamp" value="<?= time() ?>">

  <?php if ($page->send_to_more()->toBool()): ?>
    <div class="<?= FormHelper::getClassName('field', $config, 'select') ?>" style="<?= FormHelper::generateSpanStyles([12]) ?>">
      <label class="<?= FormHelper::getClassName('label', $config) ?>" for="topic"><?= t('topic_label') ?></label>
      <?php
      $options = [];
      $inputClass = FormHelper::getClassName('input', $config, 'select');

      $span = FormHelper::getResponsiveSpan('1/1');
      $spanStyle = FormHelper::generateSpanStyles($span);

      foreach ($page->send_to_structure()->toStructure() as $item) {
        $options[$item->topic()->value()] = $item->topic()->value();
      }
      snippet('email-templates/form/select', [
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
      $span = FormHelper::getResponsiveSpan($fieldConfig['width'] ?? '1/1');
    ?>
    <?php snippet('email-templates/form/base', [
        'fieldKey' => $fieldKey,
        'fieldConfig' => $fieldConfig,
        'value' => $data[$fieldKey] ?? '',
        'placeholder' => $fieldConfig['placeholder'][$languageCode] ?? '',
        'config' => $config,
        'languageCode' => $languageCode
      ] )?>
  <?php endforeach ?>
  </div>

  <!-- GDPR Checkbox -->
  <?php if ($page->gdpr_checkbox()->toBool()): ?>
    <div class="<?= FormHelper::getClassName('field', $config, 'checkbox') ?>">
      <input type="checkbox" class="<?= FormHelper::getClassName('input', $config) ?> <?= FormHelper::getClassName('input', $config) ?>--checkbox" id="gdpr" name="gdpr" <?= array_key_exists('gdpr', $data) ? 'checked' : '' ?> required>
      <label for="gdpr"><?= $page->gdpr_text()->kt() ?></label>
    </div>
  <?php endif; ?>

  <!-- Form Actions (Buttons) -->
  <div class="<?= FormHelper::getClassName('form', $config, 'actions') ?>">
    <button type="reset" class="<?= FormHelper::getClassName('button', $config, 'secondary') ?>"><?= $resetButtonText ?></button>
    <input type="submit" class="<?= FormHelper::getClassName('button', $config, 'primary') ?>" name="submit" value="<?= $sendButtonText ?>" />
</div>

  <input type="hidden" name="csrf" value="<?= csrf() ?>">
</form>