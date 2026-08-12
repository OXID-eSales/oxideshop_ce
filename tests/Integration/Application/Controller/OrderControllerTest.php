<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller;

use OxidEsales\Eshop\Application\Controller\OrderController;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
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
        unset($_SESSION['Errors']);
    }

    public function testExecuteWithWrongBasketSummaryHashParameterAndEmptyBasketWillRedirectAndAddError(): void
    {
        $this->prepareBasketMock();
        $this->stubSession();
        $_GET[$this->basketSummaryHashParameter] = 'some-invalid-hash';
        $this->basket->expects($this->once())->method('getProductsCount')->willReturn(0);

        $redirect = oxNew(OrderController::class)->execute();

        $this->assertEquals('basket', $redirect);
        $this->assertNotEmpty($_SESSION['Errors']);
    }

    public function testExecuteWithWrongBasketSummaryHashParameterAndNonEmptyBasketWillRedirectAndAddError(): void
    {
        $this->prepareBasketMock();
        $this->stubSession();
        $_GET[$this->basketSummaryHashParameter] = 'some-invalid-hash';
        $this->basket->expects($this->once())->method('getProductsCount')->willReturn(123);

        $redirect = oxNew(OrderController::class)->execute();

        $this->assertEquals('order', $redirect);
        $this->assertNotEmpty($_SESSION['Errors']);
    }

    public function testRenderWillSetSessionChallenge(): void
    {
        $orderController = oxNew(OrderController::class);
        $orderController->setIsOrderStep(false);

        $orderController->render();

        $this->assertNotEmpty($_SESSION['sess_challenge']);
    }

    private function prepareUserStub(): void
    {
        Registry::getConfig()->setConfigParam('blEnableIntangibleProdAgreement', false);
        $user = oxNew(User::class);
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
        $session->method('checkSessionChallenge')
            ->willReturn(true);
        $session->expects($this->atLeastOnce())->method('getBasket')
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
}
