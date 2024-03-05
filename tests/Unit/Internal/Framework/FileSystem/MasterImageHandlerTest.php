<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\FileSystem;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\MasterImageHandler;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class MasterImageHandlerTest extends TestCase
{
    /** @var vfsStreamDirectory */
    private $rootDir;
    private ?MasterImageHandler $imageHandler = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createFileStructure();
        $this->initImageHandler();
    }

    public function testCopyCreatesDirectoriesWithCorrectPermissions(): void
    {
        $destinationDirectory = 'dir1/dir2';

        $this->imageHandler->copy(
            Path::join($this->rootDir->url(), 'tmp/test-1.jpg'),
            "$destinationDirectory/test.jpg"
        );
        $directoryPermissions = $this->rootDir->getChild("shop-source-path/$destinationDirectory")->getPermissions();

        $this->assertSame(0744, $directoryPermissions);
    }

    public function testCopyCreatesFilesWithCorrectPermissions(): void
    {
        $destinationFile = 'test.jpg';

        $this->imageHandler->copy(
            Path::join($this->rootDir->url(), '/tmp/test-1.jpg'),
            $destinationFile
        );
        $filePermissions = $this->rootDir->getChild("shop-source-path/$destinationFile")->getPermissions();

        $this->assertSame(0644, $filePermissions);
    }

    public function testCopyTwiceOverwritesFile(): void
    {
        $destinationFile = 'test.jpg';

        $this->imageHandler->copy(
            Path::join($this->rootDir->url(), '/tmp/test-1.jpg'),
            $destinationFile
        );
        $this->imageHandler->copy(
            Path::join($this->rootDir->url(), '/tmp/test-2.jpg'),
            $destinationFile
        );
        $fileContent = $this->rootDir->getChild("shop-source-path/$destinationFile")->getContent();

        $this->assertSame('test-content-2', $fileContent);
    }

    public function testUploadWithoutPostRequestWillThrow(): void
    {
        $sourceFile = '/tmp/test-1.jpg';

        $this->expectException(IOException::class);

        $this->imageHandler->upload(
            Path::join($this->rootDir->url(), $sourceFile),
            'test.jpg'
        );
    }

    public function testRemove(): void
    {
        $existingFile = 'shop-file.jpg';
        $this->assertTrue($this->rootDir->hasChild("shop-source-path/$existingFile"));

        $this->imageHandler->remove($existingFile);

        $this->assertFalse($this->rootDir->hasChild("shop-source-path/$existingFile"));
    }

    public function testExistsWithMissingFile(): void
    {
        $missingFile = uniqid('test_file_', true);

        $exists = $this->imageHandler->exists($missingFile);

        $this->assertFalse($exists);
    }

    public function testExistsWhenFileIsPresent(): void
    {
        $existingFile = 'shop-file.jpg';

        $exists = $this->imageHandler->exists($existingFile);

        $this->assertTrue($exists);
    }

    private function createFileStructure(): void
    {
        $directoryStructure = [
            'tmp' => [
                'test-1.jpg' => 'test-content-1',
                'test-2.jpg' => 'test-content-2',
            ],
            'shop-source-path' => [
                'shop-file.jpg' => 'shop-content',
            ],
        ];
        $this->rootDir = vfsStream::setup('root', 0777);
        vfsStream::create($directoryStructure, $this->rootDir);
    }

    private function initImageHandler(): void
    {
        $contextMock = $this->createConfiguredMock(ContextInterface::class, [
            'getSourcePath' => vfsStream::url('root/shop-source-path')
        ]);
        $this->imageHandler = new MasterImageHandler(
            new Filesystem(),
            $contextMock
        );
    }
}
