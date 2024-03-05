<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Setup\Directory;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use OxidEsales\EshopCommunity\Internal\Setup\Directory\Exception\NonExistenceDirectoryException;
use OxidEsales\EshopCommunity\Internal\Setup\Directory\Exception\NotAbsolutePathException;
use OxidEsales\EshopCommunity\Internal\Setup\Directory\Service\DirectoryValidator;
use PHPUnit\Framework\TestCase;

final class DirectoryValidatorTest extends TestCase
{
    private string $shopSourcePath;

    protected function setUp(): void
    {
        $this->shopSourcePath = __DIR__ . '/Fixtures/dir-structure/test-folder';

        parent::setUp();
    }

    #[DoesNotPerformAssertions]
    public function testDirectoriesExistentAndPermission(): void
    {
        $directoryValidator = new DirectoryValidator();
        $directoryValidator->validateDirectory($this->shopSourcePath, $this->shopSourcePath . '/tmp');
    }

    public function testCheckPathIsAbsolute(): void
    {
        $shopSourcePath  = 'source';
        $shopCompilePath  = 'source/tmp';

        $directoryValidator = new DirectoryValidator();

        $this->expectException(NotAbsolutePathException::class);
        $directoryValidator->checkPathIsAbsolute($shopSourcePath, $shopCompilePath);
    }

    public function testNonExistentDirectories(): void
    {
        $shopSourcePath  = '/test-folder';

        $directoryValidator = new DirectoryValidator();

        $this->expectException(NonExistenceDirectoryException::class);
        $directoryValidator->validateDirectory($shopSourcePath, $this->shopSourcePath . '/tmp');
    }
}
