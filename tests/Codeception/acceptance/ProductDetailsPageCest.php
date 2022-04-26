<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception;

use Codeception\Util\Fixtures;
use OxidEsales\Codeception\Step\ProductNavigation;
use OxidEsales\Codeception\Module\Translation\Translator;

final class ProductDetailsPageCest
{
    /**
     * @group main
     * @group product
     * @group productVariants
     *
     * @param AcceptanceTester $I
     */
    public function selectMultidimensionalVariantsInDetailsPage(AcceptanceTester $I): void
    {
        $productNavigation = new ProductNavigation($I);
        $I->wantToTest('multidimensional variants functionality in details page');

        $I->updateConfigInDatabase('blUseMultidimensionVariants', true, 'bool');
        $I->updateConfigInDatabase('bl_showPriceAlarm', true, 'bool');

        $data = [
            'OXID' => '1001411',
            'OXLONGDESC' => 'Test description',
            'OXLONGDESC_1' => 'Test description',
            'OXLONGDESC_2' => '',
            'OXLONGDESC_3' => '',
        ];
        $I->haveInDatabase('oxartextends', $data);

        $data = [
            'OXID' => 'testattributes1',
            'OXOBJECTID' => '1001411',
            'OXATTRID' => 'testattribute1',
            'OXVALUE' => 'attr value 1 [DE]',
            'OXVALUE_1' => 'attr value 1 [EN]',
        ];
        $I->haveInDatabase('oxobject2attribute', $data);

        $productData = [
            'id' => '10014',
            'title' => '14 EN product šÄßüл',
            'description' => '13 EN description šÄßüл',
            'price' => 'from 15,00 € *'
        ];

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);

        //assert product
        $detailsPage->seeProductData($productData)
            ->checkIfProductIsNotBuyable();

        //select a variant of the product
        $detailsPage = $detailsPage->selectVariant(1, 'S')
            ->checkIfProductIsNotBuyable()
            ->selectVariant(2, 'white');

