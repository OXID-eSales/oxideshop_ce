<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for News_List class
 */
class NewsListTest extends \OxidTestCase
{

    /**
     * News_List::Init() test case
     *
     * @return null
     */
    public function testInit()
    {
        $oView = $this->getProxyClass('News_List');
        $this->assertFalse($oView->getNonPublicVar("_blDesc"));
        $oView->getListSorting();
        $this->assertTrue($oView->getNonPublicVar("_blDesc"));
    }

    /**
     * News_List::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('News_List');
        $this->assertEquals('news_list.tpl', $oView->render());
    }
}
