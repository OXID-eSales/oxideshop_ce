<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Database\Logger;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Logger\QueryFilter;

final class QueryFilterTest extends TestCase
{
    public static function providerTestFiltering(): array
    {
        return [
            [
                "select * from oxarticles",
                [],
                false
            ],
            [
                "delete * from oxarticles",
                [],
                true
            ],
            [
                "insert into oxarticles values ('some values')",
                [],
                true
            ],
            [
                "update oxarticles set oxtitle = 'other title' where oxid = '_someid' ",
                [],
                true
            ],
            [
                "UPDATE oxarticles set oxtitle = 'other title' where oxid = '_someid' ",
                [],
                true
            ],
            [
                "update oxarticles set oxtitle = 'other title' where oxid = '_someid' ",
                [
                    'oxarticles'
                ],
                false
            ],
            [
                "yadda yadda yadda insert into blabla ",
                [
                    'ox'
                ],
                true
            ],
            [
                "yadda oxyadda yadda insert into oxblabla ",
                [
                    'ox'
                ],
                false
            ],
            [
                "yadda yadda yadda oxsession blabla ",
                [],
                false
            ],
            [
                "yadda yadda yadda oxcache blabla ",
                [],
                false
            ],
        ];
    }


    #[DataProvider('providerTestFiltering')]
    public function testFiltering(string $query, array $skipLogTags, bool $expected): void
    {
        $queryFilter = new QueryFilter();

        $this->assertEquals($expected, $queryFilter->shouldLogQuery($query, $skipLogTags));
    }
}
