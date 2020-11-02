<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\CodeceptionAdmin;

use DateTime;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

final class AdminNotRegisteredUserOrderCest
{
    /** @param AcceptanceAdminTester $I */
    public function _before(AcceptanceAdminTester $I)
    {
        $this->insertAnOrderInDatabase($I);
    }

    /** @param AcceptanceAdminTester $I */
    public function checkEditingNotRegisteredUserOrder(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('Check editing not registered user order');

        $adminPanel = $I->loginAdmin();

        $orders = $adminPanel->openOrders();
        $orders = $orders->find($orders->orderNumberInput, "2");

        $addressesTab = $orders->openAddressesTab();
        $I->seeInField($addressesTab->firstNameInAddressesTab, "name");
        $I->seeInField($addressesTab->lastNameInAddressesTab, "surname");
        $I->seeInField($addressesTab->loginNameInAddressesTab, "example01@oxid-esales.dev");
        $I->seeInField($addressesTab->zipCodeInAddressesTab, "3000");
        $I->seeInField($addressesTab->cityInAddressesTab, "city");

        $productsTab = $orders->openProductsTab();
        $productsTab = $productsTab->addANewProductToTheOrder("1002-1");

        $I->waitForElement($productsTab->secondProductInProductTab);
        $I->see("1002-1", $productsTab->secondProductInProductTab);
    }

    /** @param AcceptanceAdminTester $I */
    private function insertAnOrderInDatabase(AcceptanceAdminTester $I): void
    {
        $I->haveInDatabase(
            'oxorder',
            [
                'OXID' => 'NotRegisteredOrderId',
                'OXSHOPID' => 1,
                'OXUSERID' => 'NotRegisteredUserId',
                'OXORDERDATE' => (new DateTime())->format('Y-m-d 00:00:00'),
                'OXORDERNR' => 2,
                'OXBILLEMAIL' => 'example01@oxid-esales.dev',
                'OXBILLFNAME' => 'name',
                'OXBILLLNAME' => 'surname',
                'OXBILLSTREET' => 'street',
                'OXBILLSTREETNR' => '1',
                'OXBILLCITY' => 'city',
                'OXBILLCOUNTRYID' => 'a7c40f631fc920687.20179984',
                'OXBILLSTATEID' => 'BB',
                'OXBILLZIP' => '3000',
                'OXPAYMENTID' => 'NotRegisteredPaymentId',
                'OXPAYMENTTYPE' => 'oxidcashondel',
                'OXREMARK' => 'remark text',
                'OXTRANSSTATUS' => 'OK',
                'OXFOLDER' => 'ORDERFOLDER_NEW',
                'OXDELTYPE' => 'oxidstandard',
                'OXTIMESTAMP' => (new DateTime())->format('Y-m-d 00:00:00')
            ]
        );

        $I->haveInDatabase(
            'oxorderarticles',
            [
                'OXID' => 'NotRegisteredOrderArticlesId',
                'OXORDERID' => 'NotRegisteredOrderId',
                'OXAMOUNT' => 1,
                'OXARTID' => '1002-1',
                'OXARTNUM' => '1002-1',
                'OXTITLE' => 'Test product 2 [EN] šÄßüл',
                'OXSHORTDESC' => 'Test product 2 short desc [EN] šÄßüл',
                'OXSELVARIANT' => 'var1 [EN] šÄßüл',
                'OXNETPRICE' => 46.22,
                'OXBRUTPRICE' => 55,
                'OXVATPRICE' => 8.78,
                'OXVAT' => 19,
                'OXSTOCK' => 5,
                'OXINSERT' => '2008-02-04',
                'OXTIMESTAMP' => (new DateTime())->format('Y-m-d 00:00:00'),
                'OXSEARCHKEYS' => 'šÄßüл1002',
                'OXISSEARCH' => 1,
                'OXORDERSHOPID' => 1
            ]
        );
    }
}
