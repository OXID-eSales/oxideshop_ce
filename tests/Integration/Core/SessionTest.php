<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\Eshop\Core\UtilsServer;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SessionTest extends TestCase
{
    use ContainerTrait;

    public static function tearDownAfterClass(): void
    {
        ContainerFactory::resetContainer();

        parent::tearDownAfterClass();
    }

    public function testGetSidFromRequestWithForceSidInRequestAndDisabledConfig(): void
    {
        $this->setParameter('oxid_disallow_force_session_id', true);
        $this->attachContainerToContainerFactory();
        $sessionId = uniqid('session-id-', true);
        $_GET['force_sid'] = $sessionId;
        $session = oxNew(Session::class);
        $session->start();

        $sid = $session->getId();

        $this->assertNotEquals($sessionId, $sid);
    }

    public function testProcessUrlWithDefaultConfig(): void
    {
        $sessionId = uniqid('session-id-', true);
        $url = 'https://myshop.abc';
        $session = oxNew(Session::class);
        $session->setId($sessionId);

        $processedUrl = $session->processUrl($url);

        $this->assertStringContainsString("force_sid=$sessionId", $processedUrl);
    }

    public function testProcessUrlWithDisabledConfig(): void
    {
        $this->setParameter('oxid_disallow_force_session_id', true);
        $this->attachContainerToContainerFactory();
        $sessionId = uniqid('session-id-', true);
        $url = 'https://myshop.abc';
        $session = oxNew(Session::class);
        $session->setId($sessionId);

        $processedUrl = $session->processUrl($url);

        $this->assertStringNotContainsString($sessionId, $processedUrl);
    }

    public function testAllowSessionStartWithSidInRequestAndDefaultConfig(): void
    {
        $utilsSever = $this->createMock(UtilsServer::class);
        Registry::set(UtilsServer::class, $utilsSever);
        $session = oxNew(Session::class);
        $utilsSever->expects($this->once())
            ->method('setOxCookie')
            ->with('sid', $this->isType('string'), 0, '/', null, true, false, true);
        $sessionId = uniqid('session-id-', true);
        $_GET['sid'] = $sessionId;

        $session->regenerateSessionId();
    }

    public function testAllowSessionStartWithSidInRequestAndDisabledConfig(): void
    {
        $this->setParameter('oxid_disallow_force_session_id', true);
        $this->attachContainerToContainerFactory();
        $utilsSever = $this->createMock(UtilsServer::class);
        Registry::set(UtilsServer::class, $utilsSever);
        $session = oxNew(Session::class);
        $utilsSever->expects($this->once())
            ->method('setOxCookie')
            ->with('sid', $this->isType('string'), 0, '/', null, true, false, true);
        $sessionId = uniqid('session-id-', true);
        $_GET['sid'] = $sessionId;

        $session->regenerateSessionId();
    }

    public function testAllowSessionStartWithForceSidInRequestAndDefaultConfig(): void
    {
        $utilsSever = $this->createMock(UtilsServer::class);
        Registry::set(UtilsServer::class, $utilsSever);
        $session = oxNew(Session::class);
        $utilsSever->expects($this->once())
            ->method('setOxCookie')
            ->with('sid', $this->isType('string'), 0, '/', null, true, false, true);
        $sessionId = uniqid('session-id-', true);
        $_GET['force_sid'] = $sessionId;

        $session->regenerateSessionId();
    }

    public function testAllowSessionStartWithForceSidInRequestAndDisabledConfig(): void
    {
        $this->setParameter('oxid_disallow_force_session_id', true);
        $this->attachContainerToContainerFactory();
        $utilsSever = $this->createMock(UtilsServer::class);
        Registry::set(UtilsServer::class, $utilsSever);
        $session = oxNew(Session::class);
        $utilsSever->expects($this->once())
            ->method('setOxCookie')
            ->with('sid', null, 0, '/', null, true, false, true);
        $sessionId = uniqid('session-id-', true);
        $_GET['force_sid'] = $sessionId;

        $session->regenerateSessionId();
    }

    public function testSidNeededForDifferentUrls(): void
    {
        $session = oxNew(Session::class);

        $utilsSever = $this->createMock(UtilsServer::class);
        Registry::set(UtilsServer::class, $utilsSever);
        $utilsSever
            ->method('isCurrentUrl')
            ->willReturn(false);

        $this->assertTrue($session->isSidNeeded('https://myshop.abc'));
    }

    public function testSidNotNeededForTheSameUrl(): void
    {
        $session = oxNew(Session::class);

        $utilsSever = $this->createMock(UtilsServer::class);
        Registry::set(UtilsServer::class, $utilsSever);
        $utilsSever
            ->method('isCurrentUrl')
            ->willReturn(true);

        $url = Registry::getConfig()->getCurrentShopUrl();

        $this->assertFalse($session->isSidNeeded($url));
    }

    public function testSessionChallengeTrue(): void
    {
        $session = oxNew(Session::class);
        $session->start();
        $token = $session->getSessionChallengeToken();
        $_POST['stoken'] = $token;
        $challenge = $session->checkSessionChallenge();
        $this->assertTrue($challenge);
    }

    public function testSessionChallengeEmptyToken(): void
    {
        $session = oxNew(Session::class);
        $session->start();
        $challenge = $session->checkSessionChallenge();
        $this->assertFalse($challenge);
    }

    public function testSessionChallengeWrongToken(): void
    {
        $session = oxNew(Session::class);
        $session->start();
        $_POST['stoken'] = 'dummy-string-value';
        $challenge = $session->checkSessionChallenge();
        $this->assertFalse($challenge);
    }

    public function testIsSidNeededWithForceSessionStartAndDefaultConfiguration(): void
    {
        $needSid = oxNew(Session::class)->isSidNeeded();

        $this->assertFalse($needSid);
    }

    public function testIsSidNeededWithForceSessionStart(): void
    {
        $this->assertFalse(oxNew(Session::class)->isSidNeeded());

        $this->setParameter('oxid_force_session_start', true);
        $this->attachContainerToContainerFactory();

        $needSid = oxNew(Session::class)->isSidNeeded();

        $this->assertTrue($needSid);
    }

    public function testIsSidNeededWithOxidCookiesSession(): void
    {
        $this->assertFalse(oxNew(Session::class)->isSidNeeded());

        $this->setParameter('oxid_cookies_session', false);
        $this->attachContainerToContainerFactory();

        $needSid = oxNew(Session::class)->isSidNeeded();

        $this->assertTrue($needSid);
    }

    public static function isSidNeededDefaultsDataProvider(): array
    {
        return [
            ['cl', 'register'],
            ['cl', 'account'],
            ['fnc', 'tobasket'],
            ['fnc', 'login_noredirect'],
            ['fnc', 'tocomparelist'],
            ['fnc', 'tocomparelist'],
            ['_artperpage', '1'],
            ['ldtype', 'some-type'],
            ['listorderby', 'id'],
        ];
    }

    #[DataProvider('isSidNeededDefaultsDataProvider')]
    public function testIsSidNeededWithSessionInitParamsAndDefaults(string $param, string $value): void
    {
        $_GET[$param] = $value;
        $sidNeeded = oxNew(Session::class)->isSidNeeded();

        $this->assertTrue($sidNeeded);
    }

    public function testIsSidNeededWithSessionInitParamsAndParamNotInDefaults(): void
    {
        $_GET['cl'] = 'this-controller-is-not-configured';
        $sidNeeded = oxNew(Session::class)->isSidNeeded();

        $this->assertFalse($sidNeeded);
    }

    public function testIsSidNeededWithSessionInitParamsAndNewParam(): void
    {
        $this->setParameter('oxid_session_init_params', [
            'cl' => [
                'abc' => true,
            ]
        ]);
        $this->attachContainerToContainerFactory();

        $_GET['cl'] = 'abc';
        $sidNeeded = oxNew(Session::class)->isSidNeeded();

        $this->assertTrue($sidNeeded);
    }

    public function testIsSidNeededWithSessionInitParamsAndOverwrittenDefault(): void
    {
        $this->setParameter('oxid_session_init_params', [
            'cl' => [
                'abc' => true,
                'register' => false,
            ]
        ]);
        $this->attachContainerToContainerFactory();

        $_GET['cl'] = 'register';
        $sidNeeded = oxNew(Session::class)->isSidNeeded();

        $this->assertFalse($sidNeeded);
    }

    #[DataProvider('isSidNeededDefaultsDataProvider')]
    public function testIsSidNeededWithSessionInitParamsAndDefaultsUnchanged(string $param, string $value): void
    {
        $this->setParameter('oxid_session_init_params', [
            'cl' => [
                'abc' => true,
            ],
            'fnc' => [
                'def' => true,
            ],
            'ghi' => true,
        ]);
        $this->attachContainerToContainerFactory();

        $_GET[$param] = $value;
        $sidNeeded = oxNew(Session::class)->isSidNeeded();

        $this->assertTrue($sidNeeded);
    }

    public function testIsSidNeededWithNonArrayValueWillSetAnyControllerAsRequiringSessionId(): void
    {
        $_GET['cl'] = 'some-unconfigured-controller';

        $this->assertFalse(oxNew(Session::class)->isSidNeeded());

        $this->setParameter('oxid_session_init_params', [
            'cl' => true,
        ]);
        $this->attachContainerToContainerFactory();

        $sidNeeded = oxNew(Session::class)->isSidNeeded();

        $this->assertTrue($sidNeeded);
    }
}
