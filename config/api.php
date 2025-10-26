<?php

use KirbyEmailManager\Helpers\LicenseManager;

return [
  'routes' => [
    [
      'pattern' => 'email-manager/license/status',
      'method' => 'GET',
      'action' => function () {
        try {
          $manager = new LicenseManager();
          $isActivated = $manager->isActivated();
    
          if ($isActivated) {
            $needsRenewal = $manager->needsRenewal();
    
            if ($needsRenewal) {
              $renewalResult = $manager->attemptAutoRenewal();
            }
          }
    
          return [
            'ok' => true,
            'activated' => $manager->isActivated(),
          ];
        } catch (Throwable $e) {
          return [
            'ok' => false,
            'error' => $e->getMessage(),
          ];
        }
      }
    ]
  ]
];