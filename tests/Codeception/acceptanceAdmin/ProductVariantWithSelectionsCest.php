<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\CodeceptionAdmin;

use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

final class ProductVariantWithSelectionsCest
{
    /** @param AcceptanceAdminTester $I */
    public function selectionInheritanceByProductVariant(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('product variant inherits selections from its parent');

        $admin = $I->loginAdmin();
        $productList = $admin->openProducts();
        $productList->switchLanguage('Deutsch');

        $parentMain = $productList->filterByArtNum('1002');
        $parentSelection = $parentMain->openSelectionTab();
        $parentAssignSelections = $parentSelection->openAssignSelectionListPopup();
        $parentAssignSelections->assignSelectionByTitle('test selection list [DE] šÄßüл');
        $I->closeTab();
        $parentVariant = $parentMain->openVariantsTab();

        $variantMain = $parentVariant->openEditProductVariant(1);
        $variantMain->seeInArtNum('1002-1');
        $variantSelection = $variantMain->openSelectionTab();
        $variantAssignSelections = $variantSelection->openAssignSelectionListPopup();
        $variantAssignSelections->seeInAssignedList('test selection list [DE] šÄßüл');
        $I->closeTab();
    }
}
