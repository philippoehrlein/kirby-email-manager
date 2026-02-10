<?php

use KirbyEmailManager\Helpers\PathHelper;

$dir = PathHelper::templateDir() . 'emails/default/';

return [
  // Kirby sucht nach extension('templates', 'emails/default/mail') etc.
  'emails/default/mail'       => $dir . 'mail.text.php',
  'emails/default/mail.html'  => $dir . 'mail.html.php',
  'emails/default/mail.text'  => $dir . 'mail.text.php',
  'emails/default/reply'      => $dir . 'reply.text.php',
  'emails/default/reply.html'  => $dir . 'reply.html.php',
  'emails/default/reply.text'  => $dir . 'reply.text.php',
];