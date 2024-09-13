<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Setup\Directory;

use OxidEsales\EshopCommunity\Internal\Setup\Directory\DirectoryValidator;
use OxidEsales\EshopCommunity\Internal\Setup\Directory\NonExistenceDirectoryException;
use OxidEsales\EshopCommunity\Internal\Setup\Directory\NotAbsolutePathException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

final class DirectoryValidatorTest extends TestCase
{
    private string $shopSourcePath;
    private BasicContextInterface $basicContext;

    protected function setUp(): void
    {
        $this->shopSourcePath = __DIR__ . '/Fixtures/dir-structure/test-folder';
        $this->basicContext = new BasicContext();

        parent::setUp();
    }

    #[DoesNotPerformAssertions]
    public function testDirectoriesExistentAndPermission(): void
    {
        $directoryValidator = $this->getDirectoryValidator();
        $directoryValidator->validateDirectory($this->shopSourcePath . '/tmp');
    }

    public function testCheckPathIsAbsolute(): void
    {
        $shopCompilePath  = 'source/tmp';

        $directoryValidator = $this->getDirectoryValidator();

        $this->expectException(NotAbsolutePathException::class);
        $directoryValidator->checkPathIsAbsolute($shopCompilePath);
    }

    public function testNonExistentDirectories(): void
    {
        $directoryValidator = $this->getDirectoryValidator();

        $this->expectException(NonExistenceDirectoryException::class);
        $directoryValidator->validateDirectory('/notExists/tmp');
    }

    private function getDirectoryValidator(): DirectoryValidator
    {
        return new DirectoryValidator($this->basicContext);
    }
}
