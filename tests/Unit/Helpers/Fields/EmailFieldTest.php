<?php

namespace KirbyEmailManager\Tests\Unit\Helpers\Fields;

use KirbyEmailManager\Tests\Unit\Helpers\ValidationHelperTest;
use KirbyEmailManager\Helpers\ValidationHelper;

class EmailFieldTest extends ValidationHelperTest
{
  private $fieldKey = 'email';
  private $fieldConfig;

  protected function setUp(): void
  {
    parent::setUp();

    $this->fieldConfig = [
      'validate' => 'email',
      'required' => true,
      'error_message' => [
        'de' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.'
      ]
    ];
  }

  public function testInvalidEmail()
  {
    // Test 1: Ungültige Email
    $data = [$this->fieldKey => 'nicht-valid'];
    $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
    $this->assertNotEmpty($errors);
    $this->assertEquals(
      'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
      $errors[$this->fieldKey]
    );
  }

  public function testValidEmail()
  {
    // Test 2: Gültige Email
    $data = [$this->fieldKey => 'test@example.com'];
    $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
    $this->assertEmpty($errors);
  }

  public function testEmptyEmail()
  {
    // Test 3: Leere Email (required = true)
    $data = [$this->fieldKey => ''];
    $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
    $this->assertNotEmpty($errors);
    $this->assertEquals(
      'Dieses Feld ist erforderlich.',
      $errors[$this->fieldKey]
    );
  }

  public function testOptionalEmail()
  {
    // Test 4: Leere Email (required = false)
    $this->fieldConfig['required'] = false;
    $data = [$this->fieldKey => ''];
    $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
    $this->assertEmpty($errors);
  }
}
