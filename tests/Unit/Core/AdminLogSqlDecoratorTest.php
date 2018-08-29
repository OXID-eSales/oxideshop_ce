<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\TestingLibrary\UnitTestCase;

class AdminLogSqlDecoratorTest extends UnitTestCase
{
    public function testPrepareSqlForLogging()
    {
        $decorator = oxNew('OxidEsales\EshopCommunity\Core\AdminLogSqlDecorator');
        $this->assertInstanceOf('\OxidEsales\EshopCommunity\Core\AdminLogSqlDecorator', $decorator);

        // check if wrapping of string to inserting sql works
        $originalTestString = 'somestring';
        $expectedPattern = "@^insert into .*?'" . $originalTestString . "'\)@s";
        $this->assertRegExp($expectedPattern, $decorator->prepareSqlForLogging($originalTestString));
    }
}
