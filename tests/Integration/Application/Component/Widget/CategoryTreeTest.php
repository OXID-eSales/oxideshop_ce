<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Component\Widget;

use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * Tests for OxidEsales\EshopCommunity\Application\Component\Widget\CategoryTree class
 */
class CategoryTreeTest extends UnitTestCase
{
    /**
     * Testing OxidEsales\EshopCommunity\Application\Component\Widget\CategoryTree::render()
     *
     * @return null
     */
    public function testRender()
    {
        $categoryTree = oxNew('OxidEsales\EshopCommunity\Application\Component\Widget\CategoryTree');
        $this->assertEquals('widget/sidebar/categorytree.tpl', $categoryTree->render());
    }

    /**
     * Testing OxidEsales\EshopCommunity\Application\Component\Widget\CategoryTree::render()
     *
     * @return null
     */
    public function testRenderDifferentTemplate()
    {
        $this->setConfigParam('sTheme', 'azure');
        \OxidEsales\EshopCommunity\Internal\Container\ContainerFactory::getInstance()->resetContainer();

        $categoryTree = oxNew('OxidEsales\EshopCommunity\Application\Component\Widget\CategoryTree');
        $categoryTree->setViewParameters(array("sWidgetType" => "header"));
        $this->assertEquals('widget/header/categorylist.tpl', $categoryTree->render());
    }
}
