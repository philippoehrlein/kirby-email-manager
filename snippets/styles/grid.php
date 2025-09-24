<?php
use KirbyEmailManager\Helpers\FormHelper;
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
  @media (min-width: 600px) {
  .<?= $formFieldClass ?> {
    grid-column: span var(--span, 12);
  }
}

  @media (min-width: 768px) {
    .<?= $formFieldClass ?> {
      grid-column: span var(--span-medium, var(--span, 12));
    }
  }

  @media (min-width: 1024px) {
    .<?= $formFieldClass ?> {
      grid-column: span var(--span-large, var(--span-medium, var(--span, 12)));
    }
  }

  .form--actions {
    width: 100%;
    display: flex;
    flex-direction: column;
    padding: 0.5rem 0 1rem;
    gap: 0.5rem;
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