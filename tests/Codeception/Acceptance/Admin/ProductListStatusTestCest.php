<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Acceptance\Admin;

use Codeception\Attribute\Group;
use DateTime;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin', 'product')]
final class ProductListStatusTestCest
{
    private string $productID = '1000';

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

        $I->updateInDatabase(
            'oxarticles',
            [
                'OXACTIVE' => false,
                'OXACTIVEFROM' => (new DateTime())->modify('-1 day')->format('Y-m-d 00:00:00'),
                'OXACTIVETO' => (new DateTime())->modify('+1 day')->format('Y-m-d 00:00:00')
            ],
            [
                'OXID' => $this->productID
            ]
        );

        $productList->filterByProductNumber($this->productID);

        $I->assertStringContainsString(
            'temp-active',
            $I->grabAttributeFrom($productList->productStatusClass, 'class')
        );

        $I->expect('the given product is not active in the list');

        $I->updateInDatabase(
            'oxarticles',
            [
                'OXACTIVE' => false,
                'OXACTIVEFROM' => (new DateTime())->modify('+1 day')->format('Y-m-d 00:00:00'),
                'OXACTIVETO' => (new DateTime())->modify('+2 day')->format('Y-m-d 00:00:00')
            ],
            [
                'OXID' => $this->productID
            ]
        );

        $productList->filterByProductNumber($this->productID);

        $I->assertStringContainsString(
            'temp-inactive',
            $I->grabAttributeFrom($productList->productStatusClass, 'class')
        );
    }
}
