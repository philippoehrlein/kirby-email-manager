<?php

namespace KirbyEmailManager\Form\Field;

use Kirby\Cms\App;
use Kirby\Form\Field\DisplayField;
use Kirby\Reflection\Attributes\Prop;

/**
 * Lists forms that override the global legal settings.
 */
class LocalFormsField extends DisplayField
{
	/**
	 * Forms with local legal overrides, prepared for k-items
	 */
	#[Prop(computed: true)]
	protected array $collection = [];

	public function collection(): array
	{
		return App::instance()->collection('local-legal-form') ?? [];
	}
}
