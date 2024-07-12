<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

use \oxTestModules;

/**
 * Tests for oxwServiceMenu class
 */
class ServiceMenuTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Testing oxwServiceMenu::getCompareItemsCnt()
     */
    public function testGetCompareItemsCnt()
    {
        $oCompare = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, ["getCompareItemsCnt"]);
        $oCompare->expects($this->once())->method("getCompareItemsCnt")->willReturn(10);
        oxTestModules::addModuleObject('compare', $oCompare);

        $oServiceMenu = oxNew('oxwServiceMenu');
        $this->assertSame(10, $oServiceMenu->getCompareItemsCnt());
    }

    /**
     * Testing oxwServiceMenu::getCompareItems()
     */
    public function testGetCompareItems()
    {
        $aItems = ["testId1" => "testVal1", "testId2" => "testVal2", "testId3" => "testVal3"];
        $oCompare = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, ["getCompareItems"]);
        $oCompare->expects($this->once())->method("getCompareItems")->willReturn($aItems);
        oxTestModules::addModuleObject('compare', $oCompare);

        $oServiceMenu = oxNew('oxwServiceMenu');
        $this->assertSame($aItems, $oServiceMenu->getCompareItems());
    }

    /**
     * Testing oxwServiceMenu::getCompareItems()
     */
    public function testGetCompareItemsInJson()
    {
        $aItems = ["testId1" => "testVal1", "testId2" => "testVal2", "testId3" => "testVal3"];
        $aResult = '{"testId1":"testVal1","testId2":"testVal2","testId3":"testVal3"}';
        $oCompare = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, ["getCompareItems"]);
        $oCompare->expects($this->once())->method("getCompareItems")->willReturn($aItems);
        oxTestModules::addModuleObject('compare', $oCompare);

        $oServiceMenu = oxNew('oxwServiceMenu');
        $this->assertSame($aResult, $oServiceMenu->getCompareItems(true));
    }
}
