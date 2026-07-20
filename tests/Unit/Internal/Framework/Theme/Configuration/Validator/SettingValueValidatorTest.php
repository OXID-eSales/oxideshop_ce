<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Configuration\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Validator\SettingValueValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SettingValueValidatorTest extends TestCase
{
    #[DataProvider('allowedValueProvider')]
    public function testIsValidAcceptsHarmlessValues(string $value): void
    {
        $this->assertTrue((new SettingValueValidator())->isValid($value));
    }

    public static function allowedValueProvider(): array
    {
        return [
            'empty string' => [''],
            'plain value' => ['100*100'],
            'multiline value' => ["first\nsecond"],
            'value mentioning script' => ['description of a script'],
            'html without script tag' => ['<b>bold</b>'],
        ];
    }

    #[DataProvider('forbiddenValueProvider')]
    public function testIsValidRejectsScriptTags(string $value): void
    {
        $this->assertFalse((new SettingValueValidator())->isValid($value));
    }

    public static function forbiddenValueProvider(): array
    {
        return [
            'script tag' => ['<script>alert(1)</script>'],
            'uppercase script tag' => ['<SCRIPT src="evil.js">'],
            'script tag with spaces' => ['< script >alert(1)'],
            'embedded script tag' => ["value\n<script>alert(1)"],
        ];
    }
}
