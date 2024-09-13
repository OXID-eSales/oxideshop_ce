<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Setup\Htaccess;

use Iterator;
use OxidEsales\EshopCommunity\Internal\Setup\Htaccess\InvalidShopUrlException;
use OxidEsales\EshopCommunity\Internal\Setup\Htaccess\ShopBaseUrl;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ShopBaseUrlTest extends TestCase
{
    public static function validUrlsDataProvider(): Iterator
    {
        yield ['https://www.oxid-esales.com/en/'];
        yield ['http://localhost.local'];
        yield ['https://äää.üüü'];
        yield ['https://127.0.0.1'];
    }

    #[DataProvider('validUrlsDataProvider')]
    public function testWithValidUrls(string $url): void
    {
        $object = new ShopBaseUrl($url);

        $this->assertEquals($url, $object->getUrl());
    }

    public static function invalidUrlsDataProvider(): Iterator
    {
        yield [''];
        yield ['123.456'];
        yield ['address.com'];
    }

    #[DataProvider('invalidUrlsDataProvider')]
    public function testWithInvalidUrls(string $url): void
    {
        $this->expectException(InvalidShopUrlException::class);

        new ShopBaseUrl($url);
    }
}
