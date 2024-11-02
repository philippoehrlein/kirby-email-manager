<?php

namespace KirbyEmailManager\Tests\Unit\Helpers\Fields;

use KirbyEmailManager\Tests\Unit\Helpers\ValidationHelperTest;
use KirbyEmailManager\Helpers\ValidationHelper;

class DateRangeFieldTest extends ValidationHelperTest
{
    private $fieldKey = 'date_range';
    private $fieldConfig;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->fieldConfig = [
            'validate' => 'date-range',
            'required' => true,
            'error_message' => [
                'de' => 'Bitte geben Sie einen gültigen Datumsbereich ein.'
            ]
        ];
    }

    public function testEmptyDateRange()
    {
        $data = [$this->fieldKey => ''];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertNotEmpty($errors);
        $this->assertEquals(
            'Dieses Feld ist erforderlich.',
            $errors[$this->fieldKey]
        );
    }

    public function testInvalidDateFormat()
    {
        $data = [$this->fieldKey => ['start' => 'kein-datum', 'end' => '2024-12-31']];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertNotEmpty($errors);
    }

    public function testEndBeforeStart()
    {
        $data = [$this->fieldKey => ['start' => '2024-12-31', 'end' => '2024-01-01']];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertNotEmpty($errors);
    }

    public function testValidDateRange()
    {
        $data = [$this->fieldKey => ['start' => '2024-01-01', 'end' => '2024-12-31']];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertEmpty($errors);
    }

    public function testOptionalDateRange()
    {
        $this->fieldConfig['required'] = false;
        $data = [$this->fieldKey => ['start' => '', 'end' => '']];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertEmpty($errors);
    }
}