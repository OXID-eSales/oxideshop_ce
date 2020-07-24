<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\CodeceptionAdmin;

use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Page\Home;
use OxidEsales\Codeception\Step\Basket;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceTester;

final class DownloadableProductCest
{
    /** @param AcceptanceTester $I */
    public function downloadableFiles(AcceptanceTester $I): void
    {
        $I->wantToTest('Product downloadable files');

        $I->clearShopCache();
        $startPage = $I->loginShopWithExistingUser();

        $this->enableDownloadableFilesAndSetForAProduct($I);
        $this->makePurchaseComplete($I, $startPage);
        $this->checkMyDownloads($I, $startPage);
        $this->makeOrderComplete($I);
        $this->checkFileInMyDownloads($I, $startPage);
    }

    /**
     * @param AcceptanceTester $I
     */
    private function enableDownloadableFilesAndSetForAProduct(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabase('blEnableDownloads', true, 'bool');
        $I->updateConfigInDatabase('iMaxDownloadsCount', "2", 'str');
        $I->updateConfigInDatabase('iLinkExpirationTime', "240", 'str');
        $I->updateConfigInDatabase('iDownloadExpirationTime', "24", 'str');
        $I->updateConfigInDatabase('iMaxDownloadsCountUnregistered', "1", 'str');

        $I->updateInDatabase('oxarticles', ['oxisdownloadable' => 1], ['oxartnum' => '1002-1']);
    }

    /**
     * @param AcceptanceTester $I
     * @param Home             $startPage
     */
    private function makePurchaseComplete(AcceptanceTester $I, Home $startPage): void
    {
        $basket = new Basket($I);
        $userCheckoutPage = $basket->addProductToBasketAndOpenUserCheckout('1002-1', 1);
        $userCheckoutPage->goToNextStep();
        $I->click(['name' => 'userform']);
        $I->checkOption('oxdownloadableproductsagreement');
        $I->click("//form[@id='orderConfirmAgbBottom']//button");
        $userCheckoutPage->seeOnBreadCrumb(Translator::translate('ORDER_COMPLETED'));
    }

    /**
     * @param AcceptanceTester $I
     * @param Home             $startPage
     */
    private function checkMyDownloads(AcceptanceTester $I, Home $startPage): void
    {
        $accountPage = $startPage->openAccountPage();
        $accountPage->openMyDownloadsPage();
        $I->see(Translator::translate('DOWNLOADS_PAYMENT_PENDING'));
    }

    /**
     * @param AcceptanceTester $I
     */
    private function makeOrderComplete(AcceptanceTester $I): void
    {
        $currentTime = date('Y-m-d H:i:s');
        $I->updateInDatabase('oxorder', ['oxpaid' => $currentTime], ['oxordernr' => 2]);
    }

    /**
     * @param AcceptanceTester $I
     * @param Home             $startPage
     */
    private function checkFileInMyDownloads(AcceptanceTester $I, Home $startPage): void
    {
        $accountPage = $startPage->openAccountPage();
        $accountPage->openMyDownloadsPage();
        $I->dontSee(Translator::translate('DOWNLOADS_PAYMENT_PENDING'));
        $I->click(".downloadList a");
    }
}
