<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Database;

use OxidEsales\EshopCommunity\Internal\Framework\Database\BootstrapConnectionFactory;
use PHPUnit\Framework\TestCase;

class BootstrapConnectionFactoryTest extends TestCase
{
    public function testCreateWhenCalledTwiceWillReturnTheSameInstance(): void
    {
        $bootstrapConnectionFactory = new BootstrapConnectionFactory();
        $this->assertSame(
            $bootstrapConnectionFactory->create(),
            $bootstrapConnectionFactory->create(),
        );
    }
}
