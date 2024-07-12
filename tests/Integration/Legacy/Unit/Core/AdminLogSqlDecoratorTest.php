<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\TestingLibrary\UnitTestCase;

class AdminLogSqlDecoratorTest extends \PHPUnit\Framework\TestCase
{
    public function testPrepareSqlForLogging()
    {
        $decorator = oxNew(\OxidEsales\EshopCommunity\Core\AdminLogSqlDecorator::class);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Core\AdminLogSqlDecorator::class, $decorator);

        // check if wrapping of string to inserting sql works
        $originalTestString = 'somestring';
        $expectedPattern = "@^insert into .*?'" . $originalTestString . "'\)@s";
        $this->assertMatchesRegularExpression($expectedPattern, $decorator->prepareSqlForLogging($originalTestString));
    }
}
