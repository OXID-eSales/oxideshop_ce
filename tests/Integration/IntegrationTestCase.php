<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration;

use Doctrine\DBAL\Driver\Connection;
use OxidEsales\EshopCommunity\Tests\CachingTrait;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\DatabaseTrait;
use OxidEsales\EshopCommunity\Tests\RequestTrait;
use PHPUnit\Framework\TestCase;

class IntegrationTestCase extends TestCase
{
    use ContainerTrait;
    use CachingTrait;
    use DatabaseTrait;
    use RequestTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->backupRequestData();
        $this->cleanupCaching();
        $this->beginTransactionForConnectionFromAppContainer();
    }

    public function tearDown(): void
    {
        $this->rollBackTransactionForConnectionFromAppContainer();
        $this->cleanupCaching();
        $this->restoreRequestData();

        parent::tearDown();
    }

    public function beginTransactionForConnectionFromTestContainer(): void
    {
        $this->beginTransaction($this->get(Connection::class));
    }

    public function beginTransactionForConnectionFromAppContainer(): void
    {
        $this->beginTransaction();
    }

    public function rollBackTransactionForConnectionFromTestContainer(): void
    {
        $this->rollBackTransaction($this->get(Connection::class));
    }

    public function rollBackTransactionForConnectionFromAppContainer(): void
    {
        $this->rollBackTransaction();
    }
}
