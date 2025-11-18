New Event Registration request from <?= $form->name() ?>:
Email: <?= $form->email() ?>
Event Date: <?= $form->eventdate()->toDate('d.m.Y') ?>
Number of Participants: <?= $form->participants() ?>
Subscribe to newsletter:<?php if ($form->newsletter() == 'subscribe'): ?>Yes<?php else: ?>No<?php endif; ?>