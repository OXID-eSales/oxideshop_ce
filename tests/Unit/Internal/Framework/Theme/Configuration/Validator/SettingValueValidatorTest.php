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
        $this->assertTrue($this->validator()->isValid($value));
    }

    public static function allowedValueProvider(): array
    {
        return [
            'empty string' => [''],
            'plain value' => ['100*100'],
            'multiline value' => ["first\nsecond"],
            'value mentioning script' => ['description of a script'],
            'less-than in plain text' => ['Sale < 50% & up'],
            'ampersand in text' => ['Müller & Söhne'],
            'url with query parameters' => ['https://example.com/map?a=1&b=2'],
            'associative array line' => ['oxpic1 => 800*600'],
            'word starting with on' => ['online=1'],
            'angle bracket followed by space is inert text' => ['< script >alert(1)'],
        ];
    }

    #[DataProvider('safeUrlProvider')]
    public function testIsValidAcceptsWellFormedHttpUrls(string $value): void
    {
        $this->assertTrue($this->validator()->isValid($value));
    }

    public static function safeUrlProvider(): array
    {
        return [
            'plain https url' => ['https://www.facebook.com/oxidesales'],
            'plain http url' => ['http://twitter.com/OXID_eSales'],
            'uppercase scheme' => ['HTTPS://example.com'],
            'url with fragment' => ['https://example.com/path#section'],
            'url with explicit port' => ['https://example.com:8443/path'],
            'url with parentheses in path' => ['https://en.wikipedia.org/wiki/OXID_eSales_(company)'],
            'url with percent encoded space' => ['https://example.com/a%20b'],
        ];
    }

    #[DataProvider('forbiddenValueProvider')]
    public function testIsValidRejectsScriptTags(string $value): void
    {
        $this->assertFalse($this->validator()->isValid($value));
    }

    #[DataProvider('unsafeUrlProvider')]
    public function testIsValidRejectsUnsafeUrls(string $value): void
    {
        $this->assertFalse($this->validator()->isValid($value));
    }

    public static function unsafeUrlProvider(): array
    {
        return [
            'attribute breakout with img element' =>
                ['https://example.invalid/"><img/onerror=alert(1052103) src=x>'],
            'attribute breakout with event handler' =>
                ['https://example.invalid/" onpointerenter="alert(1052101)'],
            'bare javascript scheme' => ['javascript:alert(1)'],
            'uppercase javascript scheme' => ['JavaScript:alert(1)'],
            'whitespace smuggled scheme' => ["java\tscript:alert(1)"],
            'newline smuggled scheme' => ["java\nscript:alert(1)"],
            'javascript scheme with authority bypass' => ['javascript://%0aalert(1)'],
            'bare vbscript scheme' => ['vbscript:msgbox(1)'],
            'data scheme' => ['data:text/plain,hello'],
            'ftp scheme not allowed' => ['ftp://example.com/file'],
            'scheme without host' => ['http://'],
            'leading whitespace' => ['  https://example.com'],
            'trailing whitespace' => ['https://example.com  '],
            'space inside url' => ['https://exa mple.com'],
            'double quote inside url' => ['https://example.com/"'],
            'angle brackets inside url' => ['https://example.com/<b>'],
            'raw control character' => ["https://example.com/\x01"],
        ];
    }

    public static function forbiddenValueProvider(): array
    {
        return [
            'script tag' => ['<script>alert(1)</script>'],
            'bold tag' => ['<b>bold</b>'],
            'image tag without handler' => ['<img src="logo.png">'],
            'uppercase script tag' => ['<SCRIPT src="evil.js">'],
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

    private function validator(): SettingValueValidator
    {
        return new SettingValueValidator();
    }
}
