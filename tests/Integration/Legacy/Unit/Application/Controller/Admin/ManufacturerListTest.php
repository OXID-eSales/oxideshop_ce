<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Manufacturer_List class
 */
class ManufacturerListTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Manufacturer_List::init() test case
     */
    public function testInit()
    {
        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ManufacturerList::class, ["authorize"]);
        $oView->method('authorize')->willReturn(true);
        $oView->init();
        $this->assertSame("manufacturer_list", $oView->render());
        $this->assertSame(['oxmanufacturers' => ["oxtitle" => "asc"]], $oView->getListSorting());
    }
}
