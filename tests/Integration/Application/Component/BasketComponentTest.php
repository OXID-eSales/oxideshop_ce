<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Component;

use OxidEsales\Eshop\Application\Component\BasketComponent;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\BasketReservation;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\Eshop\Core\Utils;
use OxidEsales\Eshop\Core\UtilsView;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class BasketComponentTest extends IntegrationTestCase
{
    use ProphecyTrait;
    use ContainerTrait;

    public function testFrontendErrorMsgInToBasket(): void
    {
        $utilsView = $this->createMock(UtilsView::class);
        $utilsView->expects($this->once())
            ->method('addErrorToDisplay')
            ->with('ERROR_MESSAGE_NON_MATCHING_CSRF_TOKEN');

        Registry::set(
            UtilsView::class,
            $utilsView
        );

        $this->actAsSearchEngine(false);
        $this->prepareSessionMock();

        oxNew(BasketComponent::class)->toBasket(1000, 2);
    }

    public function testInitWitDefaultBasketReservationsCleanupRate(): void
    {
        $defaultCleanupRate = 200;
        Registry::getConfig()->reinitialize();
        Registry::getConfig()->setConfigParam('blPsBasketReservationEnabled', true);
        $basketReservations = $this->prophesize(BasketReservation::class);
        $session = $this->prophesize(Session::class);
        $session->getBasketReservations()
            ->willReturn($basketReservations);
        $session->getBasket()
            ->willReturn(new Basket());
        Registry::set(Session::class, $session->reveal());

        oxNew(BasketComponent::class)->init();

        $basketReservations->discardUnusedReservations($defaultCleanupRate)
            ->shouldHaveBeenCalledOnce();
    }

    public function testInitWitModifiedBasketReservationsCleanupRate(): void
    {
        $cleanupRate = 123;
        $this->setParameter('oxid_basket_reservation_cleanup_rate', $cleanupRate);
        $this->attachContainerToContainerFactory();
        Registry::getConfig()->reinitialize();
        Registry::getConfig()->setConfigParam('blPsBasketReservationEnabled', true);
        $basketReservations = $this->prophesize(BasketReservation::class);
        $session = $this->prophesize(Session::class);
        $session->getBasketReservations()
            ->willReturn($basketReservations);
        $session->getBasket()
            ->willReturn(new Basket());
        Registry::set(Session::class, $session->reveal());

        oxNew(BasketComponent::class)->init();

        $basketReservations->discardUnusedReservations($cleanupRate)
            ->shouldHaveBeenCalledOnce();
    }

    private function prepareSessionMock(): void
    {
        $basket = oxNew(Basket::class);
        $basket->setStockCheckMode(false);
        $session = $this->createConfiguredMock(
            Session::class,
            [
                'getId' => 'random-string',
                'isActualSidInCookie' => true,
                'checkSessionChallenge' => false,
                'getBasket' => $basket
            ]
        );

        Registry::set(
            Session::class,
            $session
        );
    }

    private function actAsSearchEngine(bool $isSearchEngine): void
    {
        Registry::set(
            Utils::class,
            $this->createConfiguredMock(
                Utils::class,
                ['isSearchEngine' => $isSearchEngine]
            )
        );
    }
}
