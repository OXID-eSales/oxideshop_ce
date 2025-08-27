<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use DateTime;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin', 'order')]
final class OrderTotalsAndCurrencyDisplayCest
{
    public function testOrderTotalsAndCurrencyPosition(AcceptanceTester $I): void
    {
        $I->wantTo('verify order totals and currency display in admin panel');

        $this->prepareTestOrder($I);

        $I->amGoingTo('check order totals with default currency (EUR)');
        $adminPanel = $I->loginAdmin();
        $ordersPage = $adminPanel->openOrders()->findByOrderNumber('123');

        $I->expect('to see correct order counts and sums in EUR');
        $ordersPage->seeOrdersTodayCount('1');
        $ordersPage->seeOrdersTodaySum('20.000,00 EUR');
        $ordersPage->seeTotalOrdersCount('2');
        $ordersPage->seeTotalOrdersSum('20.000,00 EUR');

        $I->amGoingTo('change the currency settings to GBP as default');
        $coreSettings = $adminPanel->openCoreSettings();
        $settingsTab = $coreSettings->openSettingsTab();
        $settingsTab->openAdditionalSettings();

        $I->fillField(
            'confarrs[aCurrencies]',
            "GBP@ 0.8565@ .@  @ £@ 2\nCHF@ 1.4326@ ,@ .@ CHF@ 2\nUSD@ 1.2994@ .@  @ $@ 2\nEUR@ 1.00@ ,@ .@ €@ 2"
        );

        $settingsTab->save();

        $I->amGoingTo('check order totals after currency change');
        $ordersPage = $adminPanel->openOrders()->findByOrderNumber('123');

        $I->expect('to see correct order counts and sums in GBP');
        $ordersPage->seeOrdersTodayCount('1');
        $ordersPage->seeOrdersTodaySum('17130.00 GBP');
        $ordersPage->seeTotalOrdersCount('2');
        $ordersPage->seeTotalOrdersSum('17130.00 GBP');
    }

    private function prepareTestOrder(AcceptanceTester $I): void
    {
        $I->haveInDatabase('oxorder', [
            'OXID' => 'testorder2',
            'OXSHOPID' => 1,
            'OXUSERID' => 'testuser',
            'OXORDERDATE' => (new DateTime())->format('Y-m-d H:i:s'),
            'OXORDERNR' => 123,
            'OXBILLEMAIL' => 'test@billemail.com',
            'OXBILLFNAME' => 'test bill fname',
            'OXBILLLNAME' => 'test bill lname',
            'OXBILLSTREET' => 'test address street',
            'OXBILLSTREETNR' => '123',
            'OXBILLCITY' => 'test address city',
            'OXBILLCOUNTRYID' => 'testcountry_de',
            'OXBILLZIP' => '55555',
            'OXFOLDER' => 'ORDERFOLDER_NEW',
            'OXTOTALNETSUM' => 18398.26,
            'OXTOTALBRUTSUM' => 20000.00,
            'OXTOTALORDERSUM' => 20000.00,
            'OXCURRENCY' => 'EUR',
            'OXCURRATE' => 1.00
        ]);

        $I->haveInDatabase('oxorderarticles', [
            'OXID' => 'testorderarticle1',
            'OXORDERID' => 'testorder2',
            'OXAMOUNT' => 1,
            'OXARTID' => '1000',
            'OXARTNUM' => '1000',
            'OXTITLE' => 'Test product 1',
            'OXBRUTPRICE' => 5000.00,
            'OXORDERSHOPID' => 1
        ]);

        $I->haveInDatabase('oxorderarticles', [
            'OXID' => 'testorderarticle2',
            'OXORDERID' => 'testorder2',
            'OXAMOUNT' => 1,
            'OXARTID' => '1001',
            'OXARTNUM' => '1001',
            'OXTITLE' => 'Test product 2',
            'OXBRUTPRICE' => 15000.00,
            'OXORDERSHOPID' => 1
        ]);
    }
}
