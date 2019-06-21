<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

/**
 * Tests for oxwServiceList class
 */
class ServiceListTest extends \OxidTestCase
{

    /**
     * Testing oxwServiceList::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oServiceList = oxNew('oxwServiceList');
        $this->assertEquals('widget/footer/services.tpl', $oServiceList->render());
    }
}
