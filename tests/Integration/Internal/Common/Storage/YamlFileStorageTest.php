<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Common\Storage;

use OxidEsales\EshopCommunity\Internal\Common\Storage\YamlFileStorage;
use OxidEsales\TestingLibrary\VfsStreamWrapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;

/**
 * @internal
 */
class YamlFileStorageTest extends TestCase
{
    public function testSaving()
    {
        $testData = [
            'one' => [
                'two',
            ],
            'uno' => [
                'due',
            ],
        ];

        $yamlFileStorage = new YamlFileStorage(
            new FileLocator(),
            $this->getFilePath()
        );

        $yamlFileStorage->save($testData);

        $this->assertSame(
            $testData,
            $yamlFileStorage->get()
        );
    }

    public function testCreatesNewFileIfItDoesNotExist()
    {
        $yamlFileStorage = new YamlFileStorage(
            new FileLocator(),
            '/tmp/testStorageFile.yaml'
        );

        $yamlFileStorage->save(['testData']);

        $this->assertSame(
            ['testData'],
            $yamlFileStorage->get()
        );
    }

    /**
     * @return string
     */
    private function getFilePath(): string
    {
        $vfsStreamWrapper = new VfsStreamWrapper();
        $relativePath = 'test/storage.yaml';
        $path = $vfsStreamWrapper->getRootPath() . $relativePath;

        if (!is_file($path)) {
            $vfsStreamWrapper->createFile($relativePath);
        }

        return $path;
    }
}
