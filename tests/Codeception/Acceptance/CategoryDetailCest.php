<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance;

use Codeception\Attribute\Group;
use Codeception\Util\Fixtures;
use OxidEsales\Codeception\Step\CategoryNavigation;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;


#[Group('category_description')]
final class CategoryDetailCest
{
    public function categoryDetailPageInformation(AcceptanceTester $I): void
    {
        $I->wantToTest('category information in detail page');

        $categoryData = Fixtures::get('testCategory0');

        $categoryNavigation = new CategoryNavigation($I);
        $categoryDetailPage = $categoryNavigation->openCategoryDetailsPage($categoryData['id']);
        $categoryDetailPage->seeCategoryData($categoryData);
    }

    public function subcategoryDetailPageInformation(AcceptanceTester $I): void
    {
        $I->wantToTest('subcategory information in detail page');

        $categoryData = Fixtures::get('testCategory0');
        $subCategoryData = Fixtures::get('testCategory1');

        $categoryNavigation = new CategoryNavigation($I);
        $categoryDetailPage = $categoryNavigation->openCategoryDetailsPage($categoryData['id']);
        $categoryDetailPage->seeSubCategoryData($subCategoryData);
    }
}
