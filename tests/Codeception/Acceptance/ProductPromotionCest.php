<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance;

use OxidEsales\Codeception\Page\Home;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

final class ProductPromotionCest
{
    public function _after(AcceptanceTester $I): void
    {
        $I->deleteFromDatabase(
            'oxactions2article',
            [
                'OXID' => '97483faa8165859ed57e26f75fb6449d',
            ]
        );
        $I->deleteFromDatabase(
            'oxactions2article',
            [
                'OXID' => '66683faa8165859ed57e26f75fb6449d',
            ]
        );
        $I->deleteFromDatabase(
            'oxactions2article',
            [
                'OXID' => '87483faa8165859ed57e26f75fb6449d',
            ]
        );
        $I->clearShopCache();
    }

    /**
     * @group testFrontendPromotion
     * @group testFrontendPromotionNew
     */
    public function testFrontendPromotionNew(AcceptanceTester $I): void
    {
        $I->wantToTest('the newest products');
        $I->updateConfigInDatabase('sShowNewestArticles', true, 'bool');
        $I->clearShopCache();
        $I->haveInDatabase(
            'oxactions2article',
            [
                'OXID' => '97483faa8165859ed57e26f75fb6449d',
                'OXSHOPID' => 1,
                'OXACTIONID' => 'oxnewest',
                'OXARTID' => '1001',
                'OXSORT' => 2,
                'OXTIMESTAMP' => '2023-01-12 11:45:28'
            ]
        );

        $I->haveInDatabase(
            'oxactions2article',
            [
                'OXID' => '66683faa8165859ed57e26f75fb6449d',
                'OXSHOPID' => 1,
                'OXACTIONID' => 'oxnewest',
                'OXARTID' => '1000',
                'OXSORT' => 10,
                'OXTIMESTAMP' => '2023-01-12 11:45:28'
            ]
        );

        $homePage = $I->openShop();
        $productsWidget = $homePage->getNewestArticles();
        $productsWidget
            ->getProduct(1)
            ->productHasTitle('Test product 1 [EN] šÄßüл')
            ->addProductToCart();

        $productsWidget
            ->getProduct(2)
            ->productHasTitle('Test product 0 [EN] šÄßüл')
            ->setProductAmount(3)
            ->addProductToCart();

        $basketItemToCheck1 = [
            'title' => 'Test product 1 [EN] šÄßüл',
            'price' => '100,00 €',
            'amount' => 1
        ];

        $basketItemToCheck2 = [
            'title' => 'Test product 0 [EN] šÄßüл',
            'price' => '50,00 €',
            'amount' => 3
        ];

        $this->checkMiniBasket(
            $I,
            $homePage,
            [
                $basketItemToCheck1,
                $basketItemToCheck2
            ],
            '250,00 €',
            '4'
        );
    }

    /**
     * @group testFrontendPromotion
     * @group testFrontendPromotionTop
     */
    public function testFrontendPromotionTop(AcceptanceTester $I): void
    {
        $I->wantToTest('the Top5 products');
        $I->updateConfigInDatabase('sShowTopArticles', true, 'bool');
        $I->clearShopCache();
        $I->haveInDatabase(
            'oxactions2article',
            [
                'OXID' => '87483faa8165859ed57e26f75fb6449d',
                'OXSHOPID' => 1,
                'OXACTIONID' => 'oxtop5',
                'OXARTID' => '1002',
                'OXSORT' => 1,
                'OXTIMESTAMP' => '2023-01-12 11:45:28'
            ]
        );
        $I->clearShopCache();
        $homePage = $I->openShop();
        $homePage->getPromotionTop5()
            ->getProduct(1)
            ->productHasTitle('Test product 2 [EN] šÄßüл')
            ->openProductDetails();

        $productToCheck = [
            'title' => 'Test product 2 [EN] šÄßüл',
            'price' => '55,00 €',
            'amount' => 2
        ];
        $this->checkDetails($I, $productToCheck);
    }

    /**
     * @group testFrontendPromotion
     * @group testFrontendPromotionBargainItems
     */
    public function testFrontendPromotionBargainItems(AcceptanceTester $I): void
    {
        $I->wantToTest('the bargain items');
        $I->updateConfigInDatabase('sShowBargainArticles', true, 'bool');
        $I->clearShopCache();
        $I->haveInDatabase(
            'oxactions2article',
            [
                'OXID' => '97483faa8165859ed57e26f75fb6449d',
                'OXSHOPID' => 1,
                'OXACTIONID' => 'oxbargain',
                'OXARTID' => '1001',
                'OXSORT' => 1,
                'OXTIMESTAMP' => '2023-01-12 11:45:28'
            ]
        );

        $homePage = $I->openShop();

        $productToCheck = [
            'title' => 'Test product 1 [EN] šÄßüл',
            'price' => '100,00 €',
            'amount' => 1
        ];

        $productsWidget = $homePage->getBargainArticleList();
        $productsWidget->getProduct(1)
            ->productHasTitle($productToCheck['title'])
            ->addProductToCart();

        $this->checkMiniBasket(
            $I,
            $homePage,
            [
                $productToCheck
            ],
            '100,00 €',
            '1'
        );
    }

    private function checkMiniBasket(
        AcceptanceTester $I,
        Home $homepage,
        array $productsToCheck,
        string $price,
        string $amount
    ): void {
        $I->wantToTest('if the basket contains correct products and price');
        $homepage->seeMiniBasketContains($productsToCheck, $price, $amount);
        $homepage->closeMiniBasket();
    }

    private function checkDetails(AcceptanceTester $I, array $productToCheck): void
    {
        $I->wantToTest('if the correct product details page is opened');
        $I->see($productToCheck['title']);
        $I->see($productToCheck['price']);
    }
}
