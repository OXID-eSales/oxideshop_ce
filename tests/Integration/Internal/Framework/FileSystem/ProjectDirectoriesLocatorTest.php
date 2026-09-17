<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\FileSystem;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ProjectDirectoriesLocator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;

final class ProjectDirectoriesLocatorTest extends TestCase
{
    public function testGetRootPathReturnsAbsolutePath(): void
    {
        $this->assertTrue(Path::isAbsolute((new ProjectDirectoriesLocator())->getRootPath()));
    }

    public function testGetSourcePathAppendsSourceToRootPath(): void
    {
        $locator = new ProjectDirectoriesLocator();

        $this->assertSame(Path::join($locator->getRootPath(), 'source'), $locator->getSourcePath());
    }

    public function testGetVendorPathAppendsVendorToRootPath(): void
    {
        $locator = new ProjectDirectoriesLocator();

        $this->assertSame(Path::join($locator->getRootPath(), 'vendor'), $locator->getVendorPath());
    }

    public function testGetOutPathAppendsOutToSourcePath(): void
    {
        $locator = new ProjectDirectoriesLocator();

        $this->assertSame(Path::join($locator->getSourcePath(), 'out'), $locator->getOutPath());
    }
}
