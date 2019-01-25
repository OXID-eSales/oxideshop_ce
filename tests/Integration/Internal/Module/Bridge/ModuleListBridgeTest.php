<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Bridge;

use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Common\Exception\DirectoryNotExistentException;
use OxidEsales\EshopCommunity\Internal\Common\Exception\DirectoryNotReadableException;
use OxidEsales\EshopCommunity\Internal\Module\Bridge\ModuleListBridge;
use OxidEsales\EshopCommunity\Internal\Module\Bridge\ModuleListBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleListBridgeTest extends TestCase
{
    use ContainerTrait;

    /**
     *
     */
    public function testGetModuleIdFromDirectoryThrowsExceptionIfDirectoryDoesNotExist()
    {
        $this->expectException(DirectoryNotExistentException::class);
        $moduleListBridge = $this->get(ModuleListBridgeInterface::class);
        $directoryPath = __DIR__ . DIRECTORY_SEPARATOR . 'non_existent_directory';
        $moduleListBridge->getModuleDirectoriesRecursively($directoryPath);
    }

    /**
     *
     */
    public function testGetModuleIdFromDirectoryThrowsExceptionIfDirectoryIsNotReadable()
    {
        $this->expectException(DirectoryNotReadableException::class);
        $moduleListBridge = $this->get(ModuleListBridgeInterface::class);
        $directory = vfsStream::setup('root', 000, ['test']);
        $directoryPath = $directory->url();
        $moduleListBridge->getModuleDirectoriesRecursively($directoryPath);
    }

    /**
     *
     */
    public function testGetModuleDirectoriesRecursivelyReturnsProperDirectories()
    {
        $directoryStructure = [
            'metadata.php' => '<?php ',
            'modules'      => [
                'metadata.php'     => '<?php ',
                'module1'          => [
                    'metadata.php' => '<?php '
                ],
                'module2'          => [
                    'metadata.php' => '<?php '
                ],
                'module3'          => [
                    'metadata.php' => '<?php '
                ],
                'module4'          => [
                    'metadata.php' => '<?php '
                ],
                'missing_metadata' => [
                    'some_file.php' => '<?php '
                ],
                'someDirectory'    => [
                    'module5' => [
                        'metadata.php' => '<?php '
                    ]
                ],
                'module6'          => [
                    '__metadata.php' => '<?php ' // Will not be returned
                ],
                'module7'          => [
                    'metadata#php' => '<?php ' // Will not be returned
                ],
                'module8'          => [
                    'metadata.phps' => '<?php ' // Will not be returned
                ]
            ]
        ];

        $directory = vfsStream::setup('root', null, $directoryStructure);
        $directoryPath = $directory->url();

        $moduleListBridge = new ModuleListBridge();
        $moduleDirectories = $moduleListBridge->getModuleDirectoriesRecursively($directoryPath);

        $expectedModuleDirectories = [
            $directoryPath,
            $directoryPath . DIRECTORY_SEPARATOR . 'modules',
            $directoryPath . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'module1',
            $directoryPath . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'module2',
            $directoryPath . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'module3',
            $directoryPath . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'module4',
            $directoryPath . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'someDirectory' . DIRECTORY_SEPARATOR . 'module5',
        ];

        $this->assertEquals($expectedModuleDirectories, $moduleDirectories);
    }
}
