<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Templating\Locator;

use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Service\AdminUiFileLocatorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator\AdminNavigationFileLocator;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator\NavigationFileLocatorInterface;
use PHPUnit\Framework\TestCase;

final class AdminNavigationFileLocatorTest extends TestCase
{
    public function testLocateReturnsAdminUiFilesBeforeFilesOfFurtherLocators(): void
    {
        $adminUiFileLocator = $this->createStub(AdminUiFileLocatorInterface::class);
        $adminUiFileLocator->method('locate')->willReturnMap([
            ['user.xml', ['/admin-ui/user.xml']],
            ['menu.xml', ['/admin-ui/menu.xml']],
        ]);
        $moduleMenuFileLocator = $this->createStub(NavigationFileLocatorInterface::class);
        $moduleMenuFileLocator->method('locate')->willReturn(['/module/menu.xml']);

        $locator = new AdminNavigationFileLocator($adminUiFileLocator, [$moduleMenuFileLocator]);

        $this->assertSame(
            ['/admin-ui/user.xml', '/admin-ui/menu.xml', '/module/menu.xml'],
            $locator->locate()
        );
    }
}
