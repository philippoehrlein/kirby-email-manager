<?php

namespace KirbyEmailManager\Tests\Unit\Helpers;

use KirbyEmailManager\Helpers\SecurityHelper;
use PHPUnit\Framework\TestCase;

class EmailHeaderInjectionTest extends TestCase
{
    /**
     * @test
     * @dataProvider headerInjectionProvider
     */
    public function it_prevents_email_header_injection($input, $expected, $description)
    {
        $result = SecurityHelper::sanitizeEmailHeader($input);
        $this->assertEquals($expected, $result, $description);
    }

    public function headerInjectionProvider()
    {
        return [
            'carriage_return_injection' => [
                "Subject\rBCC: spam@evil.com",
                "SubjectBCC: spam@evil.com",
                "Should remove carriage return"
            ],
            'newline_injection' => [
                "Subject\nBCC: spam@evil.com",
                "SubjectBCC: spam@evil.com",
                "Should remove newline"
            ],
            'crlf_injection' => [
                "Subject\r\nBCC: spam@evil.com",
                "SubjectBCC: spam@evil.com",
                "Should remove CRLF sequence"
            ],
            'null_byte_injection' => [
                "Subject\0BCC: spam@evil.com",
                "SubjectBCC: spam@evil.com",
                "Should remove null bytes"
            ],
            'vertical_tab_injection' => [
                "Subject\x0BBCC: spam@evil.com",
                "SubjectBCC: spam@evil.com",
                "Should remove vertical tabs"
            ],
            'multiple_injections' => [
                "Subject\r\nBCC: spam1@evil.com\r\nBCC: spam2@evil.com",
                "SubjectBCC: spam1@evil.comBCC: spam2@evil.com",
                "Should remove multiple injection attempts"
            ],
            'normal_subject' => [
                "Normal Subject",
                "Normal Subject",
                "Should keep normal subjects unchanged"
            ],
            'subject_with_spaces' => [
                "  Subject with spaces  ",
                "Subject with spaces",
                "Should trim whitespace"
            ],
            'german_umlauts' => [
                "Anfrage über Produkt",
                "Anfrage über Produkt",
                "Should preserve umlauts"
            ],
            'empty_string' => [
                "",
                "",
                "Should handle empty strings"
            ],
            'null_input' => [
                null,
                "",
                "Should handle null input"
            ],
            'complex_attack' => [
                "Test\r\nBCC: spam@evil.com\r\nX-Priority: 1\r\nX-Mailer: Evil",
                "TestBCC: spam@evil.comX-Priority: 1X-Mailer: Evil",
                "Should handle complex multi-header injection"
            ],
            'username_injection' => [
                "Max Mustermann\r\nBCC: spam@evil.com",
                "Max MustermannBCC: spam@evil.com",
                "Should prevent injection via username field"
            ],
            'topic_injection' => [
                "Support-Anfrage\r\nBCC: spam@evil.com",
                "Support-AnfrageBCC: spam@evil.com",
                "Should prevent injection via topic field"
            ]
        ];
    }

    /**
     * @test
     */
    public function it_handles_very_long_strings()
    {
        $longString = str_repeat("A", 10000) . "\r\nBCC: spam@evil.com";
        $result = SecurityHelper::sanitizeEmailHeader($longString);
        
        // Ensure newlines are removed (prevents header injection)
        $this->assertStringNotContainsString("\r", $result);
        $this->assertStringNotContainsString("\n", $result);
        
        // The text "BCC: spam@evil.com" remains (harmless without newlines)
        $this->assertStringContainsString("BCC: spam@evil.com", $result);
        
        // Verify it's all one line (no actual header injection possible)
        $this->assertEquals(str_repeat("A", 10000) . "BCC: spam@evil.com", $result);
    }

    /**
     * @test
     */
    public function it_preserves_unicode_characters()
    {
        $unicode = "Test 🎉 Emoji und Überschrift";
        $result = SecurityHelper::sanitizeEmailHeader($unicode);
        
        $this->assertEquals($unicode, $result);
    }
}
