<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Storage;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Lock\LockFactory;

class YamlFileStorageFactory implements FileStorageFactoryInterface
{
    /**
     * @var FileLocatorInterface
     */
    private $fileLocator;

    /**
     * @var LockFactory
     */
    private $lockFactory;

    /**
     * @var Filesystem
     */
    private $filesystemService;

    /**
     * YamlFileStorageFactory constructor.
     * @param FileLocatorInterface $fileLocator
     * @param LockFactory $lockFactory
     * @param Filesystem $filesystemService
     */
    public function __construct(FileLocatorInterface $fileLocator, LockFactory $lockFactory, Filesystem $filesystemService)
    {
        $this->fileLocator = $fileLocator;
        $this->lockFactory = $lockFactory;
        $this->filesystemService = $filesystemService;
    }


    public function create(string $filePath): ArrayStorageInterface
    {
        return new YamlFileStorage(
            $this->fileLocator,
            $filePath,
            $this->lockFactory,
            $this->filesystemService
        );
    }
}
