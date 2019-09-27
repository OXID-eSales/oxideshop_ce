<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Storage;

use OxidEsales\EshopCommunity\Internal\Framework\Storage\YamlFileStorage;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Lock\Factory;

/**
 * @internal
 */
class YamlFileStorageTest extends TestCase
{
    use ContainerTrait;

    /**
     * @var resource
     */
    private $tempFileHandle;

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
            $this->getFilePath(),
            $this->getLockFactoryFromContainer(),
            $this->getFileSystemServiceFromContainer()
        );

        $yamlFileStorage->save($testData);

        $this->assertSame(
            $testData,
            $yamlFileStorage->get()
        );
    }

    public function testCreatesNewFileIfDoesNotExist()
    {
        $filePath = $this->getFilePath();
        unlink($filePath);

        $yamlFileStorage = new YamlFileStorage(
            new FileLocator(),
            $filePath,
            $this->getLockFactoryFromContainer(),
            $this->getFileSystemServiceFromContainer()
        );

        $yamlFileStorage->save(['testData']);

        $this->assertSame(
            ['testData'],
            $yamlFileStorage->get()
        );
    }

    public function testCreatesNewDirectoryAndFileIfDoNotExist()
    {
        $filePath = $this->getFilePath();
        unlink($filePath);

        $filePath = $this->getFilePath() . '/fileInNonExistentDirectory.yml';

        $yamlFileStorage = new YamlFileStorage(
            new FileLocator(),
            $filePath,
            $this->getLockFactoryFromContainer(),
            $this->getFileSystemServiceFromContainer()
        );

        $yamlFileStorage->save(['testData']);

        $this->assertSame(
            ['testData'],
            $yamlFileStorage->get()
        );
    }

    /**
     * @expectedException \Symfony\Component\Yaml\Exception\ParseException
     */
    public function testStorageWithCorruptedFile()
    {
        $filePath = $this->getFilePath();
        $yamlContent = "\t";

        file_put_contents($filePath, $yamlContent);

        $yamlFileStorage = new YamlFileStorage(
            new FileLocator(),
            $filePath,
            $this->getLockFactoryFromContainer(),
            $this->getFileSystemServiceFromContainer()
        );

        $yamlFileStorage->get();
    }

    public function testStorageWithEmptyFile()
    {
        $filePath = $this->getFilePath();

        file_put_contents($filePath, '');

        $yamlFileStorage = new YamlFileStorage(
            new FileLocator(),
            $filePath,
            $this->getLockFactoryFromContainer(),
            $this->getFileSystemServiceFromContainer()
        );

        $this->assertSame(
            [],
            $yamlFileStorage->get()
        );
    }

    public function testEmptyYamlArrayThrowsNoError()
    {
        $yaml = '[]';

        file_put_contents($this->getFilePath(), $yaml);

        $yamlFileStorage = new YamlFileStorage(
            new FileLocator(),
            $this->getFilePath(),
            $this->getLockFactoryFromContainer(),
            $this->getFileSystemServiceFromContainer()
        );
        $parsedYaml = $yamlFileStorage->get();

        $this->assertEquals([], $parsedYaml);
    }

    /**
     * @return string
     */
    private function getFilePath(): string
    {
        if ($this->tempFileHandle === null) {
            $this->tempFileHandle = tmpfile();
        }

        return stream_get_meta_data($this->tempFileHandle)['uri'];
    }

    /**
     * @return Factory
     */
    private function getLockFactoryFromContainer(): Factory
    {
        /** @var Factory $lockFactory */
        $lockFactory = $this->get('oxid_esales.common.storage.flock_store_lock_factory');

        return $lockFactory;
    }

    private function getFileSystemServiceFromContainer(): Filesystem
    {
        return $this->get('oxid_esales.symfony.file_system');
    }
}
