<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\EshopCommunity\Core\FileSystem\FileSystem;

class FileSystemTest extends UnitTestCase
{
    public function testCombinePathsReturnEmptyPathWhenCalledWithoutParameters()
    {
        $fileSystem = oxNew(FileSystem::class);

        $actualConnectedPath = $fileSystem->combinePaths();
        $this->assertSame('', $actualConnectedPath);
    }

    public function testCombinePathsJoinAllParametersToSingleString()
    {
        $fileSystem = oxNew(FileSystem::class);

        $actualConnectedPath = $fileSystem->combinePaths('path1');
        $expectedPath = 'path1';
        $this->assertSame($expectedPath, $actualConnectedPath);

        $actualConnectedPath = $fileSystem->combinePaths('path1', 'path2');
        $expectedPath = 'path1/path2';
        $this->assertSame($expectedPath, $actualConnectedPath);

        $actualConnectedPath = $fileSystem->combinePaths('path1', 'path2', 'path3');
        $expectedPath = 'path1/path2/path3';
        $this->assertSame($expectedPath, $actualConnectedPath);
    }

    public function testCombinePathsReturnPathWithoutBackslash()
    {
        $fileSystem = oxNew(FileSystem::class);

        $actualConnectedPath = $fileSystem->combinePaths('path1/');
        $expectedPath = 'path1';
        $this->assertSame($expectedPath, $actualConnectedPath);
    }

    public function testCombinePathsJoinsWithSingleBackSlashEvenWhenParameterAlreadyHasBackSlash()
    {
        $fileSystem = oxNew(FileSystem::class);

        $actualConnectedPath = $fileSystem->combinePaths('path1/', 'path2/', 'path3/');
        $expectedPath = 'path1/path2/path3';
        $this->assertSame($expectedPath, $actualConnectedPath);
    }

    /**
     * Test for isReadable method
     */
    public function testIsReadable()
    {
        $filePath = 'somedir/somefile.txt';

        $vfsStreamWrapper = $this->getVfsStreamWrapper();
        $vfsStreamWrapper->createFile('somedir/somefile.txt', '');
        $testDir = $vfsStreamWrapper->getRootPath();

        $fileSystem = oxNew(FileSystem::class);
        $this->assertTrue($fileSystem->isReadable($testDir . '/' . $filePath));

        if (version_compare(PHP_VERSION, '5.5') >= 0) {
            chmod($testDir . '/' . $filePath, 0000);
            $this->assertFalse($fileSystem->isReadable($testDir . '/' . $filePath));
        }

        $this->assertFalse($fileSystem->isReadable($testDir . '/notexists.txt'));
    }
}
