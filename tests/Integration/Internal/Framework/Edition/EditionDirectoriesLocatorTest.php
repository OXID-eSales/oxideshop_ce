<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Edition;

use OxidEsales\EshopCommunity\Internal\Framework\Edition\Edition;
use OxidEsales\EshopCommunity\Internal\Framework\Edition\EditionDirectoriesLocator;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ProjectDirectoriesLocator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;

final class EditionDirectoriesLocatorTest extends TestCase
{
    public function testGetEditionRootPathForCommunity(): void
    {
        $locator = new EditionDirectoriesLocator();

        $this->assertSame($this->getExpectedCommunityRootPath(), $locator->getEditionRootPath(Edition::Community));
    }

    public function testGetEditionSourcePathForCommunity(): void
    {
        $locator = new EditionDirectoriesLocator();

        $this->assertSame(
            Path::join($this->getExpectedCommunityRootPath(), 'source'),
            $locator->getEditionSourcePath(Edition::Community)
        );
    }

    private function getExpectedCommunityRootPath(): string
    {
        $projectDirectoriesLocator = new ProjectDirectoriesLocator();
        $vendoredCommunityPath = Path::join($projectDirectoriesLocator->getVendorPath(), 'oxid-esales', 'oxideshop-ce');

        return is_dir($vendoredCommunityPath)
            ? $vendoredCommunityPath
            : $projectDirectoriesLocator->getRootPath();
    }
}
