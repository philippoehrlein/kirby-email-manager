<?php

namespace KirbyEmailManager\Tests\Unit\Helpers;

use KirbyEmailManager\Helpers\EmailHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \KirbyEmailManager\Helpers\EmailHelper::getTemplates
 */
class EmailHelperTest extends TestCase
{
    private string $tempTemplatesDir;

    private $kirbyWithTemplates;

    private $kirbyWithoutTemplates;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempTemplatesDir = sys_get_temp_dir() . '/em_test_' . uniqid();
        mkdir($this->tempTemplatesDir . '/emails/existing-template', 0755, true);
        file_put_contents(
            $this->tempTemplatesDir . '/emails/existing-template/mail.text.php',
            '<?php echo "test";'
        );

        $this->kirbyWithTemplates = $this->createKirbyMock($this->tempTemplatesDir);
        $this->kirbyWithoutTemplates = $this->createKirbyMock(sys_get_temp_dir() . '/em_empty_' . uniqid());
    }

    protected function tearDown(): void
    {
        $this->removeDir($this->tempTemplatesDir);
        parent::tearDown();
    }

    private function createKirbyMock(string $templatesRoot): object
    {
        $mock = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['root'])
            ->getMock();
        $mock->method('root')->with('templates')->willReturn($templatesRoot);

        return $mock;
    }

    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->removeDir($path) : unlink($path);
        }
        rmdir($dir);
    }

    /**
     * @test
     */
    public function it_returns_custom_templates_when_they_exist(): void
    {
        $result = EmailHelper::getTemplates('existing-template', $this->kirbyWithTemplates, true);

        $this->assertEquals('existing-template/mail', $result['mail']);
        $this->assertEquals('existing-template/reply', $result['reply']);
        $this->assertFalse($result['isDefault']);
    }

    /**
     * @test
     */
    public function it_returns_default_templates_when_custom_missing(): void
    {
        $result = EmailHelper::getTemplates('non-existent', $this->kirbyWithoutTemplates, true);

        $this->assertEquals('default/mail', $result['mail']);
        $this->assertEquals('default/reply', $result['reply']);
        $this->assertTrue($result['isDefault']);
    }

    /**
     * @test
     */
    public function it_returns_null_reply_when_no_reply_field(): void
    {
        $result = EmailHelper::getTemplates('existing-template', $this->kirbyWithTemplates, false);

        $this->assertEquals('existing-template/mail', $result['mail']);
        $this->assertNull($result['reply']);
        $this->assertFalse($result['isDefault']);
    }

    /**
     * @test
     */
    public function it_returns_null_reply_when_default_and_no_reply_field(): void
    {
        $result = EmailHelper::getTemplates('non-existent', $this->kirbyWithoutTemplates, false);

        $this->assertEquals('default/mail', $result['mail']);
        $this->assertNull($result['reply']);
        $this->assertTrue($result['isDefault']);
    }
}
