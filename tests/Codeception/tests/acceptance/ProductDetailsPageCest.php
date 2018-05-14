<?php

use Step\Acceptance\ProductNavigation;
use Step\Acceptance\Basket;
use Step\Acceptance\Start;

class ProductDetailsPageCest
{
    /**
     * @group product
     *
     * @param AcceptanceTester $I
     */
    public function euroSignInTitle(AcceptanceTester $I, Start $start)
    {
        $I->wantToTest('euro sign in the product title');

        //Add euro sign to the product title
        $I->updateInDatabase('oxarticles', ["OXTITLE" => '[DE 2] Test product 2 šÄßüл €'], ["OXID" => 1000]);

        $productData = [
            'id' => 1000,
            'title' => '[DE 2] Test product 2 šÄßüл €',
            'desc' => 'Test product 0 short desc [DE]',
            'price' => '50,00 € *'
        ];

        $searchListPage = $start->searchFor($productData['id'])
            ->switchLanguage('Deutsch');

        $searchListPage->seeProductData($productData, 1);

        $searchListPage->switchLanguage('English');

        //Remove euro sign from the product title
        $I->updateInDatabase('oxarticles', ["OXTITLE" => '[DE 2] Test product 2 šÄßüл'], ["OXID" => 1000]);
    }

    /**
     * @group product
     *
     * @param AcceptanceTester $I
     */
    public function detailsPageNavigation(AcceptanceTester $I, Start $start)
    {
        $I->wantToTest('product navigation in details page');

        $productData = [
            'id' => 1001,
            'title' => 'Test product 1 [EN] šÄßüл',
            'desc' => 'Test product 1 short desc [EN] šÄßüл',
            'price' => '100,00 € *'
        ];

        $searchListPage = $start->searchFor('100')
            ->seeProductData($productData, 2);
        $detailsPage = $searchListPage->openProductDetailsPage(2);
        $breadCrumb = $I->translate('YOU_ARE_HERE').':'.sprintf($I->translate('SEARCH_RESULT'), '100');
        $I->see($breadCrumb);
        $navigationText = $I->translate('PRODUCT').' 2 '.$I->translate('OF').' 4';
        $I->see($navigationText);
        $detailsPage = $detailsPage->openNextProduct();
        $navigationText = $I->translate('PRODUCT').' 3 '.$I->translate('OF').' 4';
        $I->see($navigationText);
        $detailsPage = $detailsPage->openPreviousProduct();
        $navigationText = $I->translate('PRODUCT').' 2 '.$I->translate('OF').' 4';
        $I->see($navigationText);
        $searchListPage = $detailsPage->openProductSearchList();
        $searchListPage->seeProductData($productData, 2);
        $breadCrumb = $I->translate('YOU_ARE_HERE').':'.$I->translate('SEARCH');
        $I->see($breadCrumb);
    }

    /**
     * @group product
     *
     * @param AcceptanceTester $I
     */
    public function detailsPageInformation(AcceptanceTester $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('product information in details page');

        $data = [
            'OXID' => 'testsellist1',
            'OXTITLE' => 'test selection list [DE] šÄßüл',
            'OXIDENT' => 'test sellist šÄßüл',
            'OXVALDESC' => 'selvar1 [DE]!P!1__@@selvar2 [DE]__@@selvar3 [DE]!P!-2__@@selvar4 [DE]!P!2%__@@',
            'OXTITLE_1' => 'test selection list [EN] šÄßüл',
            'OXVALDESC_1' => 'selvar1 [EN] šÄßüл!P!1__@@selvar2 [EN] šÄßüл__@@selvar3 [EN] šÄßüл!P!-2__@@selvar4 [EN] šÄßüл!P!2%__@@',
        ];
        $I->haveInDatabase('oxselectlist', $data);

        $data = [
            'OXID' => 'obj2sellist1',
            'OXOBJECTID' => '1001',
            'OXSELNID' => 'testsellist1',
        ];
        $I->haveInDatabase('oxobject2selectlist', $data);

        $productData = [
            'id' => 1001,
            'title' => 'Test product 1 [EN] šÄßüл',
            'desc' => 'Test product 1 short desc [EN] šÄßüл',
            'price' => '100,00 € *'
        ];

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id'])
            ->seeProductData($productData)
            ->seeProductOldPrice('150,00 €');
        $I->see($I->translate('MESSAGE_NOT_ON_STOCK'));
        $I->see($I->translate('AVAILABLE_ON') . ' 2030-01-01');
        $detailsPage->selectSelectionListItem('selvar1 [EN] šÄßüл')
            ->selectSelectionListItem('selvar2 [EN] šÄßüл')
            ->selectSelectionListItem('selvar3 [EN] šÄßüл')
            ->selectSelectionListItem('selvar4 [EN] šÄßüл');
        $detailsPage->openDescription();
        $I->see('Test product 1 long description [EN] šÄßüл');
        $detailsPage->openAttributes()
            ->seeAttributeName('Test attribute 1 [EN] šÄßüл',1)
            ->seeAttributeValue('attr value 11 [EN] šÄßüл', 1)
            ->seeAttributeName('Test attribute 3 [EN] šÄßüл',2)
            ->seeAttributeValue('attr value 3 [EN] šÄßüл', 2)
            ->seeAttributeName('Test attribute 2 [EN] šÄßüл',3)
            ->seeAttributeValue('attr value 12 [EN] šÄßüл', 3);
    }

