<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Directory\Service;

use OxidEsales\EshopCommunity\Internal\Setup\Directory\Exception\DirectoryException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\Facts\Config\ConfigFile;

/**
 * Class DirectoryService
 *
 * @package OxidEsales\EshopCommunity\Internal\Setup\Directory
 */
class DirectoryService implements DirectoryServiceInterface
{
    private const DIRECTORIES_LIST = [
        'out/pictures/promo/',
        'out/pictures/master/',
        'out/pictures/generated/',
        'out/pictures/media/',
        'out/media/',
        'log/',
        '../var/'
    ];

    /**
     * @var array $directories
     */
    private $directories;

    /** @var BasicContextInterface */
    private $context;

    /**
     * DirectoryService constructor.
     *
     * @param BasicContextInterface $context
     */
    public function __construct(BasicContextInterface $context)
    {
        $this->context = $context;
        $this->setDirectories();
    }

    /**
     * @throws DirectoryException
     */
    public function checkDirectoriesExistent(): void
    {
        $nonExistentDirectories = [];
        foreach ($this->directories as $directory) {
            if (!is_dir($directory)) {
                $nonExistentDirectories[] = $directory;
            }
        }

        if (count($nonExistentDirectories) > 0) {
            throw new DirectoryException(
                DirectoryException::NON_EXISTENCE_DIRECTORY . ': ' . implode(', ', $nonExistentDirectories)
            );
        }
    }

    /**
     * @throws DirectoryException
     */
    public function checkDirectoriesPermission(): void
    {
        $noPermissionDirectories = [];
        foreach ($this->getDirectoriesAndSubDirectories() as $directory) {
            if (!is_readable($directory) || !is_writable($directory)) {
                $noPermissionDirectories[] = $directory;
            }
        }

        if (count($noPermissionDirectories) > 0) {
            throw new DirectoryException(
                DirectoryException::NO_PERMISSION_DIRECTORY . ': ' . implode(', ', $noPermissionDirectories)
            );
        }
    }

    /**
     * @return array
     */
    private function getDirectoriesAndSubDirectories(): array
    {
        $directories = $this->directories;

        $directory = reset($directories);
        while ($directory) {
            $subDirectories = glob($directory . '*', GLOB_ONLYDIR);

            if (is_array($subDirectories)) {
                foreach ($subDirectories as $subDirectory) {
                    $directories[] = $subDirectory . DIRECTORY_SEPARATOR;
                }
            }

            $directory = next($directories);
        }

        return $directories;
    }

    private function setDirectories(): void
    {
        $shopPath = $this->context->getSourcePath();

        $tmpPath = "$shopPath/tmp/";
        $configFile = new ConfigFile();
        $configTmpPath = $configFile->getVar('sCompileDir');
        if ($configTmpPath) {
            $tmpPath = $configTmpPath;
            $lastChar = substr($tmpPath, -1);
            if ($lastChar !== DIRECTORY_SEPARATOR) {
                $tmpPath .= DIRECTORY_SEPARATOR;
            }
        }

        $this->directories = self::DIRECTORIES_LIST;
        $this->directories = array_map(
            static function ($value) use ($shopPath) {
                return $shopPath . DIRECTORY_SEPARATOR . $value;
            },
            $this->directories
        );
        $this->directories[] = $tmpPath;
    }
}
