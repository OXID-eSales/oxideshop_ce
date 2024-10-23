<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Step\ProductNavigation;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

final class FlowThemeCest
{
    #[group('flow_theme')]
    public function selectMultidimensionalVariantsInLists(AcceptanceTester $I): void
    {
        $I->markTestSkipped('make it work with APEX or remove');
        $I->wantToTest('multidimensional variants functionality in lists');

        $I->updateConfigInDatabase('blUseMultidimensionVariants', true, 'bool');
        $I->updateConfigInDatabase('bl_perfLoadSelectListsInAList', true, 'bool');
        $I->updateConfigInDatabase('bl_perfLoadSelectLists', true, 'bool');

        $productData = [
            'id' => '10014',
            'title' => '14 EN product šÄßüл',
            'description' => '13 EN description šÄßüл',
            'price' => 'from 15,00 €'
        ];

        $searchListPage = $I->openShop()
            ->searchFor($productData['id']);

        $searchListPage->seeProductData($productData, 1);

        $detailsPage = $searchListPage->selectVariant(1, 'M');
        $detailsPage->seeProductData($productData);
    }

    #[group('flow_theme', 'product', 'priceAlarm')]
    public function sendProductPriceAlert(AcceptanceTester $I): void
    {
        $I->markTestSkipped('make it work with APEX or remove');

        $productNavigation = new ProductNavigation($I);
        $I->wantToTest('product price alert functionality');

        $I->updateConfigInDatabase('sProductListNavigation', true);
        $I->updateConfigInDatabase('bl_showPriceAlarm', true, 'bool');

        $I->updateConfigInDatabase('blAllowSuggestArticle', true, 'bool');
        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
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

    #[group('flow_theme', 'product', 'priceAlarm')]
    public function disableProductPriceAlert(AcceptanceTester $I): void
    {
        $I->markTestSkipped('make it work with APEX or remove');

        $productNavigation = new ProductNavigation($I);
        $I->wantToTest('product price alert functionality is disabled');

        $I->updateConfigInDatabase('sProductListNavigation', true);

        $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
        ];

        //disabling price alert for product(1000)
        $I->updateInDatabase('oxarticles', ['oxblfixedprice' => 1], ['OXID' => '1000']);

        //open details page
        $productNavigation->openProductDetailsPage($productData['id']);
        $I->see($productData['title']);
        $I->dontSee(Translator::translate('PRICE_ALERT'));
    }
}
