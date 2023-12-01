<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Acceptance\Admin;

use Codeception\Attribute\Group;
use Codeception\Util\Fixtures;
use DateTime;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin')]
final class ProductListStatusTestCest
{
    private string $activeProductID = '1000';
    private string $temporaryActiveProductID = '1003';
    private string $temporaryInactiveProductID = '1004';
    private string $inactiveProductID = '1005';

    public function _before(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabase('blUseTimeCheck', true, 'bool');
    }

    public function _after(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabase('blUseTimeCheck', false, 'bool');
    }

    public function checkProductsStatus(AcceptanceTester $I): void
    {
        $admin = $I->loginAdmin();
        $productList = $admin->openProducts();

        $I->wantToTest('Product is active in list');

        $productList->filterByProductNumber($this->activeProductID);

        $I->assertStringContainsString(
            'active',
            $I->grabAttributeFrom($productList->productStatusClass, 'class')
        );


        $I->wantToTest('Product not active in the list');

        $productList->filterByProductNumber($this->inactiveProductID);

        $I->assertStringNotContainsString(
            'active',
            $I->grabAttributeFrom($productList->productStatusClass, 'class')
        );


        $I->wantToTest('Product temporary active in the list');

        $productList->filterByProductNumber($this->temporaryActiveProductID);

        $I->assertStringContainsString(
            'temp-active',
            $I->grabAttributeFrom($productList->productStatusClass, 'class')
        );


        $I->wantToTest('Product temporary not active in the list');

        $productList->filterByProductNumber($this->temporaryInactiveProductID);

        $I->assertStringContainsString(
            'temp-inactive',
            $I->grabAttributeFrom($productList->productStatusClass, 'class')
        );
    }
}
