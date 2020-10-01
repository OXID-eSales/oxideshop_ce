<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception;

use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Page\Details\ProductDetails;
use OxidEsales\Codeception\Page\Home;

final class NewLanguageNavigationCest
{
    /** @var string */
    private $languages;

    /** @var string */
    private $languageParams;

    /** @var array */
    private $productData = [
        'id'          => '3503',
        'title'       => 'Kuyichi leather belt JEVER',
        'description' => 'Leather belt, unisex',
        'price'       => '29,90 €'
    ];

    /** @param AcceptanceTester $I */
    public function _before(AcceptanceTester $I)
    {
        $this->languages = $I->grabConfigValueFromDatabase('aLanguages', 1)['value'];
        $this->languageParams = $I->grabConfigValueFromDatabase('aLanguageParams', 1)['value'];
    }

    /** @param AcceptanceTester $I */
    public function _after(AcceptanceTester $I)
    {
        $I->updateConfigInDatabase('aLanguages', $this->languages);
        $I->updateConfigInDatabase('aLanguageParams', $this->languageParams);
        $I->regenerateDatabaseViews();
    }

    /** @param AcceptanceTester $I */
    public function newLanguageNavigation(AcceptanceTester $I): void
    {
        $I->wantToTest('if navigation to a newly created language works correctly');

        $this->createNewLanguage('lt', 'Lietuviu', $I);
        $I->regenerateDatabaseViews();

        $I->clearShopCache();
        $shop = $I->openShop();

        $I->assertEquals("Lietuviu", $I->grabAttributeFrom(".languages-menu ul li:nth-child(3) a", "title"));

        $productDetailsPage = $this->checkProductDetails($shop, $I);

        $this->switchLanguageAndCheckProductDetails($productDetailsPage, $I);
    }

    private function createNewLanguage(string $code, string $name, AcceptanceTester $I): void
    {
        $languages = unserialize($this->languages);
        $languages[$code] = $name;
        $I->updateConfigInDatabase('aLanguages', serialize($languages), 'aarr');

        $languageParams = unserialize($this->languageParams);
        $languageParams[$code] = [
            'baseId' => count($languageParams),
            'active' => '1',
            'sort'   => (string)(count($languageParams) + 1),
        ];
        $I->updateConfigInDatabase('aLanguageParams', serialize($languageParams), 'aarr');
    }

    private function checkProductDetails(Home $shop, AcceptanceTester $I): ProductDetails
    {
        $searchListPage = $shop->searchFor($this->productData['id']);

        $expectedHeader = '1 ' . Translator::translate('HITS_FOR') . ' ' . sprintf('"%s"', $this->productData['id']);
        $I->assertEquals($expectedHeader, $I->grabTextFrom("//h1"));

        $productDetailsPage = $searchListPage->openProductDetailsPage(1);
        $productDetailsPage->seeProductData($this->productData);

        return $productDetailsPage;
    }

    private function switchLanguageAndCheckProductDetails(ProductDetails $productDetailsPage, AcceptanceTester $I): void
    {
        $productDetailsPage->switchLanguage("Lietuviu");
        $I->see($this->productData['price'], '#productPrice');
        $I->see($this->productData['id'], '.detailsInfo');
    }
}
