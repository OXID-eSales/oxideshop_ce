<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Configuration\Dao\SystemConfigurationDao;
use OxidEsales\EshopCommunity\Internal\Framework\Env\DotenvLoader;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\BootstrapLocator;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\RequestTrait;
use PHPUnit\Framework\TestCase;

final class SystemConfigurationDaoTest extends TestCase
{
    use ContainerTrait;
    use RequestTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->backupRequestData();
    }

    public function tearDown(): void
    {
        $this->restoreRequestData();
        parent::tearDown();
    }

    public function testGetDatabaseConfigurationWillContainSomeDefaults(): void
    {
        $envLoader = new DotenvLoader(
            (new BootstrapLocator())->getProjectRoot()
        );
        $databaseUrl = (new SystemConfigurationDao($envLoader))
            ->get()
            ->getDatabaseUrl();

        $this->assertNotEmpty($databaseUrl);
    }
}
