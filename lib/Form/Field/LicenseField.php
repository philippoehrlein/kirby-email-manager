<?php

namespace KirbyEmailManager\Form\Field;

use Kirby\Form\Field\DisplayField;
use KirbyEmailManager\Helpers\LicenseManager;

/**
 * License activation UI (display-only, no stored value).
 * Hidden entirely once the license is active so it doesn't reserve layout space.
 */
class LicenseField extends DisplayField
{
	public function isHidden(): bool
	{
		return (new LicenseManager())->isActivated();
	}
}
