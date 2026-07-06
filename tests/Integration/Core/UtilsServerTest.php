<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\UtilsServer;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class UtilsServerTest extends TestCase
{
    use ContainerTrait;
    use ProphecyTrait;

    private array $serverBackup;
    private array $cookieBackup;

    public function setUp(): void
    {
        parent::setUp();
        $this->serverBackup = $_SERVER;
        $this->cookieBackup = $_COOKIE;
    }

    public function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
        $_COOKIE = $this->cookieBackup;
        parent::tearDown();
    }

    public function testGetServerVarReturnsNamedValue(): void
    {
        $_SERVER['SOME_SERVER_VAR'] = 'some-value';

        $this->assertSame('some-value', oxNew(UtilsServer::class)->getServerVar('SOME_SERVER_VAR'));
    }

    public function testGetServerVarReturnsNullForMissingName(): void
    {
        $this->assertNull(oxNew(UtilsServer::class)->getServerVar('MISSING_SERVER_VAR'));
    }

    public function testGetServerVarWithoutNameReturnsWholeServerArray(): void
    {
        $this->assertSame($_SERVER, oxNew(UtilsServer::class)->getServerVar());
    }

    public function testGetOxCookieSanitizesNamedValue(): void
    {
        $_COOKIE['some-cookie'] = 'a<b&c';

        $this->assertSame('a&lt;b&amp;c', oxNew(UtilsServer::class)->getOxCookie('some-cookie'));
    }

    public function testGetOxCookieReturnsNullForMissingName(): void
    {
        $this->assertNull(oxNew(UtilsServer::class)->getOxCookie('missing-cookie'));
    }

    public function testGetOxCookieFallsBackToSessionCookies(): void
    {
        $utilsServer = new class extends \OxidEsales\EshopCommunity\Core\UtilsServer {
            public function addSessionCookie(string $name, string $value): void
            {
                $this->_sSessionCookies[$name] = $value;
            }
        };
        $utilsServer->addSessionCookie('session-only', 'session-value');

        $this->assertSame('session-value', $utilsServer->getOxCookie('session-only'));
    }

    public function testGetOxCookieWithoutNameReturnsWholeCookieArrayUnsanitized(): void
    {
        $_COOKIE['raw-cookie'] = 'a<b&c';

        $wholeCookieArray = oxNew(UtilsServer::class)->getOxCookie();

        $this->assertSame('a<b&c', $wholeCookieArray['raw-cookie']);
    }

    public function testSetUserCookieWillCallPasswordHashing(): void
    {
        $passwordService = $this->prophesize(PasswordServiceBridgeInterface::class);
        $password = 'some-password';
        $passwordService
            ->hash(Argument::containingString($password))
            ->willReturn('some-hash');
        $this->createContainer();
        $this->replaceService(PasswordServiceBridgeInterface::class, $passwordService->reveal());
        $this->replaceContainerInstance();

        $utilsServer = oxNew(UtilsServer::class);
        $utilsServer->setUserCookie('some-user-name', $password);

        $passwordService->hash($password . User::USER_COOKIE_SALT)->shouldHaveBeenCalledOnce();
    }

    public function testIsTrustedServerWithDefaultConfig(): void
    {
        $isTrusted = oxNew(UtilsServer::class)->isTrustedClientIp();

        $this->assertFalse($isTrusted);
    }

    public function testIsTrustedServerWithConfiguredIp(): void
    {
        $someIp = '255.255.255.255';
        $_SERVER['REMOTE_ADDR'] = $someIp;

        $this->setParameter('oxid_esales.trusted_ips', [$someIp]);

        $isTrusted = oxNew(UtilsServer::class)->isTrustedClientIp();

        $this->assertTrue($isTrusted);
    }

    public function testIsTrustedServerWithNonTrustedIp(): void
    {
        $someIp = '255.255.255.255';
        $_SERVER['REMOTE_ADDR'] = $someIp;
        $this->setParameter('oxid_esales.trusted_ips', ['1.2.3.4', '5.6.7.8']);

        $isTrusted = oxNew(UtilsServer::class)->isTrustedClientIp();

        $this->assertFalse($isTrusted);
    }

    public function testQueuedCookieIsAppliedToOutgoingResponseOnKernelResponse(): void
    {
        $queueResponseCookie = new \ReflectionMethod(
            \OxidEsales\EshopCommunity\Core\UtilsServer::class,
            'queueResponseCookie'
        );
        $queueResponseCookie->invoke(oxNew(UtilsServer::class), Cookie::create('some-cookie', 'some-value'));

        $response = new Response();
        $this->get(EventDispatcherInterface::class)->dispatch(
            new ResponseEvent(
                $this->createStub(HttpKernelInterface::class),
                new Request(),
                HttpKernelInterface::MAIN_REQUEST,
                $response
            ),
            KernelEvents::RESPONSE
        );

        $cookies = $response->headers->getCookies();
        $this->assertCount(1, $cookies);
        $this->assertSame('some-cookie', $cookies[0]->getName());
        $this->assertSame('some-value', $cookies[0]->getValue());
    }
}
