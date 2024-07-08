<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\FileSystem;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ProjectRootLocator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;

final class ProjectRootLocatorTest extends TestCase
{
    public function testGeProjectRootDirectoryContainsDistFile(): void
    {
        $rootPath = (new ProjectRootLocator())->getProjectRoot();
        $envFile = Path::join($rootPath, '.env.dist');

        $this->assertFileExists($envFile);
    }

    public function testGeProjectRootReturnsAbsolutePath(): void
    {
        $rootPath = (new ProjectRootLocator())->getProjectRoot();

        $this->assertTrue(Path::isAbsolute($rootPath));
    }
}
