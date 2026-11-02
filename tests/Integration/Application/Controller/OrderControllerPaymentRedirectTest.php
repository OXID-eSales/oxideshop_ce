<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller;

use OxidEsales\Eshop\Application\Controller\OrderController;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Utils;
use OxidEsales\EshopCommunity\Internal\Domain\Order\Transition\Event\BeforeOrderFinalizeEvent;
use OxidEsales\EshopCommunity\Tests\Integration\Application\Model\CheckoutFixtureTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
final class OrderControllerPaymentRedirectTest extends IntegrationTestCase
{
    use CheckoutFixtureTrait;

    public function testExecuteRedirectsToThePaymentProvider(): void
    {
        $this->removeCheckoutFixtures();
        $basket = $this->createCheckoutBasket();
        $orderId = $this->startCheckout();
        Registry::getSession()->setBasket($basket);
        Registry::getSession()->setVariable('sess_stoken', 'checkouttoken');
        $_POST['stoken'] = 'checkouttoken';
        $_POST['ord_agb'] = 1;
        Registry::getConfig()->setConfigParam('blEnableIntangibleProdAgreement', false);
        $this->listen(BeforeOrderFinalizeEvent::class, static function (BeforeOrderFinalizeEvent $event): void {
            $event->setRedirectUrl('https://provider.example/start');
        });
        $utils = $this->recordRedirects();
        $controller = oxNew(OrderController::class);
        $controller->setUser($basket->getBasketUser());

        $nextStep = $controller->execute();

        $this->assertSame(
            [null, ['https://provider.example/start'], 0],
            [$nextStep, $utils->redirects, $this->countRows('oxorder', 'oxid = ?', [$orderId])]
        );
    }

    private function recordRedirects(): Utils
    {
        $utils = new class extends Utils {
            /** @var string[] */
            public array $redirects = [];

            public function redirect($sUrl, $blAddRedirectParam = true, $iHeaderCode = 302)
            {
                $this->redirects[] = $sUrl;
            }
        };
        Registry::set(Utils::class, $utils);

        return $utils;
    }
}
