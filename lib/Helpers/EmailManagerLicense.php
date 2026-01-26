<?php

namespace KirbyEmailManager\Helpers;
use Kirby\Plugin\License;
use Kirby\Plugin\LicenseStatus;
use Kirby\Plugin\Plugin;

/**
 * EmailManagerLicense class for managing the license
 * @author Philipp Oehrlein
 * @version 1.0.0
 */

class EmailManagerLicense extends License
{
  /**
   * Constructor for EmailManagerLicense
   * @param Plugin $plugin The plugin instance
   */
  public function __construct(
    protected Plugin $plugin
  ) {
    $licenseManager = new LicenseManager();
    $isActivated = $licenseManager->isActivated();
    $this->name = 'Email Manager License';
    $this->link = 'https://github.com/philippoehrlein/kirby-email-manager?tab=License-1-ov-file#readme';
    $this->status = new LicenseStatus(
      value: $isActivated ? 'active' : 'missing',
      theme: $isActivated ? 'green' : 'love',
      label: $isActivated ? t('license.status.active.label') : t('activate'),
      icon: $isActivated ? 'check' : 'key',
      dialog: $isActivated ? null : 'email-manager/license/activation'
    );
  }
}
