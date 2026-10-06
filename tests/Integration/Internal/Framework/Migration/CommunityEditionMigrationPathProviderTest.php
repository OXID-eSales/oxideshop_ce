<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\CommunityEditionMigrationPathProvider;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class CommunityEditionMigrationPathProviderTest extends TestCase
{
    use ContainerTrait;

    public function testIsTaggedAsHighestPriorityMigrationPathProvider(): void
    {
        $this->createContainer();

        $tags = $this->container
            ->findDefinition(CommunityEditionMigrationPathProvider::class)
            ->getTag('oxid_esales.migration_path_provider');

        $this->assertSame([['priority' => 300]], $tags);
    }

    public function testGetMigrationConfigPathResolvesToTheRealCommunityEditionMigrationsFile(): void
    {
        $configPath = $this->get(CommunityEditionMigrationPathProvider::class)->getMigrationConfigPath();

        $this->assertFileExists($configPath);
        $this->assertStringEndsWith('/source/migration/migrations.yml', $configPath);
    }
}
