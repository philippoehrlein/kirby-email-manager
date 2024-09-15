<?php
use KirbyEmailManager\Helpers\FormHelper;

$config = [
  'classPrefix' => $pluginConfig['classPrefix'] ?? false,
  'classes' => $pluginConfig['classes'] ?? [],
  'additionalClasses' => $pluginConfig['additionalClasses'] ?? [],
  'noPrefixElements' => $pluginConfig['noPrefixElements'] ?? []
];

$formGridClass = FormHelper::getClassName('grid', $config);
$formFieldClass = FormHelper::getClassName('field', $config);
?>

<style>
  :root {
    --kem-form-grid-gap: 1rem;
  }

  .<?= $formGridClass ?> {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: var(--kem-form-grid-gap);
  }

  .<?= $formFieldClass ?> {
    grid-column: span 12;
  }

  .<?= $formFieldClass ?>[style*="--span"] {
    grid-column: span var(--span);
  }

  @media (min-width: 768px) {
    .<?= $formFieldClass ?>[style*="--span-medium"] {
      grid-column: span var(--span-medium);
    }
  }

  @media (min-width: 1024px) {
    .<?= $formFieldClass ?>[style*="--span-large"] {
      grid-column: span var(--span-large);
    }
  }

  .form--actions {
    width: 100%;
    display: flex;
    flex-direction: column;
    padding: 0.5rem 0 1rem;
    gap: 1rem;
    text-align: center;

    * {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    @media (min-width: 768px) {
      flex-direction: row;
      justify-content: end;
      padding: 1rem 0 2rem;
    }
    
  }
</style>