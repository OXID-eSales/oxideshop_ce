<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Database\Logger;

use OxidEsales\EshopCommunity\Internal\Framework\Database\Logger\QueryFilter;

class QueryFilterTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @return array
     */
    public function providerTestFiltering()
    {
        $data = [
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

        return $data;
    }

    /**
     * @param string $query
     * @param array  $skipLogTags
     * @param bool   $expected
     *
     * @dataProvider providerTestFiltering
     */
    public function testFiltering(string $query, array $skipLogTags, bool $expected)
    {
        $queryFilter = new QueryFilter();

        $this->assertEquals($expected, $queryFilter->shouldLogQuery($query, $skipLogTags));
    }
}
