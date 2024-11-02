<?php

namespace KirbyEmailManager\Tests\Unit\Helpers\Fields;

use KirbyEmailManager\Tests\Unit\Helpers\ValidationHelperTest;
use KirbyEmailManager\Helpers\ValidationHelper;

class DateFieldTest extends ValidationHelperTest
{
    private $fieldKey = 'date';

    public function testFixedDateValidation()
    {
        $fieldConfig = [
            'validate' => 'date',
            'min' => '2024-01-01',
            'max' => '2024-12-31'
        ];

        // Test 1: Ungültiges Datumsformat
        $data = [$this->fieldKey => 'kein-datum'];
        $errors = ValidationHelper::validateField($this->fieldKey, $fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertNotEmpty($errors);

        // Test 2: Datum vor Minimum
        $data = [$this->fieldKey => '2023-12-31'];
        $errors = ValidationHelper::validateField($this->fieldKey, $fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertNotEmpty($errors);

        // Test 3: Gültiges Datum
        $data = [$this->fieldKey => '2024-06-15'];
        $errors = ValidationHelper::validateField($this->fieldKey, $fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertEmpty($errors);
    }

    public function testTodayDateValidation()
    {
        $fieldConfig = [
            'validate' => 'date',
            'min' => 'today',
            'max' => '+30days'
        ];

        // Test 1: Datum vor heute
        $data = [$this->fieldKey => date('Y-m-d', strtotime('-1 day'))];
        $errors = ValidationHelper::validateField($this->fieldKey, $fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertNotEmpty($errors);

        // Test 2: Datum ist heute
        $data = [$this->fieldKey => date('Y-m-d')];
        $errors = ValidationHelper::validateField($this->fieldKey, $fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertEmpty($errors);
    }

    public function testRelativeDateValidation()
    {
        $fieldConfig = [
            'validate' => 'date',
            'min' => '-1month',
            'max' => '+1month'
        ];

        // Test 1: Datum vor dem relativen Minimum
        $data = [$this->fieldKey => date('Y-m-d', strtotime('-2 months'))];
        $errors = ValidationHelper::validateField($this->fieldKey, $fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertNotEmpty($errors);

        // Test 2: Datum innerhalb des relativen Bereichs
        $data = [$this->fieldKey => date('Y-m-d')];
        $errors = ValidationHelper::validateField($this->fieldKey, $fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertEmpty($errors);

        // Test 3: Datum nach dem relativen Maximum
        $data = [$this->fieldKey => date('Y-m-d', strtotime('+2 months'))];
        $errors = ValidationHelper::validateField($this->fieldKey, $fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertNotEmpty($errors);
    }
}