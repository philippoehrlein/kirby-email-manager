<?php

namespace KirbyEmailManager\Tests\Unit\Helpers\Fields;

use KirbyEmailManager\Tests\Unit\Helpers\ValidationHelperTest;
use KirbyEmailManager\Helpers\ValidationHelper;

class FileFieldTest extends ValidationHelperTest
{
    private $fieldKey = 'attachment';
    private $fieldConfig;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->fieldConfig = [
            'validate' => 'file',
            'required' => true,
            'allowed_types' => ['pdf', 'doc', 'docx'],
            'max_size' => 5242880, // 5MB
            'error_message' => [
                'de' => 'Bitte laden Sie eine gültige Datei hoch.'
            ]
        ];
    }

    public function testEmptyFile()
    {
        $data = [$this->fieldKey => ''];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertNotEmpty($errors);
        $this->assertEquals(
            'Dieses Feld ist erforderlich.',
            $errors[$this->fieldKey]
        );
    }

    public function testInvalidFileType()
    {
        $data = [$this->fieldKey => [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'size' => 1024,
            'tmp_name' => '/tmp/test.jpg'
        ]];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertNotEmpty($errors);
    }

    public function testFileTooLarge()
    {
        $data = [$this->fieldKey => [
            'name' => 'test.pdf',
            'type' => 'application/pdf',
            'size' => 10485760, // 10MB
            'tmp_name' => '/tmp/test.pdf'
        ]];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertNotEmpty($errors);
    }

    public function testValidFile()
    {
        $data = [$this->fieldKey => [
            'name' => 'test.pdf',
            'type' => 'application/pdf',
            'size' => 1048576, // 1MB
            'tmp_name' => '/tmp/test.pdf'
        ]];
        $errors = ValidationHelper::validateField($this->fieldKey, $this->fieldConfig, $data, $this->translations, $this->languageCode);
        $this->assertEmpty($errors);
    }
}