<?php

namespace KirbyEmailManager\Tests\Unit\Helpers;

use KirbyEmailManager\Helpers\BlacklistHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \KirbyEmailManager\Helpers\BlacklistHelper
 */
class BlacklistHelperTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_not_blocked_for_empty_blacklist()
    {
        $data = ['name' => 'John', 'message' => 'Hello World'];
        $blacklist = [];
        
        $result = BlacklistHelper::checkAgainstList($data, $blacklist);
        
        $this->assertFalse($result['blocked']);
        $this->assertNull($result['matched']);
    }

    /**
     * @test
     */
    public function it_blocks_when_blacklisted_term_found()
    {
        $data = ['name' => 'John', 'message' => 'Buy cheap casino chips'];
        $blacklist = ['casino', 'viagra'];
        
        $result = BlacklistHelper::checkAgainstList($data, $blacklist);
        
        $this->assertTrue($result['blocked']);
        $this->assertEquals('casino', $result['matched']);
    }

    /**
     * @test
     */
    public function it_is_case_insensitive()
    {
        $data = ['message' => 'Buy CASINO chips'];
        $blacklist = ['casino'];
        
        $result = BlacklistHelper::checkAgainstList($data, $blacklist);
        
        $this->assertTrue($result['blocked']);
        $this->assertEquals('casino', $result['matched']);
    }

    /**
     * @test
     */
    public function it_matches_partial_strings()
    {
        $data = ['email' => 'test@tempmail.com'];
        $blacklist = ['@tempmail.com'];
        
        $result = BlacklistHelper::checkAgainstList($data, $blacklist);
        
        $this->assertTrue($result['blocked']);
        $this->assertEquals('@tempmail.com', $result['matched']);
    }

    /**
     * @test
     */
    public function it_does_not_block_clean_data()
    {
        $data = [
            'name' => 'Max Mustermann',
            'email' => 'max@example.com',
            'message' => 'Ich habe eine Frage zu Ihrem Produkt.'
        ];
        $blacklist = ['casino', 'viagra', '@tempmail.com'];
        
        $result = BlacklistHelper::checkAgainstList($data, $blacklist);
        
        $this->assertFalse($result['blocked']);
        $this->assertNull($result['matched']);
    }

    /**
     * @test
     */
    public function it_skips_system_fields()
    {
        $data = [
            'csrf' => 'casino-token',
            'timestamp' => '12345',
            'submit' => 'casino',
            'website_hp_' => 'casino',
            'gdpr' => 'casino',
            'message' => 'Normal message'
        ];
        $blacklist = ['casino'];
        
        $result = BlacklistHelper::checkAgainstList($data, $blacklist);
        
        $this->assertFalse($result['blocked']);
    }

    /**
     * @test
     */
    public function it_handles_nested_arrays()
    {
        $data = [
            'name' => 'John',
            'tags' => ['normal', 'casino chips', 'test']
        ];
        $blacklist = ['casino'];
        
        $result = BlacklistHelper::checkAgainstList($data, $blacklist);
        
        $this->assertTrue($result['blocked']);
        $this->assertEquals('casino', $result['matched']);
    }

    /**
     * @test
     */
    public function it_handles_date_range_fields()
    {
        $data = [
            'date_range' => ['start' => '2024-01-01', 'end' => '2024-12-31'],
            'message' => 'Booking request'
        ];
        $blacklist = ['casino'];
        
        $result = BlacklistHelper::checkAgainstList($data, $blacklist);
        
        $this->assertFalse($result['blocked']);
    }

    /**
     * @test
     */
    public function it_ignores_non_string_blacklist_entries()
    {
        $data = ['message' => 'Hello World'];
        $blacklist = [null, '', 123, ['array'], 'casino'];
        
        $result = BlacklistHelper::checkAgainstList($data, $blacklist);
        
        $this->assertFalse($result['blocked']);
    }

    /**
     * @test
     */
    public function it_handles_empty_form_data()
    {
        $data = [];
        $blacklist = ['casino'];
        
        $result = BlacklistHelper::checkAgainstList($data, $blacklist);
        
        $this->assertFalse($result['blocked']);
    }

    /**
     * @test
     */
    public function it_handles_unicode_characters()
    {
        $data = ['message' => 'Спам сообщение'];
        $blacklist = ['Спам'];
        
        $result = BlacklistHelper::checkAgainstList($data, $blacklist);
        
        $this->assertTrue($result['blocked']);
        $this->assertEquals('Спам', $result['matched']);
    }

    /**
     * @test
     */
    public function it_handles_german_umlauts()
    {
        $data = ['message' => 'Günstige Angebote'];
        $blacklist = ['günstige'];
        
        $result = BlacklistHelper::checkAgainstList($data, $blacklist);
        
        $this->assertTrue($result['blocked']);
    }

    /**
     * @test
     * @dataProvider flattenDataProvider
     */
    public function it_flattens_form_data_correctly($input, $expected)
    {
        $result = BlacklistHelper::flattenFormData($input);
        $this->assertEquals($expected, $result);
    }

    public function flattenDataProvider()
    {
        return [
            'simple_strings' => [
                ['name' => 'John', 'email' => 'john@example.com'],
                ['john', 'john@example.com']
            ],
            'with_array' => [
                ['tags' => ['Tag1', 'Tag2']],
                ['tag1', 'tag2']
            ],
            'skips_system_fields' => [
                ['csrf' => 'token', 'name' => 'John'],
                ['john']
            ],
            'empty_values_ignored' => [
                ['name' => '', 'email' => 'test@test.com'],
                ['test@test.com']
            ],
            'mixed_types' => [
                ['name' => 'John', 'age' => 25, 'active' => true],
                ['john']
            ]
        ];
    }
}
