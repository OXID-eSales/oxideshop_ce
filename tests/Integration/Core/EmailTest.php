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
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\BeforeEmailSendEvent;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as SymfonyEmail;

final class EmailTest extends IntegrationTestCase
{
    use ContainerTrait;

    private LoggerInterface|MockObject $logger;
    private Email|MockObject $email;
    private Order|MockObject $order;
    private int $dispatchedEvents = 0;

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

    public function testSendDeliversBodiesChangedByListener(): void
    {
        $this->useSymfonyMailer(false);
        $email = $this->createEmailSentByPhpMailer();

        $email->send();

        $this->assertSame('<b>Body</b> changed', $email->getBody());
        $this->assertSame('Alt body changed', $email->getAltBody());
    }

    public function testSendDispatchesEventOnceWhenFallingBackToPhpMailer(): void
    {
        $this->useSymfonyMailer(true);

        $this->createEmailSentByPhpMailer()->send();

        $this->assertSame(1, $this->dispatchedEvents);
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

    private function useSymfonyMailer(bool $useSymfonyMailer): void
    {
        $failingMailer = $this->createStub(MailerInterface::class);
        $failingMailer->method('send')->willThrowException(new TransportException());

        $this->createContainer();
        $this->container->setParameter('oxid_esales.mailing.use_symfony_mailer', $useSymfonyMailer);
        $this->replaceService(LoggerInterface::class, new NullLogger());
        $this->replaceService(MailerInterface::class, $failingMailer);
        $this->compileContainer();
        $this->replaceContainerInstance();

        $this->get(EventDispatcherInterface::class)->addListener(
            BeforeEmailSendEvent::class,
            function (BeforeEmailSendEvent $event): void {
                $this->dispatchedEvents++;
                $email = $event->getEmail();
                $email->setBody($email->getBody() . ' changed');
                $email->setAltBody($email->getAltBody() . ' changed');
            }
        );
    }

    private function createEmailSentByPhpMailer(): Email
    {
        $email = $this->createPartialMock(Email::class, ['sendMail']);
        $email->expects($this->once())->method('sendMail')->willReturn(true);
        $email->isHTML(true);
        $email->setRecipient('customer@example.com', 'Customer');
        $email->setFrom('shop@example.com', 'Shop');
        $email->setSubject('Subject');
        $email->setBody('<b>Body</b>');
        $email->setAltBody('Alt body');

        return $email;
    }
}
