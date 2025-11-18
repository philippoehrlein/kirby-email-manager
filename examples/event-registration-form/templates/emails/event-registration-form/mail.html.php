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
  New Event Registration request from <?= $form->name() ?>:<br>
  Email: <?= $form->email() ?><br>
  Event Date: <?= $form->eventdate()->toDate('d.m.Y') ?><br>
  Number of Participants: <?= $form->participants() ?><br>
  Subscribe to Newsletter:<?php if ($form->newsletter() == 'subscribe'): ?>Yes<?php else: ?>No<?php endif; ?><br><br>
</body>
</html>