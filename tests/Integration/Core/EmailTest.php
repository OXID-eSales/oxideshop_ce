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
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mime\Email as SymfonyEmail;

#[RunTestsInSeparateProcesses]
final class EmailTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->createContainer();
        $this->replaceLogger($this->createStub(LoggerInterface::class));
    }

    public function testSendOrderEmailToUserWithDefaultConfiguration(): void
    {
        $email = $this->createEmailMock();
        $order = $this->createOrderStub();

        $email->expects($this->once())
            ->method('sendMail');
        $email->expects($this->once())
            ->method('getRenderer');

        $email->sendOrderEmailToUser($order);
    }

    public function testSendOrderEmailToOwnerWithDefaultConfiguration(): void
    {
        $email = $this->createEmailMock();
        $order = $this->createOrderStub();

        $email->expects($this->once())
            ->method('sendMail');
        $email->expects($this->once())
            ->method('getRenderer');

        $email->sendOrderEmailToOwner($order);
    }

    public function testSendOrderEmailToUserWithDisabledEmails(): void
    {
        $email = $this->createEmailMock();
        $order = $this->createOrderStub();

        $this->setParameter('oxid_esales.email.disable_order_emails', true);
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->atLeastOnce())
            ->method('notice');
        $this->replaceLogger($logger);
        $this->attachContainerToContainerFactory();

        $email->expects($this->never())
            ->method('sendMail');
        $email->expects($this->never())
            ->method('getRenderer');

        $return = $email->sendOrderEmailToUser($order);

        $this->assertTrue($return);
    }

    public function testSendOrderEmailToOwnerWithDisabledEmails(): void
    {
        $email = $this->createEmailMock();
        $order = $this->createOrderStub();

        $this->setParameter('oxid_esales.email.disable_order_emails', true);
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->atLeastOnce())
            ->method('notice');
        $this->replaceLogger($logger);
        $this->attachContainerToContainerFactory();

        $email->expects($this->never())
            ->method('sendMail');
        $email->expects($this->never())
            ->method('getRenderer');

        $return = $email->sendOrderEmailToOwner($order);

        $this->assertTrue($return);
    }

    private function createOrderStub(): Order
    {
        $user = new User();
        $user->oxuser__oxfname = new Field('user-first-name');
        $user->oxuser__oxlname = new Field('user-last-name');
        $user->oxuser__oxusername = new Field('test@example.com');
        $user->oxshops__oxorderemail = new Field('test@order.com');

        $order = $this
            ->getStubBuilder(Order::class)
            ->onlyMethods(['getOrderUser'])
            ->getStub();
        $order->oxorder__oxordernr = new Field('order-test-1');
        $order->method('getOrderUser')
            ->willReturn($user);

        return $order;
    }

    private function createEmailMock(): Email
    {
        $templateRenderer = $this->createStub(TemplateRendererInterface::class);
        $templateRenderer->method('renderTemplate')
            ->willReturn('some-data');
        $email = $this->createPartialMock(Email::class, ['sendMail', 'getRenderer']);
        $email->method('getRenderer')
            ->willReturn($templateRenderer);
        $email->method('sendMail')
            ->willReturn(true);
        return $email;
    }

    public function testSendWithUnconfiguredDsnFallsBackToPhpMailer(): void
    {
        $this->setParameter('oxid_esales.mailing.use_symfony_mailer', true);
        $this->setParameter('oxid_esales.mailing.dsn', null);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Mailer failed'));

        $adapter = $this->createStub(EmailAdapterInterface::class);
        $adapter->method('convertToSymfonyEmail')->willReturn(new SymfonyEmail());

        $this->replaceLogger($logger);
        $this->container->set(EmailAdapterInterface::class, $adapter);
        $this->attachContainerToContainerFactory();

        $email = oxNew(Email::class);
        $email->setRecipient('test@example.com', 'Test User');
        $email->setFrom('shop@example.com', 'Shop');
        $email->setSubject('Test');
        $email->setBody('Body');

        $email->send();
    }

    public function testSendWithValidDsn(): void
    {
        $this->setParameter('oxid_esales.mailing.use_symfony_mailer', true);
        $this->setParameter('oxid_esales.mailing.dsn', 'null://null');

        $symfonyEmail = new SymfonyEmail();
        $adapter = $this->createStub(EmailAdapterInterface::class);
        $adapter->method('convertToSymfonyEmail')->willReturn($symfonyEmail);

        $this->container->set(EmailAdapterInterface::class, $adapter);
        $this->attachContainerToContainerFactory();

        $email = oxNew(Email::class);
        $email->setRecipient('test@example.com', 'Test User');
        $email->setFrom('shop@example.com', 'Shop');
        $email->setSubject('Test');
        $email->setBody('Body');

        $result = $email->send();

        $this->assertTrue($result);
    }

    private function replaceLogger(LoggerInterface $logger): void
    {
        $this->container->set(LoggerInterface::class, $logger);
        $this->container->autowire(LoggerInterface::class, LoggerInterface::class);
    }
}
