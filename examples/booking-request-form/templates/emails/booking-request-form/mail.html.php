<?php
$dateRange = explode(', ', $form->stay());
$stayStart = new DateTime($dateRange[0]);
$stayEnd = new DateTime($dateRange[1]);
?>

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
  New Booking Request from <?= $form->name() ?>:<br>
  Email: <?= $form->email() ?><br>
  Phone: <?= $form->phone() ?><br>
  Date Range: <?= $stayStart->format('d.m.Y') ?> - <?= $stayEnd->format('d.m.Y') ?><br>
  Message: <?= $form->message() ?><br><br>
</body>
</html>