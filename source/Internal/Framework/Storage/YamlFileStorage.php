<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Storage;

use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Yaml\Yaml;

class YamlFileStorage implements ArrayStorageInterface
{
    public function __construct(
        private FileLocatorInterface $fileLocator,
        private string $filePath,
        private LockFactory $lockFactory,
        private Filesystem $filesystemService
    ) {
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
    public function save(array $data): void
    {
        $lock = $this->lockFactory->createLock($this->getLockId());

        if ($lock->acquire(true)) {
            try {
                file_put_contents(
                    $this->getLocatedFilePath(),
                    Yaml::dump($data, 10, 2)
                );
            } finally {
                $lock->release();
            }
        }
    }

    /**
     * @return string
     */
    private function getLocatedFilePath(): string
    {
        try {
            $filePath = $this->fileLocator->locate($this->filePath);
        } catch (FileLocatorFileNotFoundException) {
            $this->createFileDirectory();
            $this->createFile();
            $filePath = $this->fileLocator->locate($this->filePath);
        }

        return $filePath;
    }

    /**
     * Creates file directory if it doesn't exist.
     */
    private function createFileDirectory(): void
    {
        if (!$this->filesystemService->exists(\dirname($this->filePath))) {
            $this->filesystemService->mkdir(\dirname($this->filePath));
        }
    }

    /**
     * Creates file.
     */
    private function createFile(): void
    {
        $this->filesystemService->touch($this->filePath);
    }

    /**
     * @return string
     */
    private function getLockId(): string
    {
        return md5($this->filePath);
    }
}