    /**
     * @group product
     *
     * @param AcceptanceTester  $I
     * @param ProductNavigation $productNavigation
     */
    public function sendProductSuggestionEmail(AcceptanceTester $I, ProductNavigation $productNavigation)
    {
        $I->wantTo('send the product suggestion email');

        $I->updateConfigInDatabase('iUseGDVersion', '', 'str');

        $productData = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
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

        $suggestionPage = $detailsPage->openProductSuggestionPage()->sendSuggestionEmail($emptyEmailData);
        $I->see($I->translate('ERROR_MESSAGE_INPUT_NOTALLFIELDS'));
        $suggestionPage->sendSuggestionEmail($suggestionEmailData);
        $I->see($productData['title']);

        $I->updateConfigInDatabase('iUseGDVersion', '2', 'str');
    }

    /**
     * @group product
     *
     * @param AcceptanceTester  $I
     * @param ProductNavigation $productNavigation
     */
    public function productPriceAlert(AcceptanceTester $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('product price alert functionality');

        $I->updateConfigInDatabase('iUseGDVersion', '', 'str');

        $productData = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);
        $I->see($I->translate('PRICE_ALERT'));

        $detailsPage->sendPriceAlert('example_test@oxid-esales.dev', '99.99');
        $I->see($I->translate('PAGE_DETAILS_THANKYOUMESSAGE3').' 99,99 € '.$I->translate('PAGE_DETAILS_THANKYOUMESSAGE4'));
        $I->see($productData['title']);

        //disabling price alert for product(1000)
        $I->updateInDatabase('oxarticles', ["oxblfixedprice" => 1], ["OXID" => 1000]);

        //open details page
        $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);
        $I->dontSee($I->translate('PRICE_ALERT'));

        $I->updateConfigInDatabase('iUseGDVersion', '2', 'str');
    }

    /**
     * @group product
     *
     * @param AcceptanceTester  $I
     * @param ProductNavigation $productNavigation
     */
    public function productVariantSelection(AcceptanceTester $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('product variant selection in details page');

        $productData = [
            'id' => 1002,
            'title' => 'Test product 2 [EN] šÄßüл',
            'desc' => 'Test product 2 short desc [EN] šÄßüл',
            'price' => 'from 55,00 € *'
        ];

        $variantData1 = [
            'id' => '1002-1',
            'title' => 'Test product 2 [EN] šÄßüл var1 [EN] šÄßüл',
            'desc' => '',
            'price' => '55,00 € *'
        ];

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);
        $detailsPage->seeProductData($productData);

        // select variant
        $detailsPage = $detailsPage->selectVariant(1, 'var1 [EN] šÄßüл')
            ->seeProductData($variantData1);

        $basketItem1 = [
            'title' => 'Test product 2 [EN] šÄßüл, var1 [EN] šÄßüл',
            'price' => '110,00 €',
            'amount' => 2
        ];
        $detailsPage = $detailsPage->addProductToBasket(2)
            ->seeMiniBasketContains([$basketItem1], '110,00 €', 2);

        $basketItem1 = [
            'title' => 'Test product 2 [EN] šÄßüл, var1 [EN] šÄßüл',
            'price' => '165,00 €',
            'amount' => 3
        ];
        $detailsPage = $detailsPage->addProductToBasket(1)
            ->seeMiniBasketContains([$basketItem1], '165,00 €', 3);

        // select second variant
        $variantData2 = [
            'id' => '1002-2',
            'title' => 'Test product 2 [EN] šÄßüл var2 [EN] šÄßüл',
            'desc' => '',
            'price' => '67,00 € *'
        ];

        $detailsPage = $detailsPage->selectVariant(1, 'var2 [EN] šÄßüл')
            ->seeProductData($variantData2);

        $basketItem2 = [
            'title' => 'Test product 2 [EN] šÄßüл, var2 [EN] šÄßüл',
            'price' => '201,00 €',
            'amount' => 3
        ];
        $detailsPage->addProductToBasket(2)
            ->addProductToBasket(1)
            ->seeMiniBasketContains([$basketItem1, $basketItem2], '366,00 €', 6);
    }

    /**
     * @group product
     *
     * @param AcceptanceTester  $I
     * @param ProductNavigation $productNavigation
     */
    public function productAccessories(AcceptanceTester $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('Product\'s accessories');

        $data = [
            'OXID' => 'testaccessories1',
            'OXOBJECTID' => '1002',
            'OXARTICLENID' => '1000',
        ];
        $I->haveInDatabase('oxaccessoire2article', $data);

        $productData = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        $accessoryData = [
            'id' => 1002,
            'title' => 'Test product 2 [EN] šÄßüл',
            'desc' => 'Test product 2 short desc [EN] šÄßüл',
            'price' => 'from 55,00 €'
        ];

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        $I->see($I->translate('ACCESSORIES'));
        $detailsPage->seeAccessoryData($accessoryData, 1);
        $accessoryDetailsPage = $detailsPage->openAccessoryDetailsPage(1);
        $accessoryDetailsPage->seeProductData($accessoryData);
    }

    /**
     * @group product
     *
     * @param AcceptanceTester  $I
     * @param ProductNavigation $productNavigation
     */
    public function similarProducts(AcceptanceTester $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('similar products');

        $productData = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        $similarProductData = [
            'id' => 1001,
            'title' => 'Test product 1 [EN] šÄßüл',
            'desc' => 'Test product 1 short desc [EN] šÄßüл',
            'price' => '100,00 € *'
        ];

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        $I->see($I->translate('SIMILAR_PRODUCTS'));
        $detailsPage->seeSimilarProductData($similarProductData, 1);
        $accessoryDetailsPage = $detailsPage->openSimilarProductDetailsPage(1);
        $accessoryDetailsPage->seeProductData($similarProductData);
        $detailsPage->seeSimilarProductData($productData, 1);
    }

    /**
     * @group product
     *
     * @param AcceptanceTester  $I
     * @param ProductNavigation $productNavigation
     */
    public function productCrossSelling(AcceptanceTester $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('Product\'s crossselling');

        $data = [
            'OXID' => 'testcrossselling1',
            'OXOBJECTID' => '1002',
            'OXARTICLENID' => '1000',
        ];
        $I->haveInDatabase('oxobject2article', $data);

        $productData = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        $crossSellingProductData = [
            'id' => 1002,
            'title' => 'Test product 2 [EN] šÄßüл',
            'desc' => 'Test product 2 short desc [EN] šÄßüл',
            'price' => 'from 55,00 €'
        ];

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);

        $I->see($I->translate('HAVE_YOU_SEEN'));
        $detailsPage->seeCrossSellingData($crossSellingProductData, 1);
        $accessoryDetailsPage = $detailsPage->openCrossSellingDetailsPage(1);
        $accessoryDetailsPage->seeProductData($crossSellingProductData);
    }

    /**
     * @group main
     *
     * @param ProductNavigation $productNavigation
     */
    public function multidimensionalVariantsInDetailsPage(ProductNavigation $productNavigation)
    {
        $productNavigation->wantToTest('multidimensional variants functionality in details page');

        $productData = [
            'id' => 10014,
            'title' => '14 EN product šÄßüл',
            'desc' => '13 EN description šÄßüл',
            'price' => 'from 15,00 € *'
        ];

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);

        //assert product
        $detailsPage->seeProductData($productData)
            ->checkIfProductIsNotBuyable();

        //select a variant of the product
        $detailsPage->selectVariant(1, 'S')
            ->checkIfProductIsNotBuyable();
        $detailsPage->selectVariant(2, 'black')
            ->checkIfProductIsNotBuyable();
        $detailsPage->selectVariant(3, 'lether');

        //assert product
        $productData = [
            'id' => '10014-1-1',
            'title' => '14 EN product šÄßüл S | black | lether',
            'desc' => '',
            'price' => '25,00 € *'
        ];
        $detailsPage->seeProductData($productData)
            ->checkIfProductIsBuyable();

        //select a variant of the product
        $detailsPage = $detailsPage->selectVariant(2, 'white')
            ->checkIfProductIsNotBuyable();

        $detailsPage = $detailsPage->selectVariant(1, 'S');

        //assert product
        $productData = [
            'id' => '10014-1-3',
            'title' => '14 EN product šÄßüл S | white',
            'desc' => '',
            'price' => '15,00 € *'
        ];
        $detailsPage->seeProductData($productData)
            ->checkIfProductIsBuyable();

        $detailsPage->selectVariant(2, 'black')
            ->selectVariant(3, 'lether')
            ->selectVariant(1, 'L');

        //assert product
        $productData = [
            'id' => '10014-3-1',
            'title' => '14 EN product šÄßüл L | black | lether',
            'desc' => '',
            'price' => '15,00 € *'
        ];
        $detailsPage->seeProductData($productData)
            ->checkIfProductIsBuyable();

        $detailsPage = $detailsPage->addProductToBasket(2);

        //assert product in basket
        $basketItem = [
            'title' => '14 EN product šÄßüл, L | black | lether',
            'price' => '30,00 €',
            'amount' => 2
        ];
        $detailsPage->seeMiniBasketContains([$basketItem], '30,00 €', 2);
    }

    /**
     * @group product
     *
     * @param AcceptanceTester $I
     */
    public function multidimensionalVariantsInLists(AcceptanceTester $I)
    {
        $I->wantToTest('multidimensional variants functionality in lists');

        $productData = [
            'id' => 10014,
            'title' => '14 EN product šÄßüл',
            'desc' => '13 EN description šÄßüл',
            'price' => 'from 15,00 €'
        ];

        $searchListPage = $I->openShop()
            ->searchFor($productData['id']);

        $searchListPage->seeProductData($productData, 1);

        $detailsPage = $searchListPage->selectVariant(1, 'M');
        $detailsPage->seeProductData($productData);
    }

    /**
     * @group main
     *
     * @param AcceptanceTester $I
     * @param ProductNavigation $productNavigation
     */
    public function multidimensionalVariantsAndJavaScript(AcceptanceTester $I, ProductNavigation $productNavigation)
    {
        $productNavigation->wantToTest('if after md variants selection in details page all other js are still working correctly');

        $data = [
            'OXID' => '1001411',
            'OXLONGDESC' => 'Test description',
            'OXLONGDESC_1' => 'Test description',
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
            'id' => 10014,
            'title' => '14 EN product šÄßüл',
            'desc' => '13 EN description šÄßüл',
            'price' => 'from 15,00 € *'
        ];

        //open details page
        $detailsPage = $productNavigation->openProductDetailsPage($productData['id']);

        //assert product
        $detailsPage->seeProductData($productData)
            ->checkIfProductIsNotBuyable();

        //select a variant of the product
        $detailsPage->selectVariant(1, 'S')
            ->checkIfProductIsNotBuyable();
        $detailsPage->selectVariant(2, 'black')
            ->checkIfProductIsNotBuyable();
        $detailsPage->selectVariant(3, 'lether');

        //assert product
        $productData = [
            'id' => '10014-1-1',
            'title' => '14 EN product šÄßüл S | black | lether',
            'desc' => '',
            'price' => '25,00 € *'
        ];
        $detailsPage->seeProductData($productData)
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
        $detailsPage->seeMiniBasketContains([$basketItem], '50,00 €', 2);
    }

    /**
     * @group product
     *
     * @param AcceptanceTester $I
     */
    public function multidimensionalVariantsAreOff(AcceptanceTester $I)
    {
        $I->wantToTest('multidimensional variants functionality is disabled');

        //multidimensional variants off
        $I->updateConfigInDatabase('blUseMultidimensionVariants', '');

        $productData = [
            'id' => 10014,
            'title' => '14 EN product šÄßüл',
            'desc' => '13 EN description šÄßüл',
            'price' => 'from 15,00 €'
        ];

        $searchListPage = $I->openShop()
            ->searchFor($productData['id']);

        $searchListPage->seeProductData($productData, 1);

        $detailsPage = $searchListPage->selectVariant(1, 'S | black | material');

        $productData = [
            'id' => 10014,
            'title' => '14 EN product šÄßüл',
            'desc' => '13 EN description šÄßüл',
            'price' => '15,00 €'
        ];

        $detailsPage->seeProductData($productData)
            ->dontSeeVariant(1, 'M | black | lether')  //10014-2-1: out of stock - offline
            ->seeVariant(1, 'M | black | material');   //10014-2-2: out of stock - not orderable

        //making 10014-2-1 and 10014-2-2 variants in stock
        $I->updateInDatabase('oxarticles', ["OXSTOCK" => 1], ["OXID" => '1001421']);
        $I->updateInDatabase('oxarticles', ["OXSTOCK" => 1], ["OXID" => '1001422']);

        $productData = [
            'id' => 10014,
            'title' => '14 EN product šÄßüл S | white',
            'desc' => '13 EN description šÄßüл',
            'price' => '15,00 €'
        ];

        $detailsPage->selectVariant(1, 'S | white')->seeProductData($productData)
            ->seeVariant(1, 'M | black | lether')
            ->seeVariant(1, 'M | black | material');

        //roll back data
        $I->updateInDatabase('oxarticles', ["OXSTOCK" => 0], ["OXID" => '1001421']);
        $I->updateInDatabase('oxarticles', ["OXSTOCK" => 0], ["OXID" => '1001422']);
        //multidimensional variants on
        $I->updateConfigInDatabase('blUseMultidimensionVariants', '1');
    }

    /**
     * @group product
     *
     * @param AcceptanceTester $I
     */
    public function productPriceA(AcceptanceTester $I)
    {
        $I->wantToTest('product price A');

        $I->updateConfigInDatabase('blOverrideZeroABCPrices', '1');

        $userData = $this->getExistingUserData();

        $data = [
            'OXID' => 'obj2group1',
            'OXOBJECTID' => $userData['userId'],
            'OXGROUPSID' => 'oxidpricea',
        ];
        $I->haveInDatabase('oxobject2group', $data);

        $productData1 = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        $productData2 = [
            'id' => 1001,
            'title' => 'Test product 1 [EN] šÄßüл',
            'desc' => 'Test product 1 short desc [EN] šÄßüл',
            'price' => '100,00 € *'
        ];

        //option "Use normal article price instead of zero A, B, C price" is ON
        $productListPage = $I->openShop()->openCategoryPage('Test category 0 [EN] šÄßüл');
        $productListPage->seeProductData($productData1, 1)
            ->seeProductData($productData2, 2);

        $productData1 = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '35,00 € *'
        ];

        $productListPage->loginUser($userData['userLoginName'], $userData['userPassword'])
            ->seeProductData($productData1, 1);

        $productDetailsPage = $productListPage->openDetailsPage(1)
            ->seeProductData($productData1)
            ->seeProductUnitPrice('17,50 €/kg')
            ->addProductToBasket(3);

        $productListPage = $productDetailsPage->openCategoryPage('Test category 0 [EN] šÄßüл')
            ->seeProductData($productData2, 2);
        $productDetailsPage = $productListPage->openDetailsPage(2)
            ->seeProductData($productData2)
            ->addProductToBasket();
        $basketPage = $productDetailsPage->openBasket();

        $productData1 = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'amount' => 3,
            'totalPrice' => '105,00 €'
        ];

        $productData2 = [
            'id' => 1001,
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
     *
     * @param AcceptanceTester $I
     */
    public function productPriceC(AcceptanceTester $I)
    {
        $I->wantToTest('product price C and amount price discount added to this price');

        $userData = $this->getExistingUserData();

        $data = [
            'OXID' => 'obj2group2',
            'OXOBJECTID' => $userData['userId'],
            'OXGROUPSID' => 'oxidpricec',
        ];
        $I->haveInDatabase('oxobject2group', $data);

        $data = [
            'OXID' => 'price2article1',
            'OXARTID' => 1000,
            'OXADDPERC' => 20,
            'OXAMOUNT' => 4,
            'OXAMOUNTTO' => 9999999,
        ];
        $I->haveInDatabase('oxprice2article', $data);

        $productData1 = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        $productListPage = $I->openShop()->openCategoryPage('Test category 0 [EN] šÄßüл');
        $productListPage->seeProductData($productData1, 1);

        $productData1 = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '55,00 € *'
        ];

        $productListPage->loginUser($userData['userLoginName'], $userData['userPassword'])
            ->seeProductData($productData1, 1);

        $productDetailsPage = $productListPage->openDetailsPage(1)
            ->seeProductData($productData1)
            ->seeProductUnitPrice('27,50 €/kg')
            ->addProductToBasket(5);

        $basketPage = $productDetailsPage->openBasket();

        //amount price discount added to the C price
        $productData1 = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'amount' => 5,
            'totalPrice' => '220,00 €'
        ];

        $basketPage->seeBasketContains([$productData1], '220,00 €');
        $I->clearShopCache();
    }

    /**
     * @group product
     *
     * @param AcceptanceTester $I
     */
    public function productPriceB(AcceptanceTester $I)
    {
        $I->wantToTest('product price B');

        $userData = $this->getExistingUserData();

        $data = [
            'OXID' => 'obj2group2',
            'OXOBJECTID' => $userData['userId'],
            'OXGROUPSID' => 'oxidpriceb',
        ];
        $I->haveInDatabase('oxobject2group', $data);

        $productData1 = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];

        $productListPage = $I->openShop()->openCategoryPage('Test category 0 [EN] šÄßüл');
        $productListPage->seeProductData($productData1, 1);

        $productData1 = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '45,00 € *'
        ];

        $productListPage->loginUser($userData['userLoginName'], $userData['userPassword'])
            ->seeProductData($productData1, 1);

        $productDetailsPage = $productListPage->openDetailsPage(1)
            ->seeProductData($productData1)
            ->seeProductUnitPrice('22,50 €/kg')
            ->addProductToBasket(2);

        $basketPage = $productDetailsPage->openBasket();

        $productData1 = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'amount' => 2,
            'totalPrice' => '90,00 €'
        ];

        $basketPage->seeBasketContains([$productData1], '90,00 €');
        $I->clearShopCache();
    }

    /**
     * @group product
     *
     * @param AcceptanceTester $I
     * @param Basket           $basket
     */
    public function bundledProduct(AcceptanceTester $I, Basket $basket)
    {
        $I->wantToTest('bundled product');

        $I->updateInDatabase('oxarticles', ["OXBUNDLEID" => '1001'], ["OXID" => '1000']);

        //add Product to basket
        /** @var \Page\Basket $basketPage */
        $basketPage = $basket->addProductToBasket('1000', 1, 'basket');

        $productData = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'amount' => 1,
            'totalPrice' => '50,00 €'
        ];

        $bundledProductData = [
            'id' => 1001,
            'title' => 'Test product 1 [EN] šÄßüл',
            'amount' => '+1'
        ];

        $basketPage->seeBasketContains([$productData], '50,00 €')
            ->seeBasketContainsBundledProduct($bundledProductData, 2);

        $I->updateInDatabase('oxarticles', ["OXBUNDLEID" => ''], ["OXID" => '1000']);
    }

    /**
     * @group product
     *
     * @param AcceptanceTester  $I
     * @param ProductNavigation $productNavigation
     */
    public function productAmountPrice(AcceptanceTester $I, ProductNavigation $productNavigation)
    {
        $I->wantToTest('product amount price');

        $data = [
            'OXID' => 'price2article1',
            'OXARTID' => 1000,
            'OXADDPERC' => 20,
            'OXAMOUNT' => 4,
            'OXAMOUNTTO' => 9999999,
        ];
        $I->haveInDatabase('oxprice2article', $data);

        $data = [
            'OXID' => 'price2article2',
            'OXARTID' => 1000,
            'OXADDPERC' => 10,
            'OXAMOUNT' => 2,
            'OXAMOUNTTO' => 3,
        ];
        $I->haveInDatabase('oxprice2article', $data);

        $productData = [
            'id' => 1000,
            'title' => 'Test product 0 [EN] šÄßüл',
            'desc' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
        ];
        $amountPrices = [
            '2' => '10% '.$I->translate('DISCOUNT'),
            '4' => '20% '.$I->translate('DISCOUNT')
        ];

        $productNavigation->openProductDetailsPage($productData['id'])
            ->seeProductData($productData)
            ->seeAmountPrices($amountPrices);
    }

    private function getExistingUserData()
    {
        $userLoginData = [
            "userId" => "testuser",
            "userLoginName" => "example_test@oxid-esales.dev",
            "userPassword" => "useruser",
            "userName" => "UserNamešÄßüл",
            "userLastName" => "UserSurnamešÄßüл",
        ];
        return $userLoginData;
    }

}
