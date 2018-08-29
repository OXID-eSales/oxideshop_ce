<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

/**
 * Tests for oxwInformation class
 */
class InformationTest extends \OxidTestCase
{

    /**
     * Test render of default template
     */
    public function testRender()
    {
        $oInformation = oxNew('oxwInformation');

        $this->assertEquals('widget/footer/info.tpl', $oInformation->render());
    }

    /**
     * Test services count.
     */
    public function testGetServicesList_ChecksServicesCount()
    {
        $aServicesList = $this->_getServicesList();
        $this->assertEquals(6, count($aServicesList));
    }

    /**
     * Returns services list- array of objects.
     *
     * @return array
     */
    protected function _getServicesList()
    {
        $oInformation = oxNew('oxwInformation');
        $aServicesList = $oInformation->getServicesList();

        return $aServicesList;
    }
}
