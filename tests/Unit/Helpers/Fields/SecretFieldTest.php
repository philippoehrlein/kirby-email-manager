<?php

namespace KirbyEmailManager\Tests\Unit\Helpers\Fields;

use KirbyEmailManager\Tests\Unit\Helpers\ValidationHelperTest;
use KirbyEmailManager\Helpers\ValidationHelper;

class SecretFieldTest extends ValidationHelperTest
{
    private $fieldKey = 'password';
    private $fieldConfig;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->fieldConfig = [
            'validate' => 'secret',
            'required' => true,
            'minlength' => 8,
            'error_message' => [
                'de' => 'Bitte geben Sie ein gültiges Passwort ein.'
            ]
        ];
    }

    public function testEmptySecret()
    {
        $data = [$this->fieldKey => ''];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertNotEmpty($errors);
        $this->assertEquals(
            'Dieses Feld ist erforderlich.',
            $errors[$this->fieldKey]
        );
    }

    public function testSecretTooShort()
    {
        $data = [$this->fieldKey => '123'];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertNotEmpty($errors);
    }

    public function testValidSecret()
    {
        $data = [$this->fieldKey => '12345678'];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertEmpty($errors);
    }

    public function testOptionalSecret()
    {
        $this->fieldConfig['required'] = false;
        $data = [$this->fieldKey => ''];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertEmpty($errors);
    }
}