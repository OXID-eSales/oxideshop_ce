<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use Codeception\Util\Fixtures;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin', 'order', 'email')]
final class OrderEmailCest
{
    public function testSendOrderWithAddress(AcceptanceTester $I): void
    {
        $I->wantToTest('Sending email on order shipment');

        $shop = $this->getShopData();
        $order = $this->getOrderData();

        $I->loginAdmin()
            ->openOrders()
            ->findByOrderNumber($order['OXORDERNR'])
            ->shipOrderWithEmail();

        $I->openRecentEmail();

        $I->amGoingTo('Check Email subject');

        $I->seeInEmailSubject($shop['OXSENDEDNOWSUBJECT']);

        $I->amGoingTo('Check Email Sender Receiver');

        $I->seeInEmailTo($order['OXBILLEMAIL']);
        $I->seeInEmailTo($order['OXBILLFNAME'] . ' ' . $order['OXBILLLNAME']);
        $I->seeInEmailFrom($shop['OXOWNEREMAIL']);
        $I->seeInEmailFrom($shop['OXNAME']);

        $I->amGoingTo('Check Email html contents');

        $this->seeAddressElementsInHtmlContent($I, $order);
        $this->seeProductsInHtmlContent($I, $order['PRODUCTS']);

        $I->amGoingTo('Check Email plain contents');

        $this->seeAddressElementsInPlainContent($I, $order);
        $this->seeProductsInPlainContent($I, $order['PRODUCTS']);
    }

    private function seeAddressElementsInHtmlContent(AcceptanceTester $I, array $order): void
    {
        $I->seeInEmailHtmlBody($order['OXBILLADDINFO']);
        $I->seeInEmailHtmlBody($order['OXBILLZIP']);
        $I->seeInEmailHtmlBody($order['OXBILLSTREET']);
        $I->seeInEmailHtmlBody($order['OXBILLSTREETNR']);
        $I->seeInEmailHtmlBody($order['OXBILLCITY']);
        $I->seeInEmailHtmlBody($order['OXREMARK']);
    }

    private function seeProductsInHtmlContent(AcceptanceTester $I, array $products): void
    {
        foreach ($products as $product) {
            $I->seeInEmailHtmlBody($product['OXTITLE']);
        }
    }

    private function seeAddressElementsInPlainContent(AcceptanceTester $I, array $order): void
    {
        $I->seeInEmailPlainBody($order['OXBILLADDINFO']);
        $I->seeInEmailPlainBody($order['OXBILLZIP']);
        $I->seeInEmailPlainBody($order['OXBILLSTREET']);
        $I->seeInEmailPlainBody($order['OXBILLSTREETNR']);
        $I->seeInEmailPlainBody($order['OXBILLCITY']);
        $I->seeInEmailPlainBody($order['OXREMARK']);
    }

    private function seeProductsInPlainContent(AcceptanceTester $I, array $products): void
    {
        foreach ($products as $product) {
            $I->seeInEmailPlainBody($product['OXAMOUNT']);
        }
    }

    private function getOrderData(): array
    {
        return Fixtures::get('testorder');
    }

    private function getShopData(): array
    {
        return Fixtures::get('shop-1');
    }
}
