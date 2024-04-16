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
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class SessionTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setApplicationDefaults();
    }

    /**  @runInSeparateProcess   */
    public function testGetSidFromRequestWithForceSidInRequestAndDisabledConfig(): void
    {
        Registry::getConfig()->setConfigParam('disallowForceSessionIdInRequest', true);
        $sessionId = uniqid('session-id-', true);
        $_GET['force_sid'] = $sessionId;
        $session = oxNew(Session::class);
        $session->start();

        $sid = $session->getId();

        $this->assertNotEquals($sessionId, $sid);
    }

    /**  @runInSeparateProcess   */
    public function testProcessUrlWithDefaultConfig(): void
    {
        $sessionId = uniqid('session-id-', true);
        $url = 'https://myshop.abc';
        $session = oxNew(Session::class);
        $session->setId($sessionId);

        $processedUrl = $session->processUrl($url);

        $this->assertStringContainsString("force_sid=$sessionId", $processedUrl);
    }

    /**  @runInSeparateProcess   */
    public function testProcessUrlWithDisabledConfig(): void
    {
        Registry::getConfig()->setConfigParam('disallowForceSessionIdInRequest', true);
        $sessionId = uniqid('session-id-', true);
        $url = 'https://myshop.abc';
        $session = oxNew(Session::class);
        $session->setId($sessionId);

        $processedUrl = $session->processUrl($url);

        $this->assertStringNotContainsString($sessionId, $processedUrl);
    }

    /**  @runInSeparateProcess   */
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

    /**  @runInSeparateProcess   */
    public function testAllowSessionStartWithSidInRequestAndDisabledConfig(): void
    {
        Registry::getConfig()->setConfigParam('disallowForceSessionIdInRequest', true);
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

    /**  @runInSeparateProcess   */
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

    /**  @runInSeparateProcess   */
    public function testAllowSessionStartWithForceSidInRequestAndDisabledConfig(): void
    {
        Registry::getConfig()->setConfigParam('disallowForceSessionIdInRequest', true);
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

    /**  @runInSeparateProcess   */
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

    /**  @runInSeparateProcess   */
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

    private function setApplicationDefaults(): void
    {
        Registry::getConfig()->setConfigParam('disallowForceSessionIdInRequest', false);
    }
}
