<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Utility\Url;

use OxidEsales\EshopCommunity\Internal\Utility\Url\UrlParser;
use PHPUnit\Framework\TestCase;

final class UrlParserTest extends TestCase
{
    /**
     * @dataProvider getPathWithoutTrailingSlashDataProvider
     * @param $url
     * @param $exp
     */
    public function testGetPathWithoutTrailingSlashWithDataProvider(string $url, string $exp): void
    {
        $act = (new UrlParser())->getPathWithoutTrailingSlash($url);

        $this->assertSame($exp, $act);
    }

    public function getPathWithoutTrailingSlashDataProvider(): array
    {
        return [
            ['https://abc.def.com', ''],
            ['https://abc.def.com/', ''],
            ['http://abc.def.com/some-path/component-string.php', '/some-path/component-string.php'],
            ['http://abc.def.com/some-path/component-string/', '/some-path/component-string'],
        ];
    }
}
