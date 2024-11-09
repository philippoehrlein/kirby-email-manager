<?php

namespace KirbyEmailManager\Tests\Unit\Helpers;

use KirbyEmailManager\Helpers\SecurityHelper;
use PHPUnit\Framework\TestCase;

class SecurityHelperTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ob_start();
    }

    protected function tearDown(): void
    {
        ob_end_clean();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function it_escapes_very_long_html_strings()
    {
        // Lese den langen Text aus einer Datei
        $longHtml = file_get_contents(__DIR__ . '/../fixtures/long_text.html');

        $escapedHtml = SecurityHelper::escapeHtml($longHtml);

        // Optional: Prüfe auf bestimmte Zeichen oder dass keine Scripts enthalten sind
        $this->assertStringContainsString('&lt;', $escapedHtml);
        $this->assertStringNotContainsString('<script>', $escapedHtml);
    }

    /**
     * @test
     * @dataProvider xssDataProvider
     */
    public function it_escapes_html_special_characters($input, $expected)
    {
        $result = SecurityHelper::escapeHtml($input);
        $this->assertEquals($expected, $result);
    }

    public function xssDataProvider()
    {
        return [
            'basic_xss' => [
                '<script>alert("XSS")</script>',
                '&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;'
            ],
            'attribute_xss' => [
                '" onclick="alert(\'XSS\')"',
                '&quot; onclick=&quot;alert(&#039;XSS&#039;)&quot;'
            ],
            'null_input' => [
                null,
                ''
            ],
            'non_string_input' => [
                ['test' => 'value'],
                ''
            ]
        ];
    }

    /**
     * @test
     * @dataProvider filenameProvider
     */
    public function it_sanitizes_filenames($input, $expected)
    {
        $result = SecurityHelper::sanitizeFilename($input);
        $this->assertEquals($expected, $result);
    }

    public function filenameProvider()
    {
        return [
            'normal_filename' => ['test.txt', 'test.txt'],
            'path_traversal' => ['../test.txt', 'test.txt'],
            'nested_path' => ['/var/www/test.txt', 'test.txt'],
            'null_input' => [null, '']
        ];
    }

    /**
     * @test
     * @dataProvider urlValidationProvider
     */
    public function it_validates_urls($url, $expected)
    {
        $result = SecurityHelper::validateUrl($url);
        $this->assertEquals($expected, $result);
    }

    public function urlValidationProvider()
    {
        return [
            'valid_http' => ['http://example.com', true],
            'valid_https' => ['https://example.com', true],
            'invalid_protocol' => ['ftp://example.com', false],
            'malformed_url' => ['not_a_url', false],
            'empty_url' => ['', false],
            'null_url' => [null, false],
            'javascript_url' => ['javascript:alert(1)', false],
            'data_url' => ['data:text/html,<script>alert(1)</script>', false]
        ];
    }

    /**
     * @test
     * @dataProvider emailValidationProvider
     */
    public function it_validates_email_addresses($email, $expected)
    {
        $result = SecurityHelper::validateEmail($email);
        $this->assertEquals($expected, $result);
    }

    public function emailValidationProvider()
    {
        return [
            'valid_email' => ['test@example.com', true],
            'invalid_email' => ['not-an-email', false],
            'empty_email' => ['', false],
            'null_email' => [null, false],
            'sql_injection' => ["' OR '1'='1", false],
            'multiple_at' => ['test@@example.com', false],
            'special_chars' => ['test+filter@example.com', true]
        ];
    }

    /**
     * @test
     * @dataProvider sanitizeDataProvider
     */
    public function it_sanitizes_input($input, $expected)
    {
        $result = SecurityHelper::sanitize($input);
        $this->assertEquals($expected, $result);
    }

    public function sanitizeDataProvider()
    {
        return [
            'normal_text' => ['Hello World', 'Hello World'],
            'html_input' => ['<p>Test</p>', '&lt;p&gt;Test&lt;/p&gt;'],
            'special_chars' => ['Test & More', 'Test &amp; More'],
            'quotes' => ['Test "quote" \'single\'', 'Test &quot;quote&quot; &#039;single&#039;'],
            'null_input' => [null, ''],
            'empty_string' => ['', '']
        ];
    }

    /**
     * @test
     * @dataProvider formDataProvider
     */
    public function it_sanitizes_and_validates_form_data($input, $expected)
    {
        $result = SecurityHelper::sanitizeAndValidateFormData($input);
        $this->assertSame($expected, $result);
    }

    public function formDataProvider()
    {
        return [
            'simple_data' => [
                ['name' => 'John', 'email' => 'test@example.com'],
                ['name' => 'John', 'email' => 'test@example.com']
            ],
            'data_with_html' => [
                ['message' => '<script>alert("XSS")</script>'],
                ['message' => '&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;']
            ],
            'array_data' => [
                ['tags' => ['<b>bold</b>', '<i>italic</i>']],
                ['tags' => ['&lt;b&gt;bold&lt;/b&gt;', '&lt;i&gt;italic&lt;/i&gt;']]
            ],
            'null_input' => [
                null,
                []
            ]
        ];
    }

    /**
     * @test
     * @dataProvider csrfTokenProvider
     */
    public function it_validates_csrf_token_edge_cases($input, $expected)
    {
        $result = SecurityHelper::validateCSRFToken($input);
        $this->assertEquals($expected, $result);
    }

    public function csrfTokenProvider()
    {
        return [
            'null_token' => [null, false],
            'empty_string' => ['', false],
            'boolean_false' => [false, false],
            'integer_zero' => [0, false],
            'empty_array' => [[], false],
        ];
    }
}