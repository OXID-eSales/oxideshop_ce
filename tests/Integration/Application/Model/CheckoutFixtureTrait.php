<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsDate;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Price\BasketConstruct;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

trait CheckoutFixtureTrait
{
    private function removeCheckoutFixtures(): void
    {
        $database = DatabaseProvider::getDb();
        foreach (['oxarticles', 'oxdiscount', 'oxdelivery', 'oxdel2delset', 'oxdeliveryset', 'oxuser'] as $table) {
            $database->execute("DELETE FROM $table WHERE 1");
        }
    }

    private function createCheckoutBasket(array $voucherSeries = []): Basket
    {
        $payment = oxNew(Payment::class);
        $payment->setId('test_payment');
        $payment->assign([
            'oxactive' => 1,
            'oxdesc' => 'test_payment',
            'oxaddsum' => 0,
            'oxaddsumtype' => 'abs',
            'oxfromamount' => 0,
            'oxtoamount' => 1000000,
            'oxchecked' => 1,
        ]);
        $payment->save();

        $basket = (new BasketConstruct())->calculateBasket([
            'options' => ['config' => ['bl_showVouchers' => true]],
            'articles' => [[
                'oxid' => 'checkout-article',
                'oxtitle' => 'checkout article',
                'oxprice' => 10,
                'oxvat' => 19,
                'oxstock' => 999,
                'amount' => 2,
            ]],
            'costs' => [
                'delivery' => [[
                    'oxactive' => 1,
                    'oxaddsum' => 5,
                    'oxaddsumtype' => 'abs',
                    'oxdeltype' => 'p',
                    'oxfinalize' => 1,
                    'oxparamend' => 99999,
                ]],
                'voucherserie' => $voucherSeries,
            ],
        ]);
        $basket->setPayment('test_payment');
        $basket->calculateBasket(true);
        $_POST['sDeliveryAddressMD5'] = $basket->getBasketUser()->getEncodedDeliveryAddress();

        return $basket;
    }

    private function startCheckout(): string
    {
        $orderId = Registry::getUtilsObject()->generateUId();
        Registry::getSession()->setVariable('sess_challenge', $orderId);

        return $orderId;
    }

    private function listen(string $eventClass, callable $listener): void
    {
        ContainerFacade::get(EventDispatcherInterface::class)->addListener($eventClass, $listener);
    }

    private function getStoredOrderField(string $orderId, string $field): string
    {
        return (string) DatabaseProvider::getDb()->getOne("SELECT $field FROM oxorder WHERE oxid = ?", [$orderId]);
    }

    private function countRows(string $table, string $condition, array $parameters): int
    {
        return (int) DatabaseProvider::getDb()->getOne("SELECT COUNT(*) FROM $table WHERE $condition", $parameters);
    }

    private function getOrderCounter(): int
    {
        return (int) DatabaseProvider::getDb()->getOne("SELECT oxcount FROM oxcounters WHERE oxident = 'oxOrder'");
    }

    private function getArticleStock(): string
    {
        return (string) DatabaseProvider::getDb()->getOne(
            "SELECT oxstock FROM oxarticles WHERE oxid = 'checkout-article'"
        );
    }

    private function createCheckoutBasketWithVoucher(): Basket
    {
        return $this->createCheckoutBasket([[
            'oxdiscount' => 5,
            'oxdiscounttype' => 'absolute',
            'oxallowsameseries' => 1,
            'oxallowotherseries' => 1,
            'oxallowuseanother' => 1,
            'voucher_count' => 1,
        ]]);
    }

    private function getStoredVoucherField(string $voucherId, string $field): string
    {
        return (string) DatabaseProvider::getDb()->getOne("SELECT $field FROM oxvouchers WHERE oxid = ?", [$voucherId]);
    }

    private function storeOrderField(string $orderId, string $field, string $value): void
    {
        DatabaseProvider::getDb()->execute("UPDATE oxorder SET $field = ? WHERE oxid = ?", [$value, $orderId]);
    }

    private function freezeShopTime(int $time): void
    {
        $utilsDate = new class extends UtilsDate {
            public int $time = 0;

            public function getTime()
            {
                return $this->time;
            }
        };
        $utilsDate->time = $time;
        Registry::set(UtilsDate::class, $utilsDate);
    }
}
