<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Env\DotenvLoader;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ProjectRootLocator;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class SystemConfigurationDaoTest extends TestCase
{
    use ContainerTrait;

    public function testGetDatabaseConfigurationWillContainSomeDefaults(): void
    {
        new DotenvLoader(
            (new ProjectRootLocator())->getProjectRoot()
        );

        $this->assertNotEmpty(getenv('OXID_DB_URL'));
    }

    public function testGetBootstrapParametersWillContainsDefaults(): void
    {
        $this->assertNotEmpty($this->get(ContextInterface::class)->getCacheDirectory());
    }
}
