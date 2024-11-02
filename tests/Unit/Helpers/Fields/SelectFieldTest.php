<?php

namespace KirbyEmailManager\Tests\Unit\Helpers\Fields;

use KirbyEmailManager\Tests\Unit\Helpers\ValidationHelperTest;
use KirbyEmailManager\Helpers\ValidationHelper;

class SelectFieldTest extends ValidationHelperTest
{
    private $fieldKey = 'category';
    private $fieldConfig;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fieldConfig = [
            'validate' => 'select',
            'required' => true,
            'options' => [
                'support' => 'Support',
                'feedback' => 'Feedback',
                'other' => 'Sonstiges'
            ]
        ];
    }

    public function testEmptySelect()
    {
        $data = [$this->fieldKey => ''];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertNotEmpty($errors);
        $this->assertEquals(
            'Dieses Feld ist erforderlich.',
            $errors[$this->fieldKey]
        );
    }

    public function testInvalidOption()
    {
        $data = [$this->fieldKey => 'invalid-option'];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertNotEmpty($errors);
    }

    public function testValidOption()
    {
        $data = [$this->fieldKey => 'support'];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertEmpty($errors);
    }

    public function testOptionalSelect()
    {
        $this->fieldConfig['required'] = false;
        $data = [$this->fieldKey => ''];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertEmpty($errors);
    }
}
