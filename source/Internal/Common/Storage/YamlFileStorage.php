<?php
declare(strict_types = 1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\Storage;

use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlFileDao
 */
class YamlFileStorage implements ArrayStorageInterface
{
    /**
     * @var FileLocatorInterface
     */
    private $fileLocator;

    /**
     * @var string
     */
    private $filePath;

    /**
     * YamlFileStorage constructor.
     * @param FileLocatorInterface $fileLocator
     * @param string               $filePath
     */
    public function __construct(FileLocatorInterface $fileLocator, string $filePath)
    {
        $this->fileLocator = $fileLocator;
        $this->filePath = $filePath;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $fileContent = file_get_contents($this->getLocatedFilePath());

        $yaml = Yaml::parse(
            $fileContent
        );

        return $yaml ?? [];
    }

    /**
     * @param array $data
     */
    public function save(array $data)
    {
        file_put_contents(
            $this->getLocatedFilePath(),
            Yaml::dump($data, 10, 2)
        );
    }

    /**
     * @return string
     */
    private function getLocatedFilePath(): string
    {
        try {
            $filePath = $this->fileLocator->locate($this->filePath);
        } catch (FileLocatorFileNotFoundException $exception) {
            $this->createFile();
            $filePath = $this->fileLocator->locate($this->filePath);
        }

        return $filePath;
    }

    /**
     * Creates file.
     */
    private function createFile()
    {
        touch($this->filePath);
    }
}
