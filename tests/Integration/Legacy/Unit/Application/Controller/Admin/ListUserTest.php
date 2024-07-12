<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use DOMDocument;
use OxidEsales\Eshop\Application\Controller\Admin\NavigationTree;

/**
 * Tests for List_User class
 */
class ListUserTest extends \OxidTestCase
{

    /**
     * List_User::GetViewListSize() test case
     */
    public function testGetViewListSize()
    {
        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListUser::class, ["getUserDefListSize"]);
        $oView->expects($this->once())->method('getUserDefListSize')->will($this->returnValue(999));
        $this->assertEquals(999, $oView->getViewListSize());
    }

    /**
     * List_User::Render() test case
     */
    public function testRender()
    {
        $oNavTree = $this->getMock(NavigationTree::class, ["getDomXml"]);
        $oNavTree->expects($this->once())->method('getDomXml')->will($this->returnValue(new DOMDocument()));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ListUser::class, ["getNavigation"]);
        $oView->expects($this->once())->method('getNavigation')->will($this->returnValue($oNavTree));
        $this->assertEquals("list_user", $oView->render());
    }
}
