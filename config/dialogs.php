<?php

return [
  [
    'pattern' => 'email-manager/license/activation',
    'load' => function () {
      // get current domain
      $url = kirby()->url();
      $parsed = parse_url($url);
      $domain = $parsed['host'] ?? 'localhost';

      // check if domain is local
      $isLocal = in_array($domain, ['localhost', '127.0.0.1']) ||
                str_contains($domain, '.local') ||
                str_contains($domain, '.ddev.site') ||
                str_contains($domain, '.test');

      $fields = [];

      // warning for local development
      if ($isLocal) {
        $fields['local-warning'] = [
          'type' => 'info',
          'text' => t('philippoehrlein.kirby-email-manager.license-activation-dialog.local-warning'),
          'theme' => 'warning',
        ];
      }

      // license key field
      $fields['license-key'] = [
        'label' => t('philippoehrlein.kirby-email-manager.license-activation-dialog.license.label'),
        'type' => 'text',
        'required' => true,
        'placeholder' => 'KEM-',
        'help' => t('philippoehrlein.kirby-email-manager.license-activation-dialog.license.help'),
      ];

      // email field
      $fields['email'] = [
        'label' => t('philippoehrlein.kirby-email-manager.license-activation-dialog.email.label'),
        'type' => 'email',
        'required' => true,
        'help' => t('philippoehrlein.kirby-email-manager.license-activation-dialog.email.help'),
      ];

      // domain as hidden field
      $fields['domain'] = [
        'type' => 'hidden',
        'required' => true,
      ];

      return [
        'component' => 'k-form-dialog',
        'props' => [
          'title' => t('philippoehrlein.kirby-email-manager.license-activation-dialog.title'),
          'fields' => $fields,
          'submitButton' => [
            'icon' => 'key',
            'text' => t('philippoehrlein.kirby-email-manager.activate-section.button.activate'),
            'theme' => 'love',
          ],
          'value' => [
            'domain' => $domain,
          ],
        ],
      ];
    },
    'submit' => function () {
      $licenseKey = get('license-key');
      $email = get('email');
      $domain = get('domain');

      if (empty($licenseKey) || empty($email) || empty($domain)) {
        return [ 'error' => t('philippoehrlein.kirby-email-manager.license-activation-dialog.error.missing-fields') ];
      }

      try {
        // API call for activation
        $activateUrl ='https://philippoehrlein.de/licenses/activate';

        $data = [
          'license_key' => $licenseKey,
          'email' => $email,
          'domain' => $domain
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $activateUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          'Content-Type: application/json',
          'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
          throw new Exception('cURL Error: ' . $error);
        }

        if ($httpCode !== 200) {
          throw new Exception('HTTP Error: ' . $httpCode);
        }

        $result = json_decode($response, true);

        if (!$result || !$result['ok']) {
          throw new Exception($result['error'] ?? 'Unknown activation error');
        }

        // save license file
        $licenseFile = kirby()->root('config') . '/.kem_licence';
        $licenseData = json_encode($result['license'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if (file_put_contents($licenseFile, $licenseData) === false) {
          throw new Exception('Could not save license file');
        }

        return [
          'message' => [
            'icon' => 'heart',
            'theme' => 'love',
            'message' => t('philippoehrlein.kirby-email-manager.license-activation-dialog.success'),
          ],
          'event' => 'license.activated'
        ];

      } catch (Exception $e) {
        throw new Exception(tt('philippoehrlein.kirby-email-manager.license-activation-dialog.error.activation-failed', ['error' => $e->getMessage()]));
      }
    },
  ]

];