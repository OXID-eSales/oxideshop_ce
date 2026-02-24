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
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Email\EmailAdapterInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mime\Email as SymfonyEmail;

final class EmailTest extends IntegrationTestCase
{
    use ContainerTrait;

    private LoggerInterface|MockObject $logger;
    private Email|MockObject $email;
    private Order|MockObject $order;

    public function testSendOrderEmailToUserWithDefaultConfiguration(): void
    {
        $this->getEmailMock();
        $this->getOrderStub(true);
        $this->email->expects($this->once())
            ->method('sendMail');
        $this->email->expects($this->once())
            ->method('getRenderer');

        $this->email->sendOrderEmailToUser($this->order);
    }

    public function testSendOrderEmailToOwnerWithDefaultConfiguration(): void
    {
        $this->getEmailMock();
        $this->getOrderStub(true);
        $this->email->expects($this->once())
            ->method('sendMail');
        $this->email->expects($this->once())
            ->method('getRenderer');

        $this->email->sendOrderEmailToOwner($this->order);
    }

    public function testSendOrderEmailToUserWithDisabledEmails(): void
    {
        $this->getEmailMock();
        $this->getOrderStub(false);
        $this->setParameter('oxid_esales.email.disable_order_emails', true);

        $this->email->expects($this->never())
            ->method('sendMail');
        $this->email->expects($this->never())
            ->method('getRenderer');

        $return = $this->email->sendOrderEmailToUser($this->order);

        $this->assertTrue($return);
    }

    public function testSendOrderEmailToOwnerWithDisabledEmails(): void
    {
        $this->getEmailMock();
        $this->getOrderStub(false);
        $this->setParameter('oxid_esales.email.disable_order_emails', true);

        $this->email->expects($this->never())
            ->method('sendMail');
        $this->email->expects($this->never())
            ->method('getRenderer');

        $return = $this->email->sendOrderEmailToOwner($this->order);

        $this->assertTrue($return);
    }

    public function testSendWithUnconfiguredDsnFallsBackToPhpMailer(): void
    {
        $this->createContainer();

        $this->container->setParameter('oxid_esales.mailing.use_symfony_mailer', true);
        $this->container->setParameter('oxid_esales.mailing.dsn', null);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Mailer failed'));

        $adapter = $this->createStub(EmailAdapterInterface::class);
        $adapter->method('convertToSymfonyEmail')->willReturn(new SymfonyEmail());

        $this->replaceService(LoggerInterface::class, $logger);
        $this->replaceService(EmailAdapterInterface::class, $adapter);
        $this->compileContainer();
        $this->replaceContainerInstance();

        $email = oxNew(Email::class);
        $email->setRecipient('test@example.com', 'Test User');
        $email->setFrom('shop@example.com', 'Shop');
        $email->setSubject('Test');
        $email->setBody('Body');

        $email->send();
    }

    public function testSendWithValidDsn(): void
    {
        $this->createContainer();

        $this->container->setParameter('oxid_esales.mailing.use_symfony_mailer', true);
        $this->container->setParameter('oxid_esales.mailing.dsn', 'null://null');

        $symfonyEmail = new SymfonyEmail();
        $adapter = $this->createStub(EmailAdapterInterface::class);
        $adapter->method('convertToSymfonyEmail')->willReturn($symfonyEmail);

        $this->replaceService(EmailAdapterInterface::class, $adapter);
        $this->compileContainer();
        $this->replaceContainerInstance();

        $email = oxNew(Email::class);
        $email->setRecipient('test@example.com', 'Test User');
        $email->setFrom('shop@example.com', 'Shop');
        $email->setSubject('Test');
        $email->setBody('Body');

        $result = $email->send();

        $this->assertTrue($result);
    }

    private function getOrderStub(bool $expectsOrderUser): void
    {
        $user = new User();
        $user->oxuser__oxfname = new Field('user-first-name');
        $user->oxuser__oxlname = new Field('user-last-name');
        $user->oxuser__oxusername = new Field('test@example.com');
        $user->oxshops__oxorderemail = new Field('test@order.com');

        $this->order = $this->createPartialMock(Order::class, ['getOrderUser']);
        $this->order->oxorder__oxordernr = new Field('order-test-1');
        $orderUserExpectation = $expectsOrderUser ? $this->atLeastOnce() : $this->never();
        $this->order->expects($orderUserExpectation)->method('getOrderUser')
            ->willReturn($user);
    }

    private function getEmailMock(): void
    {
        $templateRenderer = $this->createStub(TemplateRendererInterface::class);
        $templateRenderer->method('renderTemplate')
            ->willReturn('some-data');
        $this->email = $this->createPartialMock(Email::class, ['sendMail', 'getRenderer']);
        $this->email->method('getRenderer')
            ->willReturn($templateRenderer);
        $this->email->method('sendMail')
            ->willReturn(true);
    }
}
