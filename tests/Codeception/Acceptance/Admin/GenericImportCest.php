<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin', 'genericImport')]
final class GenericImportCest
{
    public function testGenericImportWithHeaders(AcceptanceTester $I): void
    {
        $I->wantToTest('Generic Import for file with CSV header');
        $tableName = 'oxartextends';
        $adminPanel = $I->loginAdmin();
        $genericImport = $adminPanel->openGenericImport();

        $I->amGoingTo('attach and set parameters for CSV file on Step 1');
        $genericImport->setCsvSourceFile("genericImport/{$tableName}_with_header.csv")
            ->setTargetTable($tableName)
            ->setFirstCsvRowContainsHeaders()
            ->proceedToFieldMapping($tableName);

        $I->amGoingTo('check field mapping on Step 2');
        $genericImport->seeCsvColumnToFieldMapping(1, 'OXID')
            ->seeCsvColumnToFieldMapping(2, 'OXLONGDESC')
            ->seeCsvColumnToFieldMapping(3, 'OXLONGDESC_1')
            ->doImport();

        $I->amGoingTo('check if product detail page contains updated data');
        $products = $adminPanel->openProducts();
        $mainProductPage = $products->find($products->searchNumberInput, '1001');
        $I->seeInField($mainProductPage->longDescriptionInput, 'long desc DE with header');
        $mainProductPage->switchLanguage('English');
        $I->seeInField($mainProductPage->longDescriptionInput, 'long desc EN with header');
    }

    public function testGenericImportNoHeaders(AcceptanceTester $I): void
    {
        $I->wantToTest('Generic Import for file without CSV header');
        $tableName = 'oxartextends';
        $adminPanel = $I->loginAdmin();
        $genericImport = $adminPanel->openGenericImport();

        $I->amGoingTo('attach and set parameters for CSV file on Step 1');
        $genericImport->setCsvSourceFile("genericImport/{$tableName}_without_header.csv")
            ->setTargetTable($tableName);

        $I->expect('that I can use non-default field enclosure and terminator for CSV file');
        $genericImport->setCsvFieldEnclosure("'")
            ->setCsvFieldTerminator(',')
            ->proceedToFieldMapping($tableName);

        $I->amGoingTo('define field mapping on Step 2');
        $genericImport->setCsvColumnToFieldMapping(1, 'OXID')
            ->setCsvColumnToFieldMapping(2, 'OXLONGDESC')
            ->setCsvColumnToFieldMapping(3, 'OXLONGDESC_1')
            ->doImport();

        $I->amGoingTo('check if product detail page contains updated data');
        $products = $adminPanel->openProducts();
        $mainProductPage = $products->find($products->searchNumberInput, '1001');
        $I->seeInField($mainProductPage->longDescriptionInput, 'long desc DE no header');
        $mainProductPage->switchLanguage('English');
        $I->seeInField($mainProductPage->longDescriptionInput, 'long desc EN no header');
    }
}
