<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Admin;

use Codeception\Attribute\Group;
use DateTime;
use OxidEsales\Codeception\Admin\AdminPanel;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

#[Group('admin')]
final class AdminDownloadableProductCest
{
    private string $orderId;
    private array $productData = [
            'id' => '1000',
            'title' => 'Test product 0 [EN] šÄßüл',
            'description' => 'Test product 0 short desc [EN] šÄßüл',
            'price' => '50,00 € *'
    ];

    public function _before(AcceptanceAdminTester $I): void
    {

        $I->updateInDatabase('oxarticles', ['oxisdownloadable' => 1], ['oxartnum' => $this->productData['id']]);
        $userId = $I->grabFromDatabase('oxuser', 'OXID', ['OXUSERNAME' => 'user@oxid-esales.com']);
        //$this->orderId = $I->grabFromDatabase('oxorder', 'OXID', ['oxuserid' => $userId]);
       // $articleId = $I->grabFromDatabase('oxorderarticles', 'OXID', ['OXORDERID' => $this->orderId]);
        $articleId = $this->productData['id'];

        $this->orderId = 'testorder';
        $I->haveInDatabase(
            'oxorder',
            [
                'OXID' => $this->orderId,
                'OXSHOPID' => 1,
                'OXUSERID' => $userId,
                'OXORDERDATE' => '2011-03-30 10:55:13',
                'OXORDERNR' => 1,
                'OXFOLDER' => 'ORDERFOLDER_NEW'
            ]
        );
        $I->haveInDatabase(
            'oxorderarticles',
            [
                'OXID' => $articleId,
                'OXORDERID' => $this->orderId,
                'OXAMOUNT' => 1,
                'OXARTNUM' => $articleId,
                'OXTITLE' => $this->productData['title'],
            ]
        );

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
                'OXARTID' => $this->productData['id'],
                'OXFILENAME' => 'testFile3',
                'OXPURCHASEDONLY' => 1,
                'OXSTOREHASH' => 'e48a1b571bd2d2e60fb2d9b1b76b35d5',
            ]
        );
    }

    public function _after(AcceptanceAdminTester $I): void
    {
        $I->updateConfigInDatabase('blEnableDownloads', "false", 'bool');
        $I->updateConfigInDatabase('iMaxDownloadsCount', "0", 'str');
        $I->updateConfigInDatabase('iLinkExpirationTime', "168", 'str');
        $I->updateConfigInDatabase('iMaxDownloadsCountUnregistered', "1", 'str');
        $I->deleteFromDatabase('oxorder', ['OXID' => $this->orderId]);
        $I->deleteFromDatabase('oxorderarticles', ['OXORDERID' => $this->orderId]);
    }

    public function downloadableFiles(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('Product downloadable files');

        $adminPanel = $I->loginAdmin();

        $this->enableDownloadableFiles($I, $adminPanel);
        $this->setDownloadableFileForAProduct($I, $adminPanel);
        $this->makeOrderComplete($I, $adminPanel);
    }

    private function enableDownloadableFiles(AcceptanceAdminTester $I, AdminPanel $adminPanel): void
    {
        $coreSettings = $adminPanel->openCoreSettings();
        $settingsTab = $coreSettings->openSettingsTab();
        $settingsTab->openDownloadableProducts();
        $I->checkOption('confbools[blEnableDownloads]');
        $I->fillField("confstrs[iMaxDownloadsCount]", "2");
        $I->fillField("confstrs[iLinkExpirationTime]", "240");
        $I->fillField("confstrs[iDownloadExpirationTime]", "24");
        $I->fillField("confstrs[iMaxDownloadsCountUnregistered]", "1");
        $I->click(['name' => 'save']);
    }

    private function setDownloadableFileForAProduct(AcceptanceAdminTester $I, AdminPanel $adminPanel): void
    {
        $products = $adminPanel->openProducts();
        $products->find("where[oxarticles][oxartnum]", $this->productData['id']);
        $products->openDownloadsTab();
        $I->checkOption('editval[oxarticles__oxisdownloadable]');
        $I->click(['name' => 'save']);
    }

    private function makeOrderComplete(AcceptanceAdminTester $I, AdminPanel $adminPanel): void
    {
        $orders = $adminPanel->openOrders();
        $orders->find("where[oxorder][oxordernr]", "1");
        $orders->openDownloadsTab();
        $firstDownloadableProductLocator = "//tr[@id='file.1']";
        $I->assertEquals($this->productData['id'], $I->grabTextFrom("{$firstDownloadableProductLocator}/td[1]"));
        $I->assertEquals($this->productData['title'], $I->grabTextFrom("$firstDownloadableProductLocator/td[2]"));
        $I->assertEquals("testFile3", $I->grabTextFrom("$firstDownloadableProductLocator/td[3]"));
        $I->assertEquals("0000-00-00 00:00:00", $I->grabTextFrom("$firstDownloadableProductLocator/td[4]"));
        $I->assertEquals("0000-00-00 00:00:00", $I->grabTextFrom("$firstDownloadableProductLocator/td[5]"));
        $I->assertEquals("0", $I->grabTextFrom("$firstDownloadableProductLocator/td[6]"));
        $I->assertEquals("2", $I->grabTextFrom("$firstDownloadableProductLocator/td[7]"));
    }
}
