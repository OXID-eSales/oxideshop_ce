<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

/**
 * Tests for oxwCategoryTree class
 */
class CategoryTreeTest extends \OxidTestCase
{

    /**
     * Testing oxwCategoryTree::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oCategoryTree = oxNew('oxwCategoryTree');
        $this->assertEquals('widget/sidebar/categorytree.tpl', $oCategoryTree->render());
    }

    /**
     * Testing oxwCategoryTree::render()
     *
     * @return null
     */
    public function testRenderDifferentTemplate()
    {
        $this->getConfig()->setConfigParam('sTheme', 'azure');

        $oCategoryTree = oxNew('oxwCategoryTree');
        $oCategoryTree->setViewParameters(array("sWidgetType" => "header"));
        $this->assertEquals('widget/header/categorylist.tpl', $oCategoryTree->render());
    }

    /**
     * Testing oxwCategoryTree::getDeepLevel()
     *
     * @return null
     */
    public function testGetDeepLevel()
    {
        $oCategoryTree = oxNew('oxwCategoryTree');
        $oCategoryTree->setViewParameters(array("deepLevel" => 2));
        $this->assertEquals(2, $oCategoryTree->getDeepLevel());
    }

    public function testChecksIfContentCategoryNotReturned()
    {
        $categoryTree = oxNew('OxidEsales\EshopCommunity\Application\Component\Widget\CategoryTree');

        $this->assertSame(false, $categoryTree->getContentCategory());
    }

    public function testChecksIfContentCategoryReturned()
    {
        $categoryTree = oxNew('OxidEsales\EshopCommunity\Application\Component\Widget\CategoryTree');
        $_GET['oxcid'] = 'test';

        $this->assertSame('test', $categoryTree->getContentCategory());
    }
}
