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
            'image without handler' => ['<img src="logo.png">'],
            'less-than in plain text' => ['Sale < 50% & up'],
            'ampersand in text' => ['Müller & Söhne'],
            'url with query parameters' => ['https://example.com/map?a=1&b=2'],
            'associative array line' => ['oxpic1 => 800*600'],
            'word starting with on' => ['online=1'],
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
            'iframe tag' => ['<iframe src="//evil"></iframe>'],
            'object tag' => ['<object data="evil"></object>'],
            'embed tag' => ['<embed src="evil">'],
            'svg tag' => ['<svg onload="alert(1)">'],
            'style tag' => ['<style>body{background:url(evil)}</style>'],
            'base tag' => ['<base href="//evil">'],
            'meta refresh' => ['<meta http-equiv="refresh" content="0;url=//evil">'],
            'image with onerror handler' => ['<img src=x onerror=alert(1)>'],
            'div with event handler' => ['<div onmouseover="alert(1)">hover</div>'],
            'javascript scheme link' => ['<a href="javascript:alert(1)">click</a>'],
            'vbscript scheme link' => ['<a href="vbscript:msgbox(1)">click</a>'],
        ];
    }
}
