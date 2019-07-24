<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * Tests for oxwCategoryTree class
 */
class CategoryTreeTest extends UnitTestCase
    {
    /**
     * Testing OxidEsales\EshopCommunity\Application\Component\Widget\CategoryTree::getDeepLevel()
     *
     * @return null
     */
    public function testGetDeepLevel()
    {
        $categoryTree = oxNew('OxidEsales\EshopCommunity\Application\Component\Widget\CategoryTree');
        $categoryTree->setViewParameters(array("deepLevel" => 2));
        $this->assertEquals(2, $categoryTree->getDeepLevel());
    }

    public function testChecksIfContentCategoryNotReturned()
    {
        $categoryTree = oxNew('OxidEsales\EshopCommunity\Application\Component\Widget\CategoryTree');

        $this->assertSame(false, $categoryTree->getContentCategory());
    }

    public function testChecksIfContentCategoryReturned()
    {
        $categoryTree = oxNew('OxidEsales\EshopCommunity\Application\Component\Widget\CategoryTree');
        $this->setRequestParameter('oxcid', 'test');

        $this->assertSame('test', $categoryTree->getContentCategory());
    }
}
