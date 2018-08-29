<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Common\Storage;

use OxidEsales\EshopCommunity\Internal\Common\Storage\YamlFileStorage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;

/**
 * @internal
 */
class YamlFileStorageTest extends TestCase
{
    /**
     * @var resource
     */
    private $tempFileHandle = null;

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
            $this->getFilePath()
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
    public function testinvalidYamlFileThrowsParseException()
    {
        $yaml = "\t";

        file_put_contents($this->getFilePath(), $yaml);

        $yamlFileStorage = new YamlFileStorage(
            new FileLocator(),
            $this->getFilePath()
        );
        $yamlFileStorage->get();
    }

    /**
     * @expectedException \TypeError
     */
    public function testEmptyYamlFileThrowsTypeError()
    {
        $yaml = "";

        file_put_contents($this->getFilePath(), $yaml);

        $yamlFileStorage = new YamlFileStorage(
            new FileLocator(),
            $this->getFilePath()
        );
        $yamlFileStorage->get();
    }

    public function testEmptyYamlArrayThrowsNoError()
    {
        $yaml = "[]";

        file_put_contents($this->getFilePath(), $yaml);

        $yamlFileStorage = new YamlFileStorage(
            new FileLocator(),
            $this->getFilePath()
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
}
