<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller;

use OxidEsales\Eshop\Application\Controller\OrderController;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Psr\Log\LoggerInterface;

final class OrderControllerTest extends IntegrationTestCase
{
    use ContainerTrait;

    private string $basketSummaryHashParameter = 'basketSummaryHash';
    private string $userId;
    private LoggerInterface $logger;
    private Basket $basket;

    public function setUp(): void
    {
        parent::setUp();

        $this->prepareUserStub();
        $this->prepareBasketMock();
        unset($_SESSION['Errors']);
    }

    public function testExecuteWithBasketMissingSummaryHashParameterWillLogAnError(): void
    {
        $this->stubSession();

        $this->basket->expects($this->atLeastOnce())
            ->method('getProductsCount');

        $logger = $this->createMock(LoggerInterface::class);
        $this->injectLoggerMockIntoContainer($logger);

        $logger->expects($this->once())
            ->method('warning')
            ->with($this->stringContains($this->basketSummaryHashParameter));

        oxNew(OrderController::class)->execute();
    }

    public function testExecuteWithWrongBasketSummaryHashParameterAndEmptyBasketWillRedirectAndAddError(): void
    {
        $this->stubSession();

        $_GET[$this->basketSummaryHashParameter] = 'some-invalid-hash';
        $this->basket->expects($this->atLeastOnce())
            ->method('getProductsCount')
            ->willReturn(0);

        $redirect = oxNew(OrderController::class)->execute();

        $this->assertEquals('basket', $redirect);
        $this->assertNotEmpty($_SESSION['Errors']);
    }

    public function testExecuteWithWrongBasketSummaryHashParameterAndNonEmptyBasketWillRedirectAndAddError(): void
    {
        $this->stubSession();

        $_GET[$this->basketSummaryHashParameter] = 'some-invalid-hash';
        $this->basket->expects($this->atLeastOnce())
            ->method('getProductsCount')
            ->willReturn(123);

        $redirect = oxNew(OrderController::class)->execute();

        $this->assertEquals('order', $redirect);
        $this->assertNotEmpty($_SESSION['Errors']);
    }

    public function testRenderWillSetSessionChallenge(): void
    {
        Registry::set(Session::class, oxNew(Session::class));

        $this->basket->expects($this->never())
            ->method('getProductsCount')
            ->willReturn(0);

        $orderController = oxNew(OrderController::class);
        $orderController->setIsOrderStep(false);

        $orderController->render();

        $this->assertNotEmpty($_SESSION['sess_challenge']);
    }

    private function prepareUserStub(): void
    {
        Registry::getConfig()->setConfigParam('blEnableIntangibleProdAgreement', false);
        $user = oxNew('oxUser');
        $user->oxuser__oxusername = new Field('some-user-name', Field::T_RAW);
        $user->oxuser__oxpassword = new Field('some-user-pass', Field::T_RAW);
        $user->save();

        $this->userId = $user->getId();
    }

    private function stubSession(): void
    {
        $session = $this->createPartialMock(
            Session::class,
            [
                'checkSessionChallenge',
                'getVariable',
                'getBasket',
            ]
        );
        $session->expects($this->atLeastOnce())
            ->method('checkSessionChallenge')
            ->willReturn(true);
        $session->method('getBasket')
            ->willReturn($this->basket);
        $session->method('getVariable')
            ->willReturnMap(
                [
                    ['login-token', null],
                    ['usr', $this->userId]
                ]
            );
        Registry::set(Session::class, $session);
    }

    private function prepareBasketMock(): void
    {
        $this->basket = $this->createPartialMock(
            Basket::class,
            ['getProductsCount']
        );
    }

    private function injectLoggerMockIntoContainer(LoggerInterface $logger): void
    {
        $this->createContainer();
        $this->useNonVfsProjectConfigurationDirectory();
        $this->container->set(LoggerInterface::class, $logger);
        $this->container->autowire(LoggerInterface::class, LoggerInterface::class);
        $this->attachContainerToContainerFactory();
    }

    private function useNonVfsProjectConfigurationDirectory(): void
    {
        $this->container->get(ContextInterface::class)
            ->setProjectConfigurationDirectory(
                ContainerFacade::get(ContextInterface::class)
                    ->getProjectConfigurationDirectory()
            );
    }
}
