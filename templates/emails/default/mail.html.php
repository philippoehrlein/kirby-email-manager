<!DOCTYPE html>
<html lang="<?= $languageCode ?>">
  <head>
    <title><?= $email->subject() ?></title>
  </head>
  <body>
    <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 1.5;">
      <p><?= t('philippoehrlein.kirby-email-manager.email.default.message', 'New email from the contact form.') ?></p>
      <?php snippet('emails/data-html', ['form' => $form]) ?>
    </div>
  </body>
</html>