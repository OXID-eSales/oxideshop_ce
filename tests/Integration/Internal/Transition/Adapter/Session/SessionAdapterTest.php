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

    public function testReadingStartsTheSession(): void
    {
        $this->assertFalse(Registry::getSession()->isSessionStarted());

        $this->get(SessionInterface::class)->has('usr');

        $this->assertTrue(Registry::getSession()->isSessionStarted());
    }
}
