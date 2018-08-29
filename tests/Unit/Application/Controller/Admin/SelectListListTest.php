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
     *
     * @return null
     */
    public function testInit()
    {
        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\SelectListList::class, array("_authorize"));
        $oView->expects($this->any())->method('_authorize')->will($this->returnValue(true));
        $oView->init();
        $oView->render();
        $this->assertEquals(array('oxselectlist' => array("oxtitle" => "asc")), $oView->getListSorting());
    }

    /**
     * SelectList_List::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('SelectList_List');
        $this->assertEquals('selectlist_list.tpl', $oView->render());
    }
}
