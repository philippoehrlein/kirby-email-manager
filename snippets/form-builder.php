<?php
use KirbyEmailManager\Helpers\FormHelper;
use KirbyEmailManager\Helpers\SecurityHelper;
use KirbyEmailManager\PageMethods\FormHandler;
use KirbyEmailManager\Helpers\LanguageHelper;

// Form-Builder: Generates the form layout and structure

// Get config from FormHandler
$formHandler = new FormHandler($kirby, $page, $contentWrapper);

$templateConfig = $formHandler->getTemplateConfig();
$languageHelper = new LanguageHelper(null, $templateConfig);
$languageCode = $languageHelper->getLanguage();

$resetButton = $templateConfig['buttons']['reset'] ?? ['show' => true];
$resetButtonShow = $resetButton['show'] ?? true;

$sendButtonText = $languageHelper->get('buttons.send.label');
$resetButtonText = $languageHelper->get('buttons.reset.label');

$pluginConfig = kirby()->option('philippoehrlein.kirby-email-manager.classConfig', []);
$config = [
    'classPrefix' => $pluginConfig['classPrefix'] ?? $prefix ?? 'kem-',
    'classes' => $pluginConfig['classes'] ?? [],
    'additionalClasses' => $pluginConfig['additionalClasses'] ?? [],
    'noPrefixElements' => $pluginConfig['noPrefixElements'] ?? [],
    'classModifiers' => $pluginConfig['classModifiers'] ?? []
];

$successMessage = $alert['successMessage'] ?? null;
$keepForm = $templateConfig['keep_form'] ?? false;
$fieldErrors = $alert['errors'] ?? [];

snippet('email-manager/styles/honeypot');
snippet('email-manager/styles/grid', ['config' => $config]);
?>


<form id="contactForm" method="post" enctype="multipart/form-data" action="<?= $page->url() ?>" class="<?= FormHelper::getClassName('form', $config) ?>">
  <?php if (isset($alert['message']) && $alert['type'] === 'error' && kirby()->request()->is('POST')): ?>
      <p class="<?= FormHelper::getClassName('error', $config, 'error') ?>"><?= $alert['message'] ?></p>
  <?php elseif (isset($alert['message']) && $alert['type'] === 'warning' && kirby()->request()->is('POST')): ?>
      <p class="<?= FormHelper::getClassName('error', $config, 'warning') ?>"><?= $alert['message'] ?></p>
  <?php endif ?>

  <div class="visually-hidden" aria-hidden="true">
    <label for="website_hp_" tabindex="-1">
      <span class="visually-hidden"><?= $languageHelper->get('form.honeypot.label') ?></span>
    </label>
    <input type="text" name="website_hp_" id="website_hp_" tabindex="-1" autocomplete="off">
  </div>

  <input type="hidden" name="timestamp" value="<?= time() ?>">

  <div class="<?= FormHelper::getClassName('grid', $config) ?>">

  <?php if ($contentWrapper->send_to_more()->toBool()): 
      $options = [];
      $inputClass = FormHelper::getClassName('select', $config);

      $span = FormHelper::getResponsiveSpan('1/1');
      $spanStyle = FormHelper::generateSpanStyles($span);
    
      foreach ($contentWrapper->send_to_structure()->toStructure() as $item) {
        $options[$item->topic()->value()] = $item->topic()->value();
      }

      snippet('email-manager/form/base', [
        'fieldKey' => 'topic',
        'fieldConfig' => [
            'type' => 'select',
            'required' => true,
            'placeholder' => $languageHelper->get('form.select_topic'),
            'options' => $options,
            'label' => [
                $languageCode => t('philippoehrlein.kirby-email-manager.panel.email.topic_label')
            ]
        ],
        'value' => $data['topic'] ?? '',
        'placeholder' => $languageHelper->get('form.select_topic'),
        'config' => $config,
        'languageCode' => $languageCode,
        'error' => $fieldErrors['topic'] ?? null
      ]);
  endif ?>

  
  <?php foreach ($templateConfig['fields'] as $fieldKey => $fieldConfig): ?>
    <?php
    $rawValue = $data[$fieldKey] ?? '';
    $value = is_array($rawValue) 
      ? array_map([SecurityHelper::class, 'escapeHtml'], $rawValue)
      : SecurityHelper::escapeHtml($rawValue);
    
    snippet('email-manager/form/base', [
        'label' => $languageHelper->get('fields.' . $fieldKey . '.label'),
        'fieldKey' => $fieldKey,
        'fieldConfig' => $fieldConfig,
        'value' => $value,
        'placeholder' => $languageHelper->getTranslationFromTemplateConfig('fields.' . $fieldKey . '.placeholder'),
        'config' => $config,
        'languageCode' => $languageCode,
        'error' => $fieldErrors[$fieldKey] ?? null
      ] )?>
  <?php endforeach ?>
  </div>

  <!-- GDPR Checkbox -->
  <?php if ($contentWrapper->gdpr_checkbox()->toBool()): ?>
    <div class="<?= FormHelper::getClassName('field', $config, 'checkbox') ?>">
      <input type="checkbox" tabindex="0" class="<?= FormHelper::getClassName('input', $config, 'checkbox') ?>" id="gdpr" name="gdpr" <?= array_key_exists('gdpr', $data) ? 'checked' : '' ?> required>
      <?php
       $gdprText = $contentWrapper->gdpr_text()->kt()->permalinksToUrls();
      ?>
      <label for="gdpr"><?= $gdprText ?></label>
    </div>
  <?php endif; ?>

  <!-- CAPTCHA Integration -->
  <?php if (isset($templateConfig['captcha'])): ?>
    <?php
      $captchaConfig = $templateConfig['captcha'];
      if (isset($captchaConfig['frontend']['snippet'])) {
        snippet($captchaConfig['frontend']['snippet'], [
          'options' => $captchaConfig['options'] ?? [],
          'fieldName' => $captchaConfig['frontend']['field_name'] ?? 'captcha-response',
          'error' => $fieldErrors[$captchaConfig['frontend']['field_name']] ?? null,
          'config' => $config,
          'languageCode' => $languageCode
        ]);
      }
    ?>
  <?php endif; ?>

  <?php snippet('email-manager/form/actions', [
    'classActions' => FormHelper::getClassName('form', $config, 'actions'),
    'classButtonSecondary' => FormHelper::getClassName('button', $config, 'secondary'),
    'classButtonPrimary' => FormHelper::getClassName('button', $config, 'primary'),
    'resetButtonShow' => $resetButtonShow,
    'resetButtonText' => $resetButtonText,
    'sendButtonText' => $sendButtonText
  ]) ?>

  <input type="hidden" name="csrf" value="<?= csrf() ?>">
  
</form>