<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Database;

use Doctrine\DBAL\Connection;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionProvider;

class ConnectionProviderTest extends \PHPUnit\Framework\TestCase
{
    public function testGetConnection()
    {
        $connectionProvider = new ConnectionProvider();

        $connection = $connectionProvider->get();

        $this->assertInstanceOf(
            Connection::class,
            $connection
        );

        $this->assertSame(
            $connection,
            $connectionProvider->get()
        );
    }
}
