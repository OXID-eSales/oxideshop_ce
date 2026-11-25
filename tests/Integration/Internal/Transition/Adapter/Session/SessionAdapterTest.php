<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\Session;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Session\SessionInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class SessionAdapterTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->setParameter('oxid_esales.force_session_start', true);
    }

    public function testHasWithoutSessionCookieDoesNotStartSession(): void
    {
        $this->get(SessionInterface::class)->has('some-key');

        $this->assertFalse(Registry::getSession()->isSessionStarted());
    }

    public function testGetWithSessionCookieResumesSession(): void
    {
        $this->storeSessionOfPreviousRequest(['some-key' => 'some-value']);

        $value = $this->get(SessionInterface::class)->get('some-key');

        $this->assertSame('some-value', $value);
    }

    public function testSetStartsSession(): void
    {
        $this->get(SessionInterface::class)->set('some-key', 'some-value');

        $this->assertTrue(Registry::getSession()->isSessionStarted());
    }

    public function testRemoveDeletesValueOfResumedSession(): void
    {
        $this->storeSessionOfPreviousRequest(['some-key' => 'some-value']);
        $session = $this->get(SessionInterface::class);

        $session->remove('some-key');

        $this->assertFalse($session->has('some-key'));
    }

    private function storeSessionOfPreviousRequest(array $values): void
    {
        $sessionId = uniqid('session');
        session_id($sessionId);
        session_start();
        $_SESSION = $values;
        session_write_close();
        $_SESSION = [];
        $_COOKIE['sid'] = $sessionId;
    }
}
