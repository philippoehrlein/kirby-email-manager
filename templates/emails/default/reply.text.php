<?= t('philippoehrlein.kirby-email-manager.email.default.reply_message', 'Thank you for your email.') ?>

<?php snippet('emails/data-text', ['form' => $form]) ?>
<?php if ($email->footer()->isNotEmpty()): ?>

---
<?= trim(strip_tags((string)$email->footer())) ?>
<?php endif; ?>
