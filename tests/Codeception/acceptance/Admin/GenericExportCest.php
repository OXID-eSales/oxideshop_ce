<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\acceptanceAdmin;

use Codeception\Attribute\Group;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

#[Group('admin')]
final class GenericExportCest
{
    /**
     * @group genericExport
     */
    public function testGenericExport(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('Generic Export outputs product with rendered long description');
        $adminPanel = $I->loginAdmin();

        $I->amGoingTo('add some content with template tags to the long description');
        $contents = uniqid('some-long-description-', true);
        $descriptionWithTags = "$contents{{ 'now' | date('Y') }}";
        $renderedDescription = $contents . date('Y');
        $products = $adminPanel->openProducts();
        $mainProductPage = $products->find($products->searchNumberInput, value: '1000');
        $I->fillField($mainProductPage->longDescriptionInput, $descriptionWithTags);
        $mainProductPage->save();

        $I->amGoingTo('export category of the modified product and see that long description is rendered');
        $genericExport = $adminPanel->openGenericExport();
        $genericExport->selectExportCategory('Test category 0 [DE] šÄßüл')
            ->doExport()
            ->seeInExportResultsFile($renderedDescription);
    }
}
