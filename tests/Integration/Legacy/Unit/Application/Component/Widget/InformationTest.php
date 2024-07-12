<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

/**
 * Tests for oxwInformation class
 */
class InformationTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Test render of default template
     */
    public function testRender()
    {
        $oInformation = oxNew('oxwInformation');

        $this->assertSame('widget/footer/info', $oInformation->render());
    }

    /**
     * Test services count.
     */
    public function testGetServicesList_ChecksServicesCount()
    {
        $aServicesList = $this->getServicesList();
        $this->assertCount(6, $aServicesList);
    }

    /**
     * Returns services list- array of objects.
     *
     * @return array
     */
    protected function getServicesList()
    {
        $oInformation = oxNew('oxwInformation');

        return $oInformation->getServicesList();
    }
}
