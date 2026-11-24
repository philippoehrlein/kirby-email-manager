<?= t('philippoehrlein.kirby-email-manager.email.default.message', 'New email from the contact form.') ?>

<?php snippet('emails/data-text', ['form' => $form]) ?>
<?php if ($email->footer()->isNotEmpty()): ?>

---
<?= trim(strip_tags((string)$email->footer())) ?>
<?php endif; ?>
