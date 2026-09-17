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

final class EditionDirectoriesLocatorTest extends TestCase
{
    public function testGetEditionRootPathFallsBackToProjectRootForCommunity(): void
    {
        $locator = new EditionDirectoriesLocator();

        $this->assertSame(
            (new ProjectDirectoriesLocator())->getRootPath(),
            $locator->getEditionRootPath(Edition::Community)
        );
    }

    public function testGetEditionSourcePathMatchesProjectSourcePathForCommunity(): void
    {
        $locator = new EditionDirectoriesLocator();

        $this->assertSame(
            (new ProjectDirectoriesLocator())->getSourcePath(),
            $locator->getEditionSourcePath(Edition::Community)
        );
    }
}
