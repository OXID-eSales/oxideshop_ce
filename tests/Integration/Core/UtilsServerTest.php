<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsServer;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class UtilsServerTest extends IntegrationTestCase
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
        $this->setParameter('oxid_esales.trusted_ips', [$someIp]);
        $this->get(Request::class)->server->set('REMOTE_ADDR', $someIp);

        $isTrusted = oxNew(UtilsServer::class)->isTrustedClientIp();

        $this->assertTrue($isTrusted);
    }

    public function testIsTrustedServerWithNonTrustedIp(): void
    {
        $someIp = '255.255.255.255';
        $this->setParameter('oxid_esales.trusted_ips', ['1.2.3.4', '5.6.7.8']);
        $this->get(Request::class)->server->set('REMOTE_ADDR', $someIp);

        $isTrusted = oxNew(UtilsServer::class)->isTrustedClientIp();

        $this->assertFalse($isTrusted);
    }

    public function testSetOxCookieAddsCookieToResponse(): void
    {
        $response = $this->handleRequestSettingCookie('http://shop.test/', false);

        $this->assertSame('some-value', $response->headers->getCookies()[0]->getValue());
    }

    #[DataProvider('secureCookieDataProvider')]
    public function testSetOxCookieIsSecureForHttpsRequestsOrWhenRequested(
        string $url,
        bool $secure,
        bool $expectedSecure
    ): void {
        $response = $this->handleRequestSettingCookie($url, $secure);

        $this->assertSame($expectedSecure, $response->headers->getCookies()[0]->isSecure());
    }

    public static function secureCookieDataProvider(): array
    {
        return [
            'http' => ['http://shop.test/', false, false],
            'https' => ['https://shop.test/', false, true],
            'http with secure flag' => ['http://shop.test/', true, true],
        ];
    }

    #[DataProvider('shopUrlDataProvider')]
    public function testGetUserCookieReturnsLoginCookieWithoutRedirect(string $url): void
    {
        $this->requestWithRememberMeCookies($url);
        $_COOKIE[$this->getLoginCookieName()] = 'some-user@@@some-hash';

        $userCookie = oxNew(UtilsServer::class)->getUserCookie();

        $this->assertSame('some-user@@@some-hash', $userCookie);
    }

    public static function shopUrlDataProvider(): array
    {
        return [
            'http' => ['http://shop.test/'],
            'https' => ['https://shop.test/'],
        ];
    }

    public function testGetUserCookieOfHttpsRequestWithoutLoginCookieDoesNotRedirect(): void
    {
        $this->requestWithRememberMeCookies('https://shop.test/');

        $this->assertNull(oxNew(UtilsServer::class)->getUserCookie());
    }

    public function testGetUserCookieRedirectsHttpRequestWithoutLoginCookieToHttps(): void
    {
        $this->requestWithRememberMeCookies('http://shop.test/');

        $this->expectException(RedirectException::class);

        oxNew(UtilsServer::class)->getUserCookie();
    }

    private function handleRequestSettingCookie(string $url, bool $secure): Response
    {
        $request = Request::create($url);
        $this->get(Request::class)->server->replace($request->server->all());
        $request->attributes->set('_controller', static function () use ($secure): Response {
            oxNew(UtilsServer::class)->setOxCookie('some-cookie', 'some-value', 0, '/', null, false, $secure);

            return new Response();
        });

        return $this->get(HttpKernelInterface::class)->handle($request);
    }

    private function requestWithRememberMeCookies(string $url): void
    {
        $this->setParameter('oxid_esales.shop_url', 'https://shop.test/');
        $request = Request::create($url);
        $this->get(Request::class)->server->replace($request->server->all());
        $_SERVER['REQUEST_URI'] = $request->getRequestUri();
        $_COOKIE[$this->getLoginCookieName() . '_autologin'] = '1';
        unset($_COOKIE[$this->getLoginCookieName()]);
    }

    private function getLoginCookieName(): string
    {
        return 'oxid_' . Registry::getConfig()->getShopId();
    }
}
