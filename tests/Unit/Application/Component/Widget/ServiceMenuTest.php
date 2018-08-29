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
class ServiceMenuTest extends \OxidTestCase
{
    /**
     * Testing oxwServiceMenu::getCompareItemsCnt()
     *
     * @return null
     */
    public function testGetCompareItemsCnt()
    {
        $oCompare = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, array("getCompareItemsCnt"));
        $oCompare->expects($this->once())->method("getCompareItemsCnt")->will($this->returnValue(10));
        oxTestModules::addModuleObject('compare', $oCompare);

        $oServiceMenu = oxNew('oxwServiceMenu');
        $this->assertEquals(10, $oServiceMenu->getCompareItemsCnt());
    }

    /**
     * Testing oxwServiceMenu::getCompareItems()
     *
     * @return null
     */
    public function testGetCompareItems()
    {
        $aItems = array("testId1" => "testVal1", "testId2" => "testVal2", "testId3" => "testVal3");
        $oCompare = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, array("getCompareItems"));
        $oCompare->expects($this->once())->method("getCompareItems")->will($this->returnValue($aItems));
        oxTestModules::addModuleObject('compare', $oCompare);

        $oServiceMenu = oxNew('oxwServiceMenu');
        $this->assertEquals($aItems, $oServiceMenu->getCompareItems());
    }

    /**
     * Testing oxwServiceMenu::getCompareItems()
     *
     * @return null
     */
    public function testGetCompareItemsInJson()
    {
        $aItems = array("testId1" => "testVal1", "testId2" => "testVal2", "testId3" => "testVal3");
        $aResult = '{"testId1":"testVal1","testId2":"testVal2","testId3":"testVal3"}';
        $oCompare = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, array("getCompareItems"));
        $oCompare->expects($this->once())->method("getCompareItems")->will($this->returnValue($aItems));
        oxTestModules::addModuleObject('compare', $oCompare);

        $oServiceMenu = oxNew('oxwServiceMenu');
        $this->assertEquals($aResult, $oServiceMenu->getCompareItems(true));
    }
}
