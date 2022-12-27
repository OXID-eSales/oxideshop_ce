<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Directory\Service;

use OxidEsales\EshopCommunity\Internal\Setup\Directory\Exception\NonExistenceDirectoryException;
use OxidEsales\EshopCommunity\Internal\Setup\Directory\Exception\NoPermissionDirectoryException;
use OxidEsales\EshopCommunity\Internal\Setup\Directory\Exception\NotAbsolutePathException;
use Symfony\Component\Filesystem\Path;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class DirectoryValidator implements DirectoryValidatorInterface
{
    private const DIRECTORIES_LIST = [
        'out/pictures/promo/',
        'out/pictures/master/',
        'out/pictures/generated/',
        'out/pictures/media/',
        'out/media/',
        'log/',
        '../var/',
    ];

    /**
     * @param string $shopSourcePath
     * @param string $compileDirectory
     *
     * @throws NoPermissionDirectoryException
     * @throws NonExistenceDirectoryException
     */
    public function validateDirectory(string $shopSourcePath, string $compileDirectory): void
    {
        $directories = $this->getDirectories($shopSourcePath, $compileDirectory);

        $this->checkDirectoriesExistent($directories);

        $this->checkDirectoriesPermission($directories);
    }

    /**
     * @param string $shopSourcePath
     * @param string $compileDirectory
     *
     * @throws NotAbsolutePathException
     */
    public function checkPathIsAbsolute(string $shopSourcePath, string $compileDirectory): void
    {
        if (
            !Path::isAbsolute($shopSourcePath) ||
            !Path::isAbsolute($compileDirectory)
        ) {
            throw new NotAbsolutePathException(NotAbsolutePathException::NOT_ABSOLUTE_PATHS);
        }
    }

    /**
     * @param array $directories
     *
     * @return void
     * @throws NonExistenceDirectoryException
     */
    private function checkDirectoriesExistent(array $directories): void
    {
        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                throw new NonExistenceDirectoryException(
                    NonExistenceDirectoryException::NON_EXISTENCE_DIRECTORY . ': ' . $directory
                );
            }
        }
    }

    /**
     * @param array $directories
     *
     * @return void
     * @throws NoPermissionDirectoryException
     */
    private function checkDirectoriesPermission(array $directories): void
    {
        $subDirectories = $this->getDirectoriesAndSubDirectories($directories);

        foreach ($subDirectories as $subDirectory) {
            if (!is_readable($subDirectory) || !is_writable($subDirectory)) {
                throw new NoPermissionDirectoryException(
                    NoPermissionDirectoryException::NO_PERMISSION_DIRECTORY . ': ' . $subDirectory
                );
            }
        }
    }

    /**
     * @param array $directories
     *
     * @return array
     */
    private function getDirectoriesAndSubDirectories(array $directories): array
    {
        foreach ($directories as $directory) {
            $recursiveIterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST,
                RecursiveIteratorIterator::CATCH_GET_CHILD
            );

            foreach ($recursiveIterator as $path => $dir) {
                if ($dir->isDir()) {
                    $directories[] = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
                }
            }
        }

        return $directories;
    }

    /**
     * @param string $shopSourcePath
     * @param string $compileDirectory
     *
     * @return array
     */
    private function getDirectories(string $shopSourcePath, string $compileDirectory): array
    {
        $shopSourcePath = rtrim($shopSourcePath, DIRECTORY_SEPARATOR) .  DIRECTORY_SEPARATOR;

        $directories = array_map(
            static function ($value) use ($shopSourcePath) {
                return $shopSourcePath . $value;
            },
            self::DIRECTORIES_LIST
        );

        $directories[] = rtrim($compileDirectory, DIRECTORY_SEPARATOR) .  DIRECTORY_SEPARATOR;

        return $directories;
    }
}
