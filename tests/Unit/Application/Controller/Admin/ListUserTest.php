<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use DOMDocument;

/**
 * Tests for List_User class
 */
class ListUserTest extends \OxidTestCase
{

    /**
     * List_User::GetViewListSize() test case
     *
     * @return null
     */
    public function testGetViewListSize()
    {
        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListUser::class, array("_getUserDefListSize"));
        $oView->expects($this->once())->method('_getUserDefListSize')->will($this->returnValue(999));
        $this->assertEquals(999, $oView->UNITgetViewListSize());
    }

    /**
     * List_User::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $oNavTree = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, array("getDomXml"));
        $oNavTree->expects($this->once())->method('getDomXml')->will($this->returnValue(new DOMDocument));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListUser::class, array("getNavigation"));
        $oView->expects($this->at($iCnt++))->method('getNavigation')->will($this->returnValue($oNavTree));
        $this->assertEquals("list_user.tpl", $oView->render());
    }
}
