<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

final class RequestTest extends TestCase
{
    private array $getBackup;
    private array $postBackup;
    private array $serverBackup;
    private bool $adminModeBackup;

    public function setUp(): void
    {
        parent::setUp();
        $this->getBackup = $_GET;
        $this->postBackup = $_POST;
        $this->serverBackup = $_SERVER;
        $this->adminModeBackup = Registry::getConfig()->isAdmin();
        $_GET = [];
        $_POST = [];
    }

    public function tearDown(): void
    {
        $_GET = $this->getBackup;
        $_POST = $this->postBackup;
        $_SERVER = $this->serverBackup;
        Registry::getConfig()->setAdminMode($this->adminModeBackup);
        Registry::getSession()->deleteVariable('blIsAdmin');
        parent::tearDown();
    }

    public function testPostParameterWinsOverGetParameter(): void
    {
        $_POST['param'] = 'from-post';
        $_GET['param'] = 'from-get';

        $this->assertSame('from-post', oxNew(Request::class)->getRequestParameter('param'));
    }

    public function testGetParameterIsUsedWhenPostParameterIsAbsent(): void
    {
        $_GET['param'] = 'from-get';

        $this->assertSame('from-get', oxNew(Request::class)->getRequestParameter('param'));
    }

    public function testNullPostParameterFallsThroughToGetParameter(): void
    {
        $_POST['param'] = null;
        $_GET['param'] = 'from-get';

        $this->assertSame('from-get', oxNew(Request::class)->getRequestParameter('param'));
    }

    public function testDefaultIsReturnedOnlyWhenParameterIsAbsentInBothSources(): void
    {
        $this->assertSame('fallback', oxNew(Request::class)->getRequestParameter('missing', 'fallback'));
    }

    #[DataProvider('falsyValueProvider')]
    public function testFalsyValuesAreReturnedInsteadOfDefault(mixed $falsyValue): void
    {
        $_POST['param'] = $falsyValue;

        $this->assertSame($falsyValue, oxNew(Request::class)->getRequestParameter('param', 'fallback'));
    }

    public static function falsyValueProvider(): array
    {
        return [
            'empty string' => [''],
            'zero string' => ['0'],
            'false' => [false],
        ];
    }

    public function testRawParameterKeepsSpecialCharacters(): void
    {
        $_POST['param'] = '<a href="x">&\'';

        $this->assertSame('<a href="x">&\'', oxNew(Request::class)->getRequestParameter('param'));
    }

    #[DataProvider('escapeMapProvider')]
    public function testEscapedParameterReplacesEachSpecialCharacter(string $rawValue, string $escapedValue): void
    {
        $_POST['param'] = $rawValue;

        $this->assertSame($escapedValue, oxNew(Request::class)->getRequestEscapedParameter('param'));
    }

    public static function escapeMapProvider(): array
    {
        return [
            'ampersand' => ['a&b', 'a&amp;b'],
            'less than' => ['a<b', 'a&lt;b'],
            'greater than' => ['a>b', 'a&gt;b'],
            'double quote' => ['a"b', 'a&quot;b'],
            'single quote' => ["a'b", 'a&#039;b'],
            'null byte removed' => ["a" . chr(0) . "b", 'ab'],
            'backslash' => ['a\\b', 'a&#092;b'],
            'newline' => ["a\nb", 'a&#10;b'],
            'carriage return' => ["a\rb", 'a&#13;b'],
        ];
    }

    public function testEscapedParameterAppliesToDefaultValue(): void
    {
        $this->assertSame('a&amp;b', oxNew(Request::class)->getRequestEscapedParameter('missing', 'a&b'));
    }

    public function testEscapedParameterRecursesIntoArrayValuesAndKeys(): void
    {
        $_POST['param'] = ['a"b' => 'x&y', 'plain' => 'z'];

        $this->assertSame(
            ['a&quot;b' => 'x&amp;y', 'plain' => 'z'],
            oxNew(Request::class)->getRequestEscapedParameter('param')
        );
    }

    public function testEscapingIsSkippedForAdminWithAdminSession(): void
    {
        Registry::getConfig()->setAdminMode(true);
        Registry::getSession()->setVariable('blIsAdmin', true);
        $_POST['param'] = 'a&b';

        $this->assertSame('a&b', oxNew(Request::class)->getRequestEscapedParameter('param'));
    }

    public function testEscapingAppliesInAdminModeWithoutAdminSession(): void
    {
        Registry::getConfig()->setAdminMode(true);
        $_POST['param'] = 'a&b';

        $this->assertSame('a&amp;b', oxNew(Request::class)->getRequestEscapedParameter('param'));
    }

    public function testCheckParamSpecialCharsExemptsRawKeys(): void
    {
        $value = ['raw' => 'a&b', 'escaped' => 'c<d'];

        oxNew(Request::class)->checkParamSpecialChars($value, ['raw']);

        $this->assertSame(['raw' => 'a&b', 'escaped' => 'c&lt;d'], $value);
    }

    public function testCheckParamSpecialCharsPassesObjectsThrough(): void
    {
        $object = new stdClass();
        $object->property = 'a&b';

        $result = oxNew(Request::class)->checkParamSpecialChars($object);

        $this->assertSame($object, $result);
        $this->assertSame('a&b', $object->property);
    }

    public function testRequestUrlStripsSessionParametersAndEncodesAmpersands(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/shop/index.php?cl=start&sid=abc123&stoken=XYZ&foo=bar';

        $this->assertSame(
            'index.php?cl=start&amp;foo=bar',
            oxNew(Request::class)->getRequestUrl()
        );
    }

    public function testRequestUrlIsEmptyForPostRequests(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/shop/index.php?cl=start';

        $this->assertSame('', oxNew(Request::class)->getRequestUrl());
    }

    public function testRequestUrlIsEmptyWithoutQueryString(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/shop/index.php';

        $this->assertSame('', oxNew(Request::class)->getRequestUrl());
    }
}
