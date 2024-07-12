<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Manufacturer_List class
 */
class ManufacturerListTest extends \OxidTestCase
{

    /**
     * Manufacturer_List::init() test case
     */
    public function testInit()
    {
        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ManufacturerList::class, ["authorize"]);
        $oView->expects($this->any())->method('authorize')->will($this->returnValue(true));
        $oView->init();
        $this->assertEquals("manufacturer_list", $oView->render());
        $this->assertEquals(['oxmanufacturers' => ["oxtitle" => "asc"]], $oView->getListSorting());
    }
}
