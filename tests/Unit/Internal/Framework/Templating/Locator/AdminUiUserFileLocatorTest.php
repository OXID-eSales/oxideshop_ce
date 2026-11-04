<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Templating\Locator;

use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\AdminUiPathProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator\AdminUiUserFileLocator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class AdminUiUserFileLocatorTest extends TestCase
{
    public function testLocateReturnsUserFileOfAdminUi(): void
    {
        $adminUiDirectory = vfsStream::setup('adminUi', null, ['user.xml' => 'user'])->url();

        $this->assertSame(
            ["$adminUiDirectory/user.xml"],
            $this->createLocator($adminUiDirectory)->locate()
        );
    }

    public function testLocateReturnsNothingWithoutUserFile(): void
    {
        $adminUiDirectory = vfsStream::setup('adminUi')->url();

        $this->assertSame([], $this->createLocator($adminUiDirectory)->locate());
    }

    private function createLocator(string $adminUiDirectory): AdminUiUserFileLocator
    {
        $adminUiPathProvider = $this->createStub(AdminUiPathProviderInterface::class);
        $adminUiPathProvider->method('getAbsolutePath')->willReturn($adminUiDirectory);

        return new AdminUiUserFileLocator($adminUiPathProvider, new Filesystem());
    }
}
