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
final class ProductVariantWithSelectionsCest
{
    public function selectionInheritanceByProductVariant(AcceptanceTester $I): void
    {
        $I->wantToTest('product variant inherits selections from its parent');

        $admin = $I->loginAdmin();
        $products = $admin->openProducts();
        $productsMainPage = $products->switchLanguage('Deutsch');

        $parentMainPage = $productsMainPage->findByProductNumber('1002');
        $parentSelectionPage = $parentMainPage->openSelectionTab();
        $parentSelectionPage
            ->openAssignSelectionListPopup()
            ->assignSelectionByTitle('test selection list [DE] šÄßüл');
        $I->closeTab();

        $parentVariantPage = $parentSelectionPage->openVariantsTab();
        $variantMainPage = $parentVariantPage->openEditProductVariant(1);
        $I->seeInField($variantMainPage->numberInput, '1002-1');

        $variantMainPage
            ->openSelectionTab()
            ->openAssignSelectionListPopup()
            ->seeProductAssigned('test selection list [DE] šÄßüл');
    }
}