        //assert product
        $productData3 = [
            'id' => '10014-1-3',
            'title' => '14 EN product šÄßüл S | white',
            'description' => '',
            'price' => '15,00 € *'
        ];
        $detailsPage->seeProductData($productData3)
            ->checkIfProductIsBuyable();

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);

        //assert product
        $detailsPage->seeProductData($productData);

        //select a variant of the product
        $detailsPage->selectVariant(1, 'S')
            ->checkIfProductIsNotBuyable()
            ->selectVariant(2, 'black')
            ->checkIfProductIsNotBuyable()
            ->selectVariant(3, 'lether');

        //assert product
        $productData2 = [
            'id' => '10014-1-1',
            'title' => '14 EN product šÄßüл S | black | lether',
            'description' => '',
            'price' => '25,00 € *'
        ];
        $detailsPage->seeProductData($productData2)
            ->checkIfProductIsBuyable();

        $detailsPage = $detailsPage->openPriceAlert()
            ->openAttributes();

        $I->see('attr value 1 [EN]');

        $detailsPage = $detailsPage->openDescription();

        $I->see('Test description');

        $detailsPage = $detailsPage->addProductToBasket(2);

        //assert product in basket
        $basketItem = [
            'title' => '14 EN product šÄßüл, S | black | lether',
            'price' => '50,00 €',
            'amount' => 2
        ];
        $detailsPage->seeMiniBasketContains([$basketItem], '50,00 €', '2');
    }

    /**
     * @group product
     * @group search
     *
     * @param AcceptanceTester $I
     */
    public function navigateInDetailsPage(AcceptanceTester $I): void
    {
        $I->wantToTest('product navigation in details page');

        $productData = [
            'id' => '1001',
            'title' => 'Test product 1 [EN] šÄßüл',
            'description' => 'Test product 1 short desc [EN] šÄßüл',
            'price' => '100,00 € *'
        ];

        $I->updateConfigInDatabase('aSortCols', 'a:2:{i:0;s:7:"oxtitle";i:1;s:13:"oxvarminprice";}', 'arr');

        $searchListPage = $I->openShop()
            ->searchFor('100')
            ->selectSorting('oxtitle', 'asc')
            ->seeProductData($productData, 3);
        $detailsPage = $searchListPage->openProductDetailsPage(2);
        $breadCrumb = sprintf(Translator::translate('SEARCH_RESULT'), '100');
        $detailsPage->seeOnBreadCrumb($breadCrumb);
        $navigationText = Translator::translate('PRODUCT') . ' 2 ' . Translator::translate('OF') . ' 4';
        $I->see($navigationText);
        $detailsPage = $detailsPage->openNextProduct();
        $navigationText = Translator::translate('PRODUCT') . ' 3 ' . Translator::translate('OF') . ' 4';
        $I->see($navigationText);
        $detailsPage = $detailsPage->openPreviousProduct();
        $navigationText = Translator::translate('PRODUCT') . ' 2 ' . Translator::translate('OF') . ' 4';
        $I->see($navigationText);
        $detailsPage->openProductSearchList()
            ->seeProductData($productData, 3);
        $breadCrumb = Translator::translate('SEARCH');
        $detailsPage->seeOnBreadCrumb($breadCrumb);
    }

    /**
     * @group product
     *
     * @param AcceptanceTester $I
     */
    public function detailsPageInformation(AcceptanceTester $I): void
    {
        $I->wantToTest('product information in details page');

        $productNavigation = new ProductNavigation($I);
        $productData = [
            'id' => '1001',
            'title' => 'Test product 1 [EN] šÄßüл',
            'description' => 'Test product 1 short desc [EN] šÄßüл',
            'price' => '100,00 € *'
        ];

        $data = [
            'OXID' => 'testattributes1',
            'OXOBJECTID' => '1001',
            'OXSELNID' => 'testsellist',
            'OXSORT' => 0,
        ];
        $I->haveInDatabase('oxobject2selectlist', $data);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id'])
            ->seeProductData($productData)
            ->seeProductOldPrice('150,00 €');
        $I->see(Translator::translate('MESSAGE_NOT_ON_STOCK'));
        $I->see(Translator::translate('AVAILABLE_ON') . ' 2030-01-01');
        $detailsPage = $detailsPage->selectSelectionListItem('selvar1 [EN] šÄßüл')
            ->selectSelectionListItem('selvar2 [EN] šÄßüл')
            ->openDescription();
        $I->see('Test product 1 long description [EN] šÄßüл');
        $detailsPage->openAttributes()
            ->seeAttributeName('Test attribute 1 [EN] šÄßüл', 1)
            ->seeAttributeValue('attr value 11 [EN] šÄßüл', 1)
            ->seeAttributeName('Test attribute 3 [EN] šÄßüл', 2)
            ->seeAttributeValue('attr value 3 [EN] šÄßüл', 2)
            ->seeAttributeName('Test attribute 2 [EN] šÄßüл', 3)
            ->seeAttributeValue('attr value 12 [EN] šÄßüл', 3);
    }

    /**
     * @group product
     * @group productSuggestion
     *
     * @param AcceptanceTester $I
     */
    public function sendProductSuggestionEmail(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabase('blAllowSuggestArticle', '1');
        $productNavigation = new ProductNavigation($I);
        $I->wantTo('send the product suggestion email');

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];
        $emptyEmailData = [
            'recipient_name' => '',
            'recipient_email' => '',
            'sender_name' => '',
            'sender_email' => '',
        ];
        $suggestionEmailData = [
            'recipient_name' => 'Test User',
            'recipient_email' => 'example@oxid-esales.dev',
            'sender_name' => 'user',
            'sender_email' => 'example_test@oxid-esales.dev',
        ];

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        $suggestionPage = $detailsPage->openProductSuggestionPage()
            ->sendSuggestionEmail($emptyEmailData);
        $I->see(Translator::translate('ERROR_MESSAGE_INPUT_NOTALLFIELDS'));
        $suggestionPage->sendSuggestionEmail($suggestionEmailData);
        $I->see($productData['title']);
    }

    /**
     * @group product
     * @group priceAlarm
     *
     * @param AcceptanceTester $I
     */
    public function sendProductPriceAlert(AcceptanceTester $I): void
    {
        $productNavigation = new ProductNavigation($I);
        $I->wantToTest('product price alert functionality');

        $I->updateConfigInDatabase('bl_showPriceAlarm', true, 'bool');

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);
        $I->see(Translator::translate('PRICE_ALERT'));

        $detailsPage->sendPriceAlert('example_test@oxid-esales.dev', 99.99);
        $thankYouMessage = Translator::translate('PAGE_DETAILS_THANKYOUMESSAGE3')
            . ' 99,99 € ' . Translator::translate('PAGE_DETAILS_THANKYOUMESSAGE4');
        $I->see($thankYouMessage);
        $I->see($productData['title']);
    }

    /**
     * @group product
     * @group priceAlarm
     *
     * @param AcceptanceTester $I
     */
    public function disableProductPriceAlert(AcceptanceTester $I): void
    {
        $productNavigation = new ProductNavigation($I);
        $I->wantToTest('product price alert functionality is disabled');

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        //disabling price alert for product(1000)
        $I->updateInDatabase('oxarticles', ['oxblfixedprice' => 1], ['OXID' => '1000']);

        //open details page
        $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);
        $I->dontSee(Translator::translate('PRICE_ALERT'));
    }

    /**
     * @group product
     * @group productVariants
     *
     * @param AcceptanceTester $I
     */
    public function selectProductVariant(AcceptanceTester $I): void
    {
        $productNavigation = new ProductNavigation($I);
        $I->wantToTest('product simple variant selection and order in details page');

        $productData = [
            'id' => '1002',
            'title' => 'Test product 2 [EN] šÄßüл',
            'description' => 'Test product 2 short desc [EN] šÄßüл',
            'price' => 'from 55,00 € *'
        ];

        $variantData1 = [
            'id' => '1002-1',
            'title' => 'Test product 2 [EN] šÄßüл var1 [EN] šÄßüл',
            'variantName' => 'var1 [EN] šÄßüл',
            'description' => '',
            'price' => '55,00 € *'
        ];

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);
        $detailsPage->seeProductData($productData);

        // select variant
        $detailsPage = $detailsPage->selectVariant(1, $variantData1['variantName'])
            ->seeProductData($variantData1);

        $basketItemToCheck1 = [
            'title' => 'Test product 2 [EN] šÄßüл, var1 [EN] šÄßüл',
            'price' => '165,00 €',
            'amount' => 3
        ];

        $detailsPage = $detailsPage->addProductToBasket(3)
            ->seeMiniBasketContains([$basketItemToCheck1], '165,00 €', '3');

        // select second variant
        $variantData2 = [
            'id' => '1002-2',
            'title' => 'Test product 2 [EN] šÄßüл var2 [EN] šÄßüл',
            'variantName' => 'var2 [EN] šÄßüл',
            'description' => '',
            'price' => '67,00 € *'
        ];

        $detailsPage = $detailsPage->selectVariant(1, $variantData2['variantName'])
            ->seeProductData($variantData2);

        $basketItemToCheck2 = [
            'title' => 'Test product 2 [EN] šÄßüл, var2 [EN] šÄßüл',
            'price' => '201,00 €',
            'amount' => 3
        ];
        $detailsPage->addProductToBasket(3)
            ->seeMiniBasketContains([$basketItemToCheck1, $basketItemToCheck2], '366,00 €', '6');
    }

    public function selectProductVariantsWithSelectionLists(AcceptanceTester $I): void
    {
        $I->wantToTest('add to cart with product variant/selection list combinations');

        $productId = '1002';
        $productName = 'Test product 2 [EN] šÄßüл';
        $variant1Name = 'var1 [EN] šÄßüл';
        $variant2Name = 'var2 [EN] šÄßüл';
        $selectionListsTitle = 'test selection list [EN] šÄßüл';
        $selectionList2Value = 'selvar2 [EN] šÄßüл';
        $selectionList3Value = 'selvar3 [EN] šÄßüл';

        $data = [
            'OXID' => 'testattributes1',
            'OXOBJECTID' => $productId,
            'OXSELNID' => 'testsellist',
            'OXSORT' => 0,
        ];
        $I->haveInDatabase('oxobject2selectlist', $data);

        $productNavigation = new ProductNavigation($I);
        $detailsPage = $productNavigation->openProductDetailsPage($productId);

        $detailsPage = $detailsPage->selectVariant(1, $variant1Name);
        $detailsPage->selectSelectionListItem($selectionList2Value);
        $detailsPage->addProductToBasket(1);

        $detailsPage = $detailsPage->selectVariant(1, $variant2Name);
        $detailsPage->selectSelectionListItem($selectionList3Value);
        $detailsPage->addProductToBasket(2);

        $detailsPage->openBasket();

        $I->see("$productName, $variant1Name");
        $I->see("$selectionListsTitle: $selectionList2Value");

        $I->see("$productName, $variant2Name");
        $I->see("$selectionListsTitle: $selectionList3Value");
    }

    /**
     * @group product
     * @group accessories
     *
     * @param AcceptanceTester $I
     */
    public function checkProductAccessories(AcceptanceTester $I): void
    {
        $productNavigation = new ProductNavigation($I);
        $I->wantToTest('Product\'s accessories');

        $I->updateConfigInDatabase('bl_perfLoadAccessoires', true, 'bool');
        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        $accessoryData = [
            'id' => '1002',
            'title' => 'Test product 2 [EN] šÄßüл',
            'description' => 'Test product 2 short desc [EN] šÄßüл',
            'price' => 'from 55,00 €'
        ];

        $this->prepareAccessoriesDataForProduct($I, $productData['id'], $accessoryData['id']);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);
        $I->see(Translator::translate('ACCESSORIES'));
        $detailsPage->seeAccessoryData($accessoryData, 1)
            ->openAccessoryDetailsPage(1)
            ->seeProductData($accessoryData);
    }

    /**
     * @group product
     * @group similarProducts
     *
     * @param AcceptanceTester $I
     */
    public function checkSimilarProducts(AcceptanceTester $I): void
    {
        $productNavigation = new ProductNavigation($I);
        $I->wantToTest('similar products on details page');

        $I->updateConfigInDatabase('bl_perfLoadSimilar', true, 'bool');
        $I->updateConfigInDatabase('iNrofSimilarArticles', '5', 'str');

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        $similarProductData = [
            'id' => '1001',
            'title' => 'Test product 1 [EN] šÄßüл',
            'description' => 'Test product 1 short desc [EN] šÄßüл',
            'price' => '100,00 € *'
        ];

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        $I->see(Translator::translate('SIMILAR_PRODUCTS'));
        $detailsPage->seeSimilarProductData($similarProductData, 1)
            ->openSimilarProductDetailsPage(1)
            ->seeProductData($similarProductData)
            ->seeSimilarProductData($productData, 1);
    }

    /**
     * @group product
     * @group crossSelling
     *
     * @param AcceptanceTester $I
     */
    public function checkProductCrossSelling(AcceptanceTester $I): void
    {
        $productNavigation = new ProductNavigation($I);
        $I->wantToTest('Product\'s crossselling on details page');

        $I->updateConfigInDatabase('bl_perfLoadCrossselling', true, 'bool');
        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        $crossSellingProductData = [
            'id' => '1002',
            'title' => 'Test product 2 [EN] šÄßüл',
            'description' => 'Test product 2 short desc [EN] šÄßüл',
            'price' => 'from 55,00 €'
        ];

        $this->prepareCrossSellingDataForProduct($I, $productData['id'], $crossSellingProductData['id']);

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        $I->see(Translator::translate('HAVE_YOU_SEEN'));
        $detailsPage->seeCrossSellingData($crossSellingProductData, 1)
            ->openCrossSellingDetailsPage(1)
            ->seeProductData($crossSellingProductData);
    }

    /**
     * @group product
     * @group productPrice
     *
     * @param AcceptanceTester $I
     */
    public function checkProductPriceA(AcceptanceTester $I): void
    {
        $I->wantToTest('product price A');

        $I->updateConfigInDatabase('blOverrideZeroABCPrices', '1');

        $userData = $this->getExistingUserData();

        $this->preparePriceGroupDataForUser($I, $userData['userId'], 'oxidpricea');

        $productData1 = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        $productData2 = [
            'id' => '1001',
            'title' => 'Test product 1 [EN] šÄßüл',
            'description' => 'Test product 1 short desc [EN] šÄßüл',
            'price' => '100,00 € *'
        ];

        //option "Use normal article price instead of zero A, B, C price" is ON
        $productListPage = $I->openShop()
            ->openCategoryPage('Test category 0 [EN] šÄßüл')
            ->seeProductData($productData1, 1)
            ->seeProductData($productData2, 2);

        $productData1 = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '35,00 € *'
        ];

        $productDetailsPage = $productListPage->loginUser($userData['userLoginName'], $userData['userPassword'])
            ->seeProductData($productData1, 1)
            ->openDetailsPage(1)
            ->seeProductData($productData1)
            ->seeProductUnitPrice('17,50 €/kg')
            ->addProductToBasket(3);

        $basketPage = $productDetailsPage->openCategoryPage('Test category 0 [EN] šÄßüл')
            ->seeProductData($productData2, 2)
            ->openDetailsPage(2)
            ->seeProductData($productData2)
            ->addProductToBasket(1)
            ->openBasket();

        $productData1 = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'amount' => 3,
            'totalPrice' => '105,00 €'
        ];

        $productData2 = [
            'id' => '1001',
            'title' => 'Test product 1 [EN] šÄßüл',
            'amount' => 1,
            'totalPrice' => '100,00 €'
        ];

        $basketPage->seeBasketContains([$productData1, $productData2], '205,00 €');

        $I->updateConfigInDatabase('blOverrideZeroABCPrices', '');
        $I->clearShopCache();
    }

    /**
     * @group product
     * @group productPrice
     *
     * @param AcceptanceTester $I
     */
    public function checkProductPriceC(AcceptanceTester $I): void
    {
        $I->wantToTest('product price C and amount price discount added to this price');

        $userData = $this->getExistingUserData();

        $this->preparePriceGroupDataForUser($I, $userData['userId'], 'oxidpricec');

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];
        $amountPrices = [
            'priceCase1' => [
                'amountFrom' => 4,
                'amountTo' => 9999999,
                'discount' => 20,
            ]
        ];
        $this->prepareAmountPriceDataForProduct($I, $productData['id'], $amountPrices['priceCase1']);

        $productListPage = $I->openShop()
            ->openCategoryPage('Test category 0 [EN] šÄßüл')
            ->seeProductData($productData, 1);

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '55,00 € *'
        ];

        $basketPage = $productListPage->loginUser($userData['userLoginName'], $userData['userPassword'])
            ->seeProductData($productData, 1)
            ->openDetailsPage(1)
            ->seeProductData($productData)
            ->seeProductUnitPrice('27,50 €/kg')
            ->addProductToBasket(5)
            ->openBasket();

        $breadCrumbName = Translator::translate('CART');
        $basketPage->seeOnBreadCrumb($breadCrumbName);

        //amount price discount added to the C price
        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'amount' => 5,
            'totalPrice' => '220,00 €'
        ];

        $basketPage->seeBasketContains([$productData], '220,00 €');
        $I->clearShopCache();
    }

    /**
     * @group product
     * @group productPrice
     *
     * @param AcceptanceTester $I
     */
    public function checkProductPriceB(AcceptanceTester $I): void
    {
        $I->wantToTest('product price B');

        $userData = $this->getExistingUserData();

        $this->preparePriceGroupDataForUser($I, $userData['userId'], 'oxidpriceb');

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        $productListPage = $I->openShop()
            ->openCategoryPage('Test category 0 [EN] šÄßüл')
            ->seeProductData($productData, 1);

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '45,00 € *'
        ];

        $basketPage = $productListPage->loginUser($userData['userLoginName'], $userData['userPassword'])
            ->seeProductData($productData, 1)
            ->openDetailsPage(1)
            ->seeProductData($productData)
            ->seeProductUnitPrice('22,50 €/kg')
            ->addProductToBasket(2)
            ->openBasket();

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'amount' => 2,
            'totalPrice' => '90,00 €'
        ];

        $basketPage->seeBasketContains([$productData], '90,00 €');
        $I->clearShopCache();
    }

    /**
     * @group product
     * @group productPrice
     * @group productAmountPrice
     *
     * @param AcceptanceTester $I
     */
    public function checkProductAmountPrice(AcceptanceTester $I): void
    {
        $productNavigation = new ProductNavigation($I);
        $I->wantToTest('product amount price');

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];
        $amountPrices = [
            'priceCase1' => [
                'amountFrom' => 2,
                'amountTo' => 3,
                'discount' => 10,
            ],
            'priceCase2' => [
                'amountFrom' => 4,
                'amountTo' => 9999999,
                'discount' => 20,
            ]
        ];

        $this->prepareAmountPriceDataForProduct($I, $productData['id'], $amountPrices['priceCase1']);
        $this->prepareAmountPriceDataForProduct($I, $productData['id'], $amountPrices['priceCase2']);

        $productNavigation->openProductDetailsPage($productData['id'])
            ->seeProductData($productData)
            ->seeAmountPrices($amountPrices);
    }

    public function _failed(AcceptanceTester $I) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $I->cleanUp();
        $I->clearShopCache();
    }

    private function getExistingUserData()
    {
        return Fixtures::get('existingUser');
    }

    /**
     * @param AcceptanceTester $I
     * @param string           $productId
     * @param string           $accessoryProductId
     */
    private function prepareAccessoriesDataForProduct(AcceptanceTester $I, $productId, $accessoryProductId): void
    {
        $data = [
            'OXID' => 'testaccessories1',
            'OXOBJECTID' => $accessoryProductId,
            'OXARTICLENID' => $productId,
        ];
        $I->haveInDatabase('oxaccessoire2article', $data);
    }

    /**
     * @param AcceptanceTester $I
     * @param string           $productId
     * @param string           $crossSellingProductId
     */
    private function prepareCrossSellingDataForProduct(AcceptanceTester $I, $productId, $crossSellingProductId): void
    {
        $data = [
            'OXID' => 'testcrossselling1',
            'OXOBJECTID' => $crossSellingProductId,
            'OXARTICLENID' => $productId,
        ];
        $I->haveInDatabase('oxobject2article', $data);
    }

    /**
     * @param AcceptanceTester $I
     * @param string           $userId
     * @param string           $priceGroupId
     */
    private function preparePriceGroupDataForUser(AcceptanceTester $I, $userId, $priceGroupId): void
    {
        $data = [
            'OXID' => 'obj2group' . $priceGroupId,
            'OXOBJECTID' => $userId,
            'OXGROUPSID' => $priceGroupId,
        ];
        $I->haveInDatabase('oxobject2group', $data);
    }

    /**
     * @param AcceptanceTester $I
     * @param string           $productId
     * @param array            $amountPrice
     */
    private function prepareAmountPriceDataForProduct(AcceptanceTester $I, $productId, $amountPrice): void
    {
        $data = [
            'OXID' => 'price2article' . $amountPrice['discount'],
            'OXARTID' => $productId,
            'OXADDPERC' => $amountPrice['discount'],
            'OXAMOUNT' => $amountPrice['amountFrom'],
            'OXAMOUNTTO' => $amountPrice['amountTo'],
        ];
        $I->haveInDatabase('oxprice2article', $data);
    }
}
