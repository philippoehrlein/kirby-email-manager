<?php

namespace KirbyEmailManager\Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;

abstract class ValidationHelperTest extends TestCase
{
  protected $translations;
  protected $languageCode = 'de';

  protected function setUp(): void
  {
    $this->translations = require __DIR__ . '/../../../translations/de.php';
  }
}
