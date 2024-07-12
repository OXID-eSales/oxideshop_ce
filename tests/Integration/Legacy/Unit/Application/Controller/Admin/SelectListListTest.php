<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for SelectList_List class
 */
class SelectListListTest extends \OxidTestCase
{

    /**
     * SelectList_List::Init() test case
     */
    public function testInit()
    {
        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\SelectListList::class, ["authorize"]);
        $oView->expects($this->any())->method('authorize')->will($this->returnValue(true));
        $oView->init();
        $oView->render();
        $this->assertEquals(['oxselectlist' => ["oxtitle" => "asc"]], $oView->getListSorting());
    }

    /**
     * SelectList_List::Render() test case
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('SelectList_List');
        $this->assertEquals('selectlist_list', $oView->render());
    }
}
