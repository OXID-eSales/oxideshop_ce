<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin')]
final class AssignProductsToCategoryCest
{
    public function testAssignProductsToCategory(AcceptanceTester $I): void
    {
        $I->wantToTest('assigning products to a category');

        $I->amGoingTo('open category products assignment popup');
        $adminPanel = $I->loginAdmin();
        $categoriesPage = $adminPanel->openCategories();
        $mainCategoryPage = $categoriesPage->selectProductCategory('Test category 1 [DE] šÄßüл');
        $assignProductsPopup = $mainCategoryPage->openAssignProductsPopup();

        $I->expect('products to be initially unassigned');
        $assignProductsPopup
            ->seeProductInUnassignedList('1000')
            ->seeProductInUnassignedList('10014');

        $I->amGoingTo('assign all products to category');
        $assignProductsPopup->assignAllProducts();

        $I->expect('all products to appear in assigned list');
        $assignProductsPopup
            ->seeProductInAssignedList('1000')
            ->seeProductInAssignedList('10014');

        $I->amGoingTo('unassign all products from category');
        $assignProductsPopup->unassignAllProducts();

        $I->expect('all products to be back in unassigned list');
        $assignProductsPopup
            ->seeProductInUnassignedList('1000')
            ->seeProductInUnassignedList('10014');

        $I->amGoingTo('assign single product to category');
        $assignProductsPopup->assignProductByArtNr('1000');

        $I->expect('only selected product to be assigned');
        $assignProductsPopup
            ->seeProductInAssignedList('1000')
            ->seeProductInUnassignedList('10014');
    }

    public function testAssignProductsWithVariantsToCategory(AcceptanceTester $I): void
    {
        $I->wantToTest('variant products visibility in category assignment');

        $I->amGoingTo('enable variants display in assignment lists');
        $adminPanel = $I->loginAdmin();
        $coreSettings = $adminPanel->openCoreSettings();
        $systemTab = $coreSettings->openSystemTab();
        $variantsTab = $systemTab->openVariants();
        $variantsTab->enableVariantsInAssignmentLists();

        $I->amGoingTo('check product variants visibility');
        $categoriesPage = $adminPanel->openCategories();
        $mainCategoryPage = $categoriesPage->selectProductCategory('Test category 1 [DE] šÄßüл');
        $assignProductsPopup = $mainCategoryPage->openAssignProductsPopup();

        $I->expect('to see parent product and all variants');
        $assignProductsPopup
            ->seeProductInUnassignedList('1002')
            ->seeProductInUnassignedList('1002-1')
            ->seeProductInUnassignedList('1002-2');

        $I->amGoingTo('assign all products with variants');
        $assignProductsPopup->assignAllProducts();

        $I->expect('parent and variants to be in assigned list');
        $assignProductsPopup
            ->seeProductInAssignedList('1002')
            ->seeProductInAssignedList('1002-1')
            ->seeProductInAssignedList('1002-2');

        $I->amGoingTo('disable variants display');
        $I->closeTab();
        $coreSettings = $adminPanel->openCoreSettings();
        $systemTab = $coreSettings->openSystemTab();
        $variantsTab = $systemTab->openVariants();
        $variantsTab->disableVariantsInAssignmentLists();

        $categoriesPage = $adminPanel->openCategories();
        $mainCategoryPage = $categoriesPage->selectProductCategory('Test category 1 [DE] šÄßüл');
        $assignProductsPopup = $mainCategoryPage->openAssignProductsPopup();

        $I->expect('to see only parent product without variants');
        $assignProductsPopup
            ->seeProductInAssignedList('1002')
            ->dontSeeProductInUnassignedList('1002-1')
            ->dontSeeProductInAssignedList('1002-2');
    }

    public function testSortCategoryProducts(AcceptanceTester $I): void
    {
        $I->wantToTest('category products sorting');

        $I->amGoingTo('open products sorting popup');
        $adminPanel = $I->loginAdmin();
        $categoriesPage = $adminPanel->openCategories();
        $mainCategoryPage = $categoriesPage->selectProductCategory('Test category 0 [DE] šÄßüл');
        $sortingCategoryPage = $mainCategoryPage->openSortingTab();
        $sortProductsPopup = $sortingCategoryPage->openSortingProductsPopup();

        $I->expect('products to be in default order');
        $sortProductsPopup
            ->seeProductInPosition('1000', 1)
            ->seeProductInPosition('1001', 2);

        $I->amGoingTo('create new product sorting');
        $sortProductsPopup
            ->assignProductByArtNr('1001')
            ->assignProductByArtNr('1000')
            ->saveSorting();

        $I->amGoingTo('apply column sorting');
        $sortProductsPopup->sortByColumn(3);

        $I->expect('products to be in new order');
        $sortProductsPopup
            ->seeProductInPosition('1001', 1)
            ->seeProductInPosition('1000', 2);

        $I->amGoingTo('reset product sorting');
        $sortProductsPopup->deleteSorting();

        $I->expect('products to return to default order');
        $sortProductsPopup
            ->seeProductInPosition('1000', 1)
            ->seeProductInPosition('1001', 2);
    }
}
