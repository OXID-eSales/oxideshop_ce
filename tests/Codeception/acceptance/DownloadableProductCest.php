<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception;

use DateTime;
use OxidEsales\Codeception\Module\Translation\Translator;
use OxidEsales\Codeception\Page\Home;
use OxidEsales\Codeception\Step\Basket;

final class DownloadableProductCest
{
    private string $orderId;

    public function _before(AcceptanceTester $I)
    {
        $I->updateConfigInDatabase('blEnableDownloads', true, 'bool');
        $I->updateConfigInDatabase('iMaxDownloadsCount', "2", 'str');
        $I->updateConfigInDatabase('iLinkExpirationTime', "240", 'str');
        $I->updateConfigInDatabase('blEnableIntangibleProdAgreement', true, 'bool');

        $I->updateInDatabase('oxarticles', ['oxisdownloadable' => 1], ['oxartnum' => '1002-1']);
    }

    public function _after(AcceptanceTester $I)
    {
        $I->updateConfigInDatabase('iMaxDownloadsCount', "0", 'str');
        $I->updateConfigInDatabase('iLinkExpirationTime', "168", 'str');
        $I->updateConfigInDatabase('blEnableIntangibleProdAgreement', false, 'bool');
        $I->updateInDatabase('oxarticles', ['oxisdownloadable' => 0], ['oxartnum' => '1002-1']);
        $I->deleteFromDatabase('oxorder', ['OXID' => $this->orderId]);
        $I->deleteFromDatabase('oxorderarticles', ['OXORDERID' => $this->orderId]);
    }

    public function downloadableFiles(AcceptanceTester $I): void
    {
        $I->wantToTest('Product downloadable files');

        $I->clearShopCache();
        $startPage = $I->loginShopWithExistingUser();
        $this->makePurchaseComplete($I);

        $this->orderId = $I->grabFromDatabase('oxorder', 'OXID', ['oxuserid' => 'testuser']);
        $articleId = $I->grabFromDatabase('oxorderarticles', 'OXID', ['OXORDERID' => $this->orderId]);

        $I->haveInDatabase(
            'oxorderfiles',
            [
                'OXID' => "testdownloadProductCest",
                'OXORDERID' => $this->orderId,
                'OXFILENAME' => 'testFile3',
                'OXFILEID' => '1000l',
                'OXSHOPID' => 1,
                'OXORDERARTICLEID' => $articleId,
                'OXDOWNLOADCOUNT' => '0',
                'OXMAXDOWNLOADCOUNT' => 2,
                'OXDOWNLOADEXPIRATIONTIME' => 24,
                'OXLINKEXPIRATIONTIME' => 240,
                'OXRESETCOUNT' => 0,
                'OXVALIDUNTIL' => (new DateTime())->modify('+1 week')->format('Y-m-d 00:00:00'),
                'OXTIMESTAMP' => (new DateTime())->format('Y-m-d 00:00:00')
            ]
        );

        $I->haveInDatabase(
            'oxfiles',
            [
                'OXID' => '1000l',
                'OXARTID' => '1002-1',
                'OXFILENAME' => 'testFile3',
                'OXPURCHASEDONLY' => 1,
                'OXSTOREHASH' => 'e48a1b571bd2d2e60fb2d9b1b76b35d5',
            ]
        );

        $this->checkMyDownloads($I, $startPage);
        $this->makeOrderComplete($I);
        $this->checkFileInMyDownloads($I, $startPage);
    }

    private function makePurchaseComplete(AcceptanceTester $I): void
    {
        $basket = new Basket($I);
        $basket->addProductToBasketAndOpenUserCheckout('1002-1', 1)
            ->goToNextStep()
            ->goToNextStep()
            ->confirmDownloadableProductsAgreement()
            ->submitOrderSuccessfully();
    }

    private function checkMyDownloads(AcceptanceTester $I, Home $startPage): void
    {
        $accountPage = $startPage->openAccountPage();
        $accountPage->openMyDownloadsPage();
        $I->see(Translator::translate('DOWNLOADS_PAYMENT_PENDING'));
    }

    private function makeOrderComplete(AcceptanceTester $I): void
    {
        $currentTime = date('Y-m-d H:i:s');
        $I->updateInDatabase('oxorder', ['oxpaid' => $currentTime], ['OXID' => $this->orderId]);
    }

    private function checkFileInMyDownloads(AcceptanceTester $I, Home $startPage): void
    {
        $accountPage = $startPage->openAccountPage();
        $accountPage->openMyDownloadsPage();
        $I->dontSee(Translator::translate('DOWNLOADS_PAYMENT_PENDING'));
        $I->click(".downloadList a");
    }
}
