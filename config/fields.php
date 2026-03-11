<?php

return [
  'email-manager-local-forms' => [
    'props' => [
      'collection' => function() {
        return kirby()->collection('local-legal-form');
      }
    ]
  ]
];