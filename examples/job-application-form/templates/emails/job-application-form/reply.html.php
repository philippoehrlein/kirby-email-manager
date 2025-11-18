<!DOCTYPE html>
<html lang="<?= $languageCode ?>">
<head>
  <meta charset="UTF-8">
  <title><?= $email->subject() ?></title>
  <style>
  body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
    font-size: 16px;
    line-height: 1.5;
  }
  
  label {
    font-weight: bold;
  }

  hr {
    border: 0;
    border-top: 1px solid #777;
    margin: 20px 0;
  }
</style>
</head>
<body>
    Hello <?= $form->name() ?>,<br>
    Thank you for your job application.<br>
    We will get back to you shortly.<br>
    Best regards,<br>
    HR Team
    <?php if ($email->footer()->isNotEmpty()): ?>
      <hr>
      <?= $email->footer()->kt() ?>
    <?php endif; ?>
</body>
</html>