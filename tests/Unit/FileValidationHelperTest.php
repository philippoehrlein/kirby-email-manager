<?php

namespace KirbyEmailManager\Tests\Unit\Helpers;

use KirbyEmailManager\Helpers\FileValidationHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \KirbyEmailManager\Helpers\FileValidationHelper
 */
class FileValidationHelperTest extends TestCase
{
    private $testFiles = [];
    private $translations;

    /**
     * Create test files and translations before the tests
     * @before
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->createTestFiles();
        
        $this->translations = [
            'file' => [
                'too_large' => 'Die Datei ist zu groß. Maximale Größe ist :maxSize MB.',
                'invalid_type' => 'Ungültiger Dateityp.',
                'malicious' => 'Die Datei enthält möglicherweise schädlichen Code.'
            ]
        ];
    }

    /**
     * Delete test files after the tests
     * @after
     */
    protected function tearDown(): void
    {
        foreach ($this->testFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        parent::tearDown();
    }

    /**
     * Create test files for the tests
     * @return void
     */
    private function createTestFiles()
    {
        $validPdf = tempnam(sys_get_temp_dir(), 'test_') . '.pdf';
        file_put_contents($validPdf, '%PDF-1.4' . PHP_EOL); 
        $this->testFiles[] = $validPdf;

        $maliciousFile = tempnam(sys_get_temp_dir(), 'test_') . '.pdf';
        file_put_contents($maliciousFile, '<?php echo "hack"; ?>');
        $this->testFiles[] = $maliciousFile;

        $fakePdf = tempnam(sys_get_temp_dir(), 'test_') . '.pdf';
        file_put_contents($fakePdf, "\x89PNG\r\n\x1a\n"); 
        $this->testFiles[] = $fakePdf;
    }

    /**
     * Test a valid file upload
     * @return void
     */
    public function testValidFileUpload()
    {
        $file = [
            'name' => 'test.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $this->testFiles[0],
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($this->testFiles[0])
        ];

        $fieldConfig = [
            'max_size' => 5 * 1024 * 1024
        ];

        $errors = FileValidationHelper::validateFile($file, $fieldConfig, $this->translations, 'de');
        $this->assertEmpty($errors);
    }

    /**
     * Test a malicious file upload
     * @return void
     */
    public function testMaliciousFileUpload()
    {
        $file = [
            'name' => 'malicious.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $this->testFiles[1],
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($this->testFiles[1])
        ];

        $fieldConfig = [
            'max_size' => 5 * 1024 * 1024
        ];

        $errors = FileValidationHelper::validateFile($file, $fieldConfig, $this->translations, 'de');
        $this->assertArrayHasKey('security', $errors);
    }

    /**
     * Test an invalid mime type upload
     * @return void
     */
    public function testInvalidMimeTypeUpload()
    {
        $file = [
            'name' => 'fake.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $this->testFiles[2], 
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($this->testFiles[2])
        ];

        $errors = FileValidationHelper::validateFile($file, [], $this->translations, 'de');
        $this->assertArrayHasKey('type', $errors);
    }

    /**
     * Test an oversized file upload
     * @return void
     */
    public function testOversizedFileUpload()
    {
        $file = [
            'name' => 'large.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $this->testFiles[2], 
            'error' => UPLOAD_ERR_OK,
            'size' => 6 * 1024 * 1024 
        ];

        $errors = FileValidationHelper::validateFile($file, [
            'max_size' => 5 * 1024 * 1024 
        ], $this->translations, 'de');
        
        $this->assertArrayHasKey('size', $errors);
    }
}