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
use PHPUnit\Framework\TestCase;

final class SessionTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setApplicationDefaults();
    }

    public function testInitNewSessionUnsetsSessionVariables(): void
    {
        $session = Registry::getSession();

        $session->setVariable('testVariable', 'value');
        Registry::getSession()->initNewSession();

        $this->assertNull($session->getVariable('testVariable'));
    }

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
        Registry::getConfig()->setConfigParam('disallowForceSessionIdInRequest', true);
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

    private function setApplicationDefaults(): void
    {
        Registry::getConfig()->setConfigParam('disallowForceSessionIdInRequest', false);
    }
}
