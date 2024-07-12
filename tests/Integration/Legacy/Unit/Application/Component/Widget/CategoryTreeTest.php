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
class CategoryTreeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Testing OxidEsales\EshopCommunity\Application\Component\Widget\CategoryTree::getDeepLevel()
     */
    public function testGetDeepLevel()
    {
        $categoryTree = oxNew(\OxidEsales\EshopCommunity\Application\Component\Widget\CategoryTree::class);
        $categoryTree->setViewParameters(["deepLevel" => 2]);
        $this->assertSame(2, $categoryTree->getDeepLevel());
    }

    public function testChecksIfContentCategoryNotReturned()
    {
        $categoryTree = oxNew(\OxidEsales\EshopCommunity\Application\Component\Widget\CategoryTree::class);

        $this->assertFalse($categoryTree->getContentCategory());
    }

    public function testChecksIfContentCategoryReturned()
    {
        $categoryTree = oxNew(\OxidEsales\EshopCommunity\Application\Component\Widget\CategoryTree::class);
        $this->setRequestParameter('oxcid', 'test');

        $this->assertSame('test', $categoryTree->getContentCategory());
    }
}
