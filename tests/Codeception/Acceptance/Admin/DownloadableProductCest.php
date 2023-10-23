<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use Codeception\Util\Fixtures;
use DateTime;
use OxidEsales\Codeception\Admin\AdminPanel;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin')]
final class DownloadableProductCest
{
    private string $orderNo;
    private string $productId;
    private string $productTitle;

    public function _before(AcceptanceTester $I): void
    {
        $order = Fixtures::get('testorder');
        $orderId = $order['OXID'];
        $this->orderNo = $order['OXORDERNR'];
        $orderProductData = $order['PRODUCTS'][0];
        $this->productId = $orderProductData['OXARTID'];
        $this->productTitle = $orderProductData['OXTITLE'];

        $I->updateInDatabase('oxarticles', ['oxisdownloadable' => 1], ['oxartnum' => $this->productId]);

        $I->haveInDatabase(
            'oxorderfiles',
            [
                'OXID' => 'testdownloadProductCest',
                'OXORDERID' => $orderId,
                'OXFILENAME' => 'testFile3',
                'OXFILEID' => '1000l',
                'OXSHOPID' => 1,
                'OXORDERARTICLEID' => $orderProductData['OXID'],
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
                'OXARTID' => $this->productId,
                'OXFILENAME' => 'testFile3',
                'OXPURCHASEDONLY' => 1,
                'OXSTOREHASH' => 'e48a1b571bd2d2e60fb2d9b1b76b35d5',
            ]
        );
    }

    public function _after(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabase('blEnableDownloads', "false", 'bool');
        $I->updateConfigInDatabase('iMaxDownloadsCount', "0", 'str');
        $I->updateConfigInDatabase('iLinkExpirationTime', "168", 'str');
        $I->updateConfigInDatabase('iMaxDownloadsCountUnregistered', "1", 'str');
    }

    public function downloadableFiles(AcceptanceTester $I): void
    {
        $I->wantToTest('Product downloadable files');

        $adminPanel = $I->loginAdmin();

        $this->enableDownloadableFiles($I, $adminPanel);
        $this->setDownloadableFileForAProduct($I, $adminPanel);
        $this->makeOrderComplete($I, $adminPanel);
    }

    private function enableDownloadableFiles(AcceptanceTester $I, AdminPanel $adminPanel): void
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

    private function setDownloadableFileForAProduct(AcceptanceTester $I, AdminPanel $adminPanel): void
    {
        $products = $adminPanel->openProducts();
        $products->find("where[oxarticles][oxartnum]", $this->productId);
        $products->openDownloadsTab();
        $I->checkOption('editval[oxarticles__oxisdownloadable]');
        $I->click(['name' => 'save']);
    }

    private function makeOrderComplete(AcceptanceTester $I, AdminPanel $adminPanel): void
    {
        $orders = $adminPanel->openOrders();
        $orders->findByOrderNumber($this->orderNo);
        $orders->openDownloadsTab();
        $firstDownloadableProductLocator = "//tr[@id='file.1']";

        $I->assertEquals($this->productId, $I->grabTextFrom("{$firstDownloadableProductLocator}/td[1]"));
        $I->assertEquals($this->productTitle, $I->grabTextFrom("$firstDownloadableProductLocator/td[2]"));
        $I->assertEquals("testFile3", $I->grabTextFrom("$firstDownloadableProductLocator/td[3]"));
        $I->assertEquals("0000-00-00 00:00:00", $I->grabTextFrom("$firstDownloadableProductLocator/td[4]"));
        $I->assertEquals("0000-00-00 00:00:00", $I->grabTextFrom("$firstDownloadableProductLocator/td[5]"));
        $I->assertEquals("0", $I->grabTextFrom("$firstDownloadableProductLocator/td[6]"));
        $I->assertEquals("2", $I->grabTextFrom("$firstDownloadableProductLocator/td[7]"));
    }
}
