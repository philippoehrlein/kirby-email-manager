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
    private $languageCode = 'de';
    private $fieldConfig;

    /**
     * Create test files and translations before the tests
     * @before
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->createTestFiles();
        
        $this->fieldConfig = [
            'max_size' => 5 * 1024 * 1024 // 5MB
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

        $phpFile = tempnam(sys_get_temp_dir(), 'test_') . '.pdf';
        file_put_contents($phpFile, '<?php echo "test"; ?>');
        $this->testFiles[] = $phpFile;
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

        $errors = FileValidationHelper::validateFile($file, $this->fieldConfig, $this->languageCode);
        $this->assertEmpty($errors);
    }

    /**
     * Test a malicious file upload
     * @return void
     */
    public function testMaliciousFileUpload()
    {
        $file = [
            'name' => 'malicious.php',
            'type' => 'application/pdf',
            'tmp_name' => $this->testFiles[1],
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($this->testFiles[1])
        ];

        $errors = FileValidationHelper::validateFile($file, $this->fieldConfig, $this->languageCode);
        $this->assertArrayHasKey('error', $errors);
    }

    /**
     * Test an invalid mime type upload
     * @return void
     */
    public function testInvalidMimeType()
    {
        $file = [
            'name' => 'test.xyz',
            'type' => 'application/xyz',
            'tmp_name' => $this->testFiles[0],
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($this->testFiles[0])
        ];

        $errors = FileValidationHelper::validateFile($file, $this->fieldConfig, $this->languageCode);
        $this->assertArrayHasKey('error', $errors);
    }

    /**
     * Test an oversized file upload
     * @return void
     */
    public function testFileTooLarge()
    {
        $file = [
            'name' => 'large.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $this->testFiles[2],
            'error' => UPLOAD_ERR_OK,
            'size' => 6 * 1024 * 1024
        ];

        $errors = FileValidationHelper::validateFile($file, $this->fieldConfig, $this->languageCode);
        $this->assertArrayHasKey('error', $errors);
    }
}