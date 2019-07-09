<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\Storage;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Lock\Factory;

/**
 * @internal
 */
class YamlFileStorageFactory implements FileStorageFactoryInterface
{
    /**
     * @var FileLocatorInterface
     */
    private $fileLocator;

    /**
     * @var Factory
     */
    private $lockFactory;

    /**
     * @var Filesystem
     */
    private $filesystemService;

    /**
     * YamlFileStorageFactory constructor.
     * @param FileLocatorInterface $fileLocator
     * @param Factory $lockFactory
     * @param Filesystem $filesystemService
     */
    public function __construct(FileLocatorInterface $fileLocator, Factory $lockFactory, Filesystem $filesystemService)
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
