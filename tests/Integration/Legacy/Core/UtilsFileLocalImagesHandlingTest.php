<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Core;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsFile;
use OxidEsales\EshopCommunity\Application\Model\Article;
use OxidEsales\EshopCommunity\Core\Field;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ImageHandlerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\MasterImageHandler as LocalImageHandler;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class UtilsFileLocalImagesHandlingTest extends IntegrationTestCase
{
    use ContainerTrait;

    private string $testFile = '';

    private string $testFileDestination = '';

    private string $testFileDuplicateDestination = '';

    private $someTmpDir = '';

    public function setUp(): void
    {
        $this->checkImageHandlerIsTestable();
        parent::setUp();
        $this->prepareTestDirectories();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->clearTestDirectories();
    }

    public function testProcessFilesWillCopyFile(): void
    {
        $tempPath = Path::join($this->someTmpDir, 'tmp.jpg');
        $product = oxNew(Article::class);
        $files = [
            'myfile' => [
                'tmp_name' => [
                    0 => $tempPath,
                ],
                'name' => [
                    0 => $this->testFile,
                ],
            ],
        ];
        (new Filesystem())->touch($tempPath);

        (new UtilsFile())->processFiles($product, $files, true);

        $this->assertFileExists($this->testFileDestination);
    }

    public function testProcessFilesWithSuperGlobals(): void
    {
        $tempPath = Path::join($this->someTmpDir, 'tmp.jpg');
        $_FILES['myfile'] = [
            'name' => [
                0 => $this->testFile,
            ],
            'tmp_name' => [
                0 => $tempPath,
            ],
            'error' => [
                0 => 0,
            ],
        ];
        (new Filesystem())->touch($tempPath);

        (new UtilsFile())->processFiles(null, null, true);

        $this->assertFileExists($this->testFileDestination);
    }

    public function testProcessFilesWithCreateDuplicateName(): void
    {
        $tempPath = Path::join($this->someTmpDir, 'tmp.jpg');
        $product = oxNew(Article::class);
        $files = [
            'myfile' => [
                'tmp_name' => [
                    0 => $tempPath,
                ],
                'name' => [
                    0 => $this->testFile,
                ],
            ],
        ];
        (new Filesystem())->touch($tempPath);

        (new UtilsFile())->processFiles($product, $files, true);
        (new UtilsFile())->processFiles($product, $files, true);

        $this->assertFileExists($this->testFileDestination);
        $this->assertFileExists($this->testFileDuplicateDestination);
    }

    public function testProcessFilesWillOverwrite(): void
    {
        $tempPath1 = Path::join($this->someTmpDir, 'tmp.jpg');
        $tempPath2 = Path::join($this->someTmpDir, 'tmp2.jpg');
        $product = oxNew(Article::class);
        $files = [
            'myfile' => [
                'tmp_name' => [
                    0 => $tempPath1,
                ],
                'name' => [
                    0 => $this->testFile,
                ],
            ],
        ];
        $files2 = [
            'myfile' => [
                'tmp_name' => [
                    0 => $tempPath2,
                ],
                'name' => [
                    0 => $this->testFile,
                ],
            ],
        ];
        $utilsFile = new UtilsFile();
        (new Filesystem())->dumpFile($tempPath1, 'abc');
        (new Filesystem())->dumpFile($tempPath2, 'xyz');

        $utilsFile->processFiles($product, $files, true);
        $utilsFile->processFiles($product, $files2, true, false);

        $this->assertFileDoesNotExist($this->testFileDuplicateDestination);
        $this->assertSame('xyz', file_get_contents($this->testFileDestination));
        $this->assertSame(1, $utilsFile->getNewFilesCounter());
    }

    public function testGetNewFilesCounter(): void
    {
        $tempPath1 = Path::join($this->someTmpDir, 'tmp.jpg');
        $tempPath2 = Path::join($this->someTmpDir, 'tmp2.jpg');
        $product = oxNew(Article::class);
        $files = [
            'myfile' => [
                'tmp_name' => [
                    0 => $tempPath1,
                    1 => $tempPath2,
                ],
                'name' => [
                    0 => $this->testFile,
                    1 => $this->testFile,
                ],
            ],
        ];
        (new Filesystem())->touch($tempPath1);
        (new Filesystem())->touch($tempPath2);
        $utilsFile = new UtilsFile();

        $utilsFile->processFiles($product, $files, true);

        $this->assertSame(2, $utilsFile->getNewFilesCounter());
    }

    public function testProcessFilesSetsObjectValue(): void
    {
        $tempPath = Path::join($this->someTmpDir, 'tmp.jpg');
        $product = oxNew(Article::class);
        $product->oxarticles__oxfile = new Field();
        $files = [
            'myfile' => [
                'tmp_name' => [
                    'something@oxarticles__oxfile' => $tempPath,
                ],
                'name' => [
                    'something@oxarticles__oxfile' => $this->testFile,
                ],
            ],
        ];
        (new Filesystem())->touch($tempPath);

        (new UtilsFile())->processFiles($product, $files, true);

        $this->assertSame($this->testFile, $product->oxarticles__oxfile->getRawValue());
    }

    private function prepareTestDirectories(): void
    {
        $this->someTmpDir = Registry::getConfig()->getConfigParam('sCompileDir');
        $pictureDir = Registry::getConfig()->getPictureDir(false);
        $uniqueFilename = uniqid('some_image_', true);
        $this->testFile = sprintf('%s.jpg', $uniqueFilename);
        $testFileDuplicate = sprintf('%s(1).jpg', $uniqueFilename);
        $this->testFileDestination = Path::join($pictureDir, '0', $this->testFile);
        $this->testFileDuplicateDestination = Path::join($pictureDir, '0', $testFileDuplicate);
    }

    private function clearTestDirectories(): void
    {
        (new Filesystem())->remove($this->testFileDestination);
        (new Filesystem())->remove($this->testFileDuplicateDestination);
    }

    private function checkImageHandlerIsTestable(): void
    {
        if ($this->get(ImageHandlerInterface::class)::class !== LocalImageHandler::class) {
            $this->markTestSkipped('This test runs only when local filesystem is used for image storage.');
        }
    }
}
