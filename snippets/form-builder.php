<?php
use Kirby\Data\Data;
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

?>

<form method="post" action="<?= $page->url() ?>" class="form">
  <?php if (isset($alert['message']) && $alert['type'] === 'error'): ?>
      <p class="alert alert--error  general-error"><?= $alert['message'] ?></p>
  <?php endif ?>

  <?php if ($page->use_email_structure()->toBool()): ?>
  <div class="field field--select">
    <label for="subject"><?= t('subject') ?></label>
    <select name="subject" id="subject" required>
      <option value=""><?= t('select_subject') ?></option>
      <?php foreach ($page->email_structure()->toStructure() as $item): ?>
        <option value="<?= $item->subject() ?>" <?= ($data['subject'] ?? '') === $item->subject() ? 'selected' : '' ?>>
          <?= $item->subject() ?>
        </option>
      <?php endforeach ?>
    </select>
</div>
<?php endif ?>

  <?php foreach ($templateConfig['fields'] as $fieldKey => $fieldConfig): ?>
    <div class="field field--<?= $fieldConfig['type'] ?>">
      <label for="<?= $fieldKey ?>"><?= $fieldConfig['label'][$languageCode] ?></label>
      <?php snippet('email-templates/form/base', [
        'fieldKey' => $fieldKey,
        'fieldConfig' => $fieldConfig,
        'value' => $data[$fieldKey] ?? '',
        'placeholder' => $fieldConfig['placeholder'][$languageCode] ?? '',
      ] )?>
      
      <!-- Display validation error if present -->
      <?php if (isset($alert['errors'][$fieldKey])): ?>
        <p class="form-error"><?= $alert['errors'][$fieldKey] ?></p>
      <?php endif ?>
    </div>
  <?php endforeach ?>

  <!-- GDPR Checkbox -->
  <?php if ($page->gdpr_checkbox()->toBool()): ?>
    <div class="field field--checkbox">
      <input type="checkbox" class="checkbox" id="gdpr" name="gdpr" <?= array_key_exists('gdpr', $data) ? 'checked' : '' ?> required>
      <label for="gdpr"><?= $page->gdpr_text()->kt() ?></label>
    </div>
  <?php endif; ?>

  <!-- Form Actions (Buttons) -->
  <div class="form__actions">
    <button type="reset" class="button button--secondary"><?= $resetButtonText ?></button>
    <input type="submit" class="button button--primary" name="submit" value="<?= $sendButtonText ?>" />
  </div>
</form>