<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\CodeceptionAdmin;

use OxidEsales\Codeception\Admin\AdminPanel;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

final class AdminDownloadableProductCest
{
    /** @param AcceptanceAdminTester $I */
    public function downloadableFiles(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('Product downloadable files');

        $adminPanel = $I->loginAdmin();

        $this->enableDownloadableFiles($I, $adminPanel);
        $this->setDownloadableFileForAProduct($I, $adminPanel);
        $this->makeOrderComplete($I, $adminPanel);
    }

    /**
     * @param AcceptanceAdminTester $I
     * @param AdminPanel            $adminPanel
     */
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

    /**
     * @param AcceptanceAdminTester $I
     * @param AdminPanel            $adminPanel
     */
    private function setDownloadableFileForAProduct(AcceptanceAdminTester $I, AdminPanel $adminPanel): void
    {
        $products = $adminPanel->openProducts();
        $products->find("where[oxarticles][oxartnum]", "1002");
        $products->openDownloadsTab();
        $I->checkOption('editval[oxarticles__oxisdownloadable]');
        $I->click(['name' => 'save']);
    }

    /**
     * @param AcceptanceAdminTester $I
     * @param AdminPanel            $adminPanel
     */
    private function makeOrderComplete(AcceptanceAdminTester $I, AdminPanel $adminPanel): void
    {
        $orders = $adminPanel->openOrders();
        $orders->find("where[oxorder][oxordernr]", "3");
        $orders->openDownloadsTab();
        $firstDownloadableProductLocator = "//tr[@id='file.1']";
        $I->assertEquals("1002-1", $I->grabTextFrom("{$firstDownloadableProductLocator}/td[1]"));
        $I->assertEquals("Test product 2 [EN] šÄßüл", $I->grabTextFrom("$firstDownloadableProductLocator/td[2]"));
        $I->assertEquals("testFile3", $I->grabTextFrom("$firstDownloadableProductLocator/td[3]"));
        $I->assertEquals("0000-00-00 00:00:00", $I->grabTextFrom("$firstDownloadableProductLocator/td[4]"));
        $I->assertEquals("0000-00-00 00:00:00", $I->grabTextFrom("$firstDownloadableProductLocator/td[5]"));
        $I->assertEquals("0", $I->grabTextFrom("$firstDownloadableProductLocator/td[6]"));
        $I->assertEquals("2", $I->grabTextFrom("$firstDownloadableProductLocator/td[7]"));
    }
}
