<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Registry;
use PHPUnit\Framework\TestCase;

final class SessionTest extends TestCase
{
    public function testInitNewSessionUnsetsSessionVariables(): void
    {
        $session = Registry::getSession();

        $session->setVariable('testVariable', 'value');
        Registry::getSession()->initNewSession();

        $this->assertNull($session->getVariable('testVariable'));
    }
}