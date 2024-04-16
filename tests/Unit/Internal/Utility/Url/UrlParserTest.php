<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Utility\Url;

use PHPUnit\Framework\Attributes\DataProvider;
use OxidEsales\EshopCommunity\Internal\Utility\Url\UrlParser;
use PHPUnit\Framework\TestCase;

final class UrlParserTest extends TestCase
{
    /**
     * @param $url
     * @param $exp
     */
    #[DataProvider('getPathWithoutTrailingSlashDataProvider')]
    public function testGetPathWithoutTrailingSlashWithDataProvider(string $url, string $exp): void
    {
        $act = (new UrlParser())->getPathWithoutTrailingSlash($url);

        $this->assertSame($exp, $act);
    }

    public static function getPathWithoutTrailingSlashDataProvider(): array
    {
        return [
            ['https://abc.def.com', ''],
            ['https://abc.def.com/', ''],
            ['http://abc.def.com/some-path/component-string.php', '/some-path/component-string.php'],
            ['http://abc.def.com/some-path/component-string/', '/some-path/component-string'],
        ];
    }
}
