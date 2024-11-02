<?php

namespace KirbyEmailManager\Tests\Unit\Helpers\Fields;

use KirbyEmailManager\Tests\Unit\Helpers\ValidationHelperTest;
use KirbyEmailManager\Helpers\ValidationHelper;

class NumberFieldTest extends ValidationHelperTest
{
    private $fieldKey = 'amount';
    private $fieldConfig;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->fieldConfig = [
            'validate' => 'number',
            'required' => true,
            'min' => 1,
            'max' => 100,
            'error_message' => [
                'de' => 'Bitte geben Sie eine gültige Zahl ein.'
            ]
        ];
    }

    public function testEmptyNumber()
    {
        $data = [$this->fieldKey => ''];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertNotEmpty($errors);
        $this->assertEquals(
            'Dieses Feld ist erforderlich.',
            $errors[$this->fieldKey]
        );
    }

    public function testInvalidNumber()
    {
        $data = [$this->fieldKey => 'abc'];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertNotEmpty($errors);
    }

    public function testNumberTooSmall()
    {
        $data = [$this->fieldKey => '0'];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertNotEmpty($errors);
    }

    public function testNumberTooLarge()
    {
        $data = [$this->fieldKey => '101'];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertNotEmpty($errors);
    }

    public function testValidNumber()
    {
        $data = [$this->fieldKey => '50'];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertEmpty($errors);
    }
}