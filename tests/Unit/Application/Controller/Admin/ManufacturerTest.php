<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Manufacturer class
 */
class ManufacturerTest extends \OxidTestCase
{

    /**
     * Manufacturer::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Manufacturer');
        $this->assertEquals('manufacturer.tpl', $oView->render());
    }
}
