<?php
$dateRange = explode(', ', $form->stay());
$stayStart = new DateTime($dateRange[0]);
$stayEnd = new DateTime($dateRange[1]);
?>
New Booking Request from <?= $form->name() ?>:
Email: <?= $form->email() ?>
Phone: <?= $form->phone() ?>
Date Range: <?= $stayStart->format('d.m.Y') ?> - <?= $stayEnd->format('d.m.Y') ?>
Message: <?= $form->message() ?>