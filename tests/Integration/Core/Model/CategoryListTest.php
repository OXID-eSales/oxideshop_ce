<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Model;

use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\CategoryList;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class CategoryListTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->prepareTestData();
    }

    public function testUpdateNodesWithSimpleTree(): void
    {
        $categoryList = new CategoryList();

        $categoryList->updateCategoryTree(false);

        $rootCategory = new Category();
        $rootCategory->load('test_root');
        $this->assertEquals(1, $rootCategory->oxcategories__oxleft->value);
        $this->assertEquals(10, $rootCategory->oxcategories__oxright->value);
        $this->assertEquals('test_root', $rootCategory->oxcategories__oxrootid->value);

        $childCategory = new Category();
        $childCategory->load('test_child1');
        $this->assertEquals(2, $childCategory->oxcategories__oxleft->value);
        $this->assertEquals(7, $childCategory->oxcategories__oxright->value);
        $this->assertEquals('test_root', $childCategory->oxcategories__oxrootid->value);

        $childCategory = new Category();
        $childCategory->load('test_child2');
        $this->assertEquals(8, $childCategory->oxcategories__oxleft->value);
        $this->assertEquals(9, $childCategory->oxcategories__oxright->value);
        $this->assertEquals('test_root', $childCategory->oxcategories__oxrootid->value);

        $childCategory = new Category();
        $childCategory->load('test_child1_1');
        $this->assertEquals(3, $childCategory->oxcategories__oxleft->value);
        $this->assertEquals(4, $childCategory->oxcategories__oxright->value);

        $childCategory = new Category();
        $childCategory->load('test_child1_2');
        $this->assertEquals(5, $childCategory->oxcategories__oxleft->value);
        $this->assertEquals(6, $childCategory->oxcategories__oxright->value);
    }

    private function prepareTestData(): void
    {
        $categories = [
            [
                'oxid' => 'test_root',
                'oxparentid' => 'oxrootid',
                'oxtitle' => 'Root Category',
                'oxsort' => 1,
                'oxleft' => 0,
                'oxright' => 0,
                'oxrootid' => 'test_root'
            ],
            [
                'oxid' => 'test_child1',
                'oxparentid' => 'test_root',
                'oxtitle' => 'Child 1',
                'oxsort' => 1,
                'oxleft' => 0,
                'oxright' => 0,
                'oxrootid' => ''
            ],
            [
                'oxid' => 'test_child2',
                'oxparentid' => 'test_root',
                'oxtitle' => 'Child 2',
                'oxsort' => 2,
                'oxleft' => 0,
                'oxright' => 0,
                'oxrootid' => ''
            ],
            [
                'oxid' => 'test_child1_1',
                'oxparentid' => 'test_child1',
                'oxtitle' => 'Child 1.1',
                'oxsort' => 1,
                'oxleft' => 0,
                'oxright' => 0,
                'oxrootid' => ''
            ],
            [
                'oxid' => 'test_child1_2',
                'oxparentid' => 'test_child1',
                'oxtitle' => 'Child 1.2',
                'oxsort' => 2,
                'oxleft' => 0,
                'oxright' => 0,
                'oxrootid' => ''
            ]
        ];

        foreach ($categories as $categoryData) {
            $category = new Category();
            $category->setId($categoryData['oxid']);
            $category->oxcategories__oxparentid = new Field($categoryData['oxparentid']);
            $category->oxcategories__oxtitle = new Field($categoryData['oxtitle']);
            $category->oxcategories__oxsort = new Field($categoryData['oxsort']);
            $category->oxcategories__oxleft = new Field($categoryData['oxleft']);
            $category->oxcategories__oxright = new Field($categoryData['oxright']);
            $category->oxcategories__oxrootid = new Field($categoryData['oxrootid']);
            $category->save();
        }
    }
}