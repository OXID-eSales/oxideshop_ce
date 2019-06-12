<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Adminlinks_List class
 */
class AdminlinksListTest extends \OxidTestCase
{

    /**
     * Adminlinks_List::Init() test case
     *
     * @return null
     */
    public function testInit()
    {
        $oView = $this->getProxyClass('Adminlinks_List');
        $this->assertFalse($oView->getNonPublicVar("_blDesc"));
        $oView->getListSorting();
        $this->assertTrue($oView->getNonPublicVar("_blDesc"));
    }

    /**
     * Adminlinks_List::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Adminlinks_List');
        $this->assertEquals('adminlinks_list.tpl', $oView->render());
    }
}
