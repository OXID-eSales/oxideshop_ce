<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Acceptance;

use Codeception\Attribute\Group;
use Codeception\Util\Fixtures;
use DateTime;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('product')]
final class ProductStatusTestCest
{
    private string $productID = '1000';
    private array $productData;

    public function _before(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabase('blUseTimeCheck', true, 'bool');
        $product = $this->getProductData($this->productID);
        $this->productData = [
            'title' => $product['OXTITLE_1'],
            'description' => $product['OXSHORTDESC_1'],
            'price' => $product['OXPRICE']
        ];
    }

    public function checkProductsTemporaryActiveStatus(AcceptanceTester $I): void
    {
        $I->wantToTest('Product active in list');

        $homePage = $I->openShop();

        $I->amGoingTo('Test product temporary active in the list with empty activeto');

        $I->updateInDatabase(
            'oxarticles',
            [
                'OXACTIVE' => false,
                'OXACTIVEFROM' => (new DateTime())->modify('-1 day')->format('Y-m-d 00:00:00'),
                'OXACTIVETO' => '0000-00-00 00:00:00'
            ],
            [
                'OXID' => $this->productID
            ]
        );

        $I->expect('Product is shown');
        $productList = $homePage->searchFor($this->productID);
        $productList->seeProductData($this->productData);


        $I->amGoingTo('Test product temporary active in the list with empty activefrom');

        $I->updateInDatabase(
            'oxarticles',
            [
                'OXACTIVE' => false,
                'OXACTIVEFROM' => '0000-00-00 00:00:00',
                'OXACTIVETO' => (new DateTime())->modify('+1 day')->format('Y-m-d 00:00:00')
            ],
            [
                'OXID' => $this->productID
            ]
        );

        $I->expect('Product is shown');
        $productList = $homePage->searchFor($this->productID);
        $productList->seeProductData($this->productData);
    }

    public function checkProductsTemporaryNotActiveStatus(AcceptanceTester $I): void
    {
        $I->wantToTest('Product not active in list');

        $homePage = $I->openShop();

        $I->amGoingTo('Test product temporary inactive in the list with empty activeto');

        $I->updateInDatabase(
            'oxarticles',
            [
                'OXACTIVE' => false,
                'OXACTIVEFROM' => (new DateTime())->modify('+1 day')->format('Y-m-d 00:00:00'),
                'OXACTIVETO' => '0000-00-00 00:00:00'
            ],
            [
                'OXID' => $this->productID
            ]
        );

        $I->expect('Product is not shown in case 1');
        $productList = $homePage->searchFor($this->productID);
        $productList->dontSeeProductData($this->productData);


        $I->amGoingTo('Test product temporary inactive in the list with empty activefrom');

        $I->updateInDatabase(
            'oxarticles',
            [
                'OXACTIVE' => false,
                'OXACTIVEFROM' => '0000-00-00 00:00:00',
                'OXACTIVETO' => (new DateTime())->modify('-1 day')->format('Y-m-d 00:00:00')
            ],
            [
                'OXID' => $this->productID
            ]
        );

        $I->expect('Product is not shown in case 2');
        $productList = $homePage->searchFor($this->productID);
        $productList->dontSeeProductData($this->productData);
    }

    private function getProductData(string $productID): array
    {
        return Fixtures::get(sprintf('product-%s', $productID));
    }
}
