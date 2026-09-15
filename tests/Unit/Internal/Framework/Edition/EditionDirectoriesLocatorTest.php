<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Edition;

use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Framework\Edition\Edition;
use OxidEsales\EshopCommunity\Internal\Framework\Edition\EditionDirectoriesLocator;
use OxidEsales\EshopCommunity\Internal\Framework\Edition\EditionPaths;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\DirectoryNotExistentException;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ProjectRootLocator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;

final class EditionDirectoriesLocatorTest extends TestCase
{
    public function testGetEditionSourcePathAppendsSourceFolderForCommunity(): void
    {
        $projectRoot = vfsStream::setup('project')->url();

        $locator = new EditionDirectoriesLocator($this->createProjectRootLocatorStub($projectRoot));

        $this->assertSame(
            Path::join($projectRoot, 'source'),
            $locator->getEditionSourcePath(Edition::Community)
        );
    }

    public function testGetEditionRootPathResolvesToVendorDirectoryWhenEditionIsInstalled(): void
    {
        $projectRoot = $this->setupProjectWithEdition(Edition::Professional);

        $locator = new EditionDirectoriesLocator($this->createProjectRootLocatorStub($projectRoot));

        $this->assertSame(
            Path::join($projectRoot, 'vendor', 'oxid-esales', 'oxideshop-pe'),
            $locator->getEditionRootPath(Edition::Professional)
        );
    }

    public function testGetEditionRootPathThrowsExceptionWhenEditionIsNotInstalled(): void
    {
        $projectRoot = vfsStream::setup('project')->url();

        $locator = new EditionDirectoriesLocator($this->createProjectRootLocatorStub($projectRoot));

        $this->expectException(DirectoryNotExistentException::class);
        $locator->getEditionRootPath(Edition::Enterprise);
    }

    public function testGetEditionSourcePathResolvesToVendorSourceFolderWhenCommunityEditionIsInstalled(): void
    {
        $projectRoot = $this->setupProjectWithEdition(Edition::Community);

        $locator = new EditionDirectoriesLocator($this->createProjectRootLocatorStub($projectRoot));

        $this->assertSame(
            Path::join($projectRoot, 'vendor', 'oxid-esales', 'oxideshop-ce', 'source'),
            $locator->getEditionSourcePath(Edition::Community)
        );
    }

    public function testGetEditionSourcePathResolvesToEditionRootForNonCommunityEditions(): void
    {
        $projectRoot = $this->setupProjectWithEdition(Edition::Enterprise);

        $locator = new EditionDirectoriesLocator($this->createProjectRootLocatorStub($projectRoot));

        $this->assertSame(
            Path::join($projectRoot, 'vendor', 'oxid-esales', 'oxideshop-ee'),
            $locator->getEditionSourcePath(Edition::Enterprise)
        );
    }

    private function setupProjectWithEdition(Edition $edition): string
    {
        return vfsStream::setup(
            'project',
            null,
            [
                'vendor' => [
                    'oxid-esales' => [
                        EditionPaths::from($edition->value)->getProjectFolderName() => [],
                    ],
                ],
            ],
        )->url();
    }

    private function createProjectRootLocatorStub(string $projectRoot): ProjectRootLocator
    {
        $stub = $this->createStub(ProjectRootLocator::class);
        $stub->method('getProjectRoot')->willReturn($projectRoot);

        return $stub;
    }
}
