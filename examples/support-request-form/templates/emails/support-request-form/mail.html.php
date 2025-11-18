<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $email->subject() ?></title>
  <style>
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
      font-size: 16px;
      line-height: 1.5;
    }
  </style>
</head>
<body>
  New support request from <?= $form->name() ?>:<br>
  Email: <?= $form->email() ?><br>
  Category: <?= $form->category() ?><br>
  Message: <?= $form->message() ?><br>
</body>
</html>