<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Email;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

#[RunTestsInSeparateProcesses]
final class EmailTest extends IntegrationTestCase
{
    use ContainerTrait;

    private LoggerInterface|MockObject $logger;
    private Email|MockObject $email;
    private Order|MockObject $order;

    public function setUp(): void
    {
        parent::setUp();

        $this->getLoggerMock();
        $this->getEmailMock();
        $this->getOrderStub();
        $this->createContainer();
        $this->container->set(LoggerInterface::class, $this->logger);
        $this->container->autowire(LoggerInterface::class, LoggerInterface::class);
    }

    public function testSendOrderEmailToUserWithDefaultConfiguration(): void
    {
        $this->email->expects($this->once())
            ->method('sendMail');
        $this->email->expects($this->once())
            ->method('getRenderer');

        $this->email->sendOrderEmailToUser($this->order);
    }

    public function testSendOrderEmailToOwnerWithDefaultConfiguration(): void
    {
        $this->email->expects($this->once())
            ->method('sendMail');
        $this->email->expects($this->once())
            ->method('getRenderer');

        $this->email->sendOrderEmailToOwner($this->order);
    }

    public function testSendOrderEmailToUserWithDisabledEmails(): void
    {
        $this->setParameter('oxid_esales.email.disable_order_emails', true);
        $this->attachContainerToContainerFactory();

        $this->logger->expects($this->atLeastOnce())
            ->method('notice');
        $this->email->expects($this->never())
            ->method('sendMail');
        $this->email->expects($this->never())
            ->method('getRenderer');

        $return = $this->email->sendOrderEmailToUser($this->order);

        $this->assertTrue($return);
    }

    public function testSendOrderEmailToOwnerWithDisabledEmails(): void
    {
        $this->setParameter('oxid_esales.email.disable_order_emails', true);
        $this->attachContainerToContainerFactory();

        $this->logger->expects($this->atLeastOnce())
            ->method('notice');
        $this->email->expects($this->never())
            ->method('sendMail');
        $this->email->expects($this->never())
            ->method('getRenderer');

        $return = $this->email->sendOrderEmailToOwner($this->order);

        $this->assertTrue($return);
    }

    private function getOrderStub(): void
    {
        $user = new User();
        $user->oxuser__oxfname = new Field('user-first-name');
        $user->oxuser__oxlname = new Field('user-last-name');
        $user->oxuser__oxusername = new Field('test@example.com');
        $user->oxshops__oxorderemail = new Field('test@order.com');

        $this->order = $this->createPartialMock(Order::class, ['getOrderUser']);
        $this->order->oxorder__oxordernr = new Field('order-test-1');
        $this->order->method('getOrderUser')
            ->willReturn($user);
    }

    private function getEmailMock(): void
    {
        $templateRenderer = $this->createMock(TemplateRendererInterface::class);
        $templateRenderer->method('renderTemplate')
            ->willReturn('some-data');
        $this->email = $this->createPartialMock(Email::class, ['sendMail', 'getRenderer']);
        $this->email->method('getRenderer')
            ->willReturn($templateRenderer);
        $this->email->method('sendMail')
            ->willReturn(true);
    }

    private function getLoggerMock(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
    }
}
