<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin', 'product')]
final class ProductListStatusTestCest
{
    private string $temporaryActiveProductID = '1003';
    private string $temporaryInactiveProductID = '1004';

    public function _before(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabase('blUseTimeCheck', true, 'bool');
    }

    public function checkProductsStatuses(AcceptanceTester $I): void
    {
        $I->wantToTest('Product statuses by time range');

        $admin = $I->loginAdmin();
        $productList = $admin->openProducts();

        $I->expect('the given product is active in the list');

        $productList->filterByProductNumber($this->temporaryActiveProductID);

        $I->assertStringContainsString(
            'temp-active',
            $I->grabAttributeFrom($productList->productStatusClass, 'class')
        );

        $I->expect('the given product is not active in the list');

        $productList->filterByProductNumber($this->temporaryInactiveProductID);

        $I->assertStringContainsString(
            'temp-inactive',
            $I->grabAttributeFrom($productList->productStatusClass, 'class')
        );
    }
}
