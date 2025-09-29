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
            'maxsize' => 5 * 1024 * 1024 // 5MB
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

        $validJpeg = tempnam(sys_get_temp_dir(), 'test_') . '.jpg';
        file_put_contents($validJpeg, "\xFF\xD8\xFF\xE0" . str_repeat('0', 1024));
        $this->testFiles[] = $validJpeg;

        $hiddenFile = tempnam(sys_get_temp_dir(), '.test_') . '.pdf';
        file_put_contents($hiddenFile, '%PDF-1.4' . PHP_EOL);
        $this->testFiles[] = $hiddenFile;

        $invalidSignature = tempnam(sys_get_temp_dir(), 'test_') . '.pdf';
        file_put_contents($invalidSignature, 'INVALID' . PHP_EOL);
        $this->testFiles[] = $invalidSignature;
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

    /**
     * Test for hidden files
     */
    public function testHiddenFile()
    {
        $file = [
            'name' => '.hidden.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $this->testFiles[3],
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($this->testFiles[3])
        ];

        $errors = FileValidationHelper::validateFile($file, $this->fieldConfig, $this->languageCode);
        $this->assertArrayHasKey('error', $errors);
        $this->assertEquals('Versteckte Dateien sind nicht erlaubt.', $errors['error']);
    }

    /**
     * Test for invalid file signature
     */
    public function testInvalidFileSignature()
    {
        $file = [
            'name' => 'invalid.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $this->testFiles[4],
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($this->testFiles[4])
        ];

        $errors = FileValidationHelper::validateFile($file, $this->fieldConfig, $this->languageCode);
        $this->assertArrayHasKey('error', $errors);
        $this->assertEquals('Dateityp stimmt nicht mit der Dateiendung überein.', $errors['error']);
    }

    /**
     * Test for valid JPEG
     */
    public function testValidJpegUpload()
    {
        $file = [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => $this->testFiles[2],
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($this->testFiles[2])
        ];

        $errors = FileValidationHelper::validateFile($file, $this->fieldConfig, $this->languageCode);
        $this->assertEmpty($errors);
    }

    /**
     * Test for MIME-Type Mismatch
     */
    public function testMimeTypeMismatch()
    {
        $file = [
            'name' => 'fake.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => $this->testFiles[0], // PDF-Datei
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($this->testFiles[0])
        ];

        $errors = FileValidationHelper::validateFile($file, $this->fieldConfig, $this->languageCode);
        $this->assertArrayHasKey('error', $errors);
        $this->assertEquals('Dateityp stimmt nicht mit der Dateiendung überein.', $errors['error']);
    }
}