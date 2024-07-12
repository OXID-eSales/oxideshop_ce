<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for SelectList_List class
 */
class SelectListListTest extends \PHPUnit\Framework\TestCase
{

    /**
     * SelectList_List::Init() test case
     */
    public function testInit()
    {
        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\SelectListList::class, ["authorize"]);
        $oView->method('authorize')->willReturn(true);
        $oView->init();
        $oView->render();
        $this->assertSame(['oxselectlist' => ["oxtitle" => "asc"]], $oView->getListSorting());
    }

    /**
     * SelectList_List::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('SelectList_List');
        $this->assertSame('selectlist_list', $oView->render());
    }
}
