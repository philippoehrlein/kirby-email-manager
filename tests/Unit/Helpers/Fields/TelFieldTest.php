<?php

namespace KirbyEmailManager\Tests\Unit\Helpers\Fields;

use KirbyEmailManager\Tests\Unit\Helpers\ValidationHelperTest;
use KirbyEmailManager\Helpers\ValidationHelper;

class TelFieldTest extends ValidationHelperTest
{
    private $fieldKey = 'phone';
    private $fieldConfig;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->fieldConfig = [
            'validate' => 'tel',
            'required' => true,
            'error_message' => [
                'de' => 'Bitte geben Sie eine gültige Telefonnummer ein.'
            ]
        ];
    }

    public function testEmptyPhone()
    {
        $data = [$this->fieldKey => ''];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertNotEmpty($errors);
        $this->assertEquals(
            'Dieses Feld ist erforderlich.',
            $errors[$this->fieldKey]
        );
    }

    public function testInvalidPhone()
    {
        $data = [$this->fieldKey => 'abc123'];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertNotEmpty($errors);
    }

    public function testValidPhone()
    {
        $data = [$this->fieldKey => '+49 123 4567890'];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertEmpty($errors);
    }

    public function testCustomPattern()
    {
        $this->fieldConfig['pattern'] = '^[0-9]{4,}$';
        $data = [$this->fieldKey => '12345'];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertEmpty($errors);
    }
}