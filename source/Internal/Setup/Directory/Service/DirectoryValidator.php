<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Directory\Service;

use OxidEsales\EshopCommunity\Internal\Setup\Directory\Exception\DirectoryValidatorException;

/**
 * Class DirectoryValidator
 *
 * @package OxidEsales\EshopCommunity\Internal\Setup\Directory
 */
class DirectoryValidator implements DirectoryValidatorInterface
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
     * @param string $shopSourcePath
     * @param string $compileDirectory
     *
     * @throws DirectoryValidatorException
     */
    public function validateDirectory(string $shopSourcePath, string $compileDirectory): void
    {
        $directories = $this->getDirectories($shopSourcePath, $compileDirectory);

        $nonExistentDirectories = $this->checkDirectoriesExistent($directories);
        if (count($nonExistentDirectories) > 0) {
            throw new DirectoryValidatorException(
                DirectoryValidatorException::NON_EXISTENCE_DIRECTORY . ': ' . implode(', ', $nonExistentDirectories)
            );
        }

        $noPermissionDirectories = $this->checkDirectoriesPermission($directories);
        if (count($noPermissionDirectories) > 0) {
            throw new DirectoryValidatorException(
                DirectoryValidatorException::NO_PERMISSION_DIRECTORY . ': ' . implode(', ', $noPermissionDirectories)
            );
        }
    }

    /**
     * @param array $directories
     *
     * @return array
     */
    private function checkDirectoriesExistent(array $directories): array
    {
        $nonExistentDirectories = [];
        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                $nonExistentDirectories[] = $directory;
            }
        }

        return $nonExistentDirectories;
    }

    /**
     * @param array $directories
     *
     * @return array
     */
    private function checkDirectoriesPermission(array $directories): array
    {
        $subDirectories = $this->getDirectoriesAndSubDirectories($directories);

        $noPermissionDirectories = [];
        foreach ($subDirectories as $subDirectory) {
            if (!is_readable($subDirectory) || !is_writable($subDirectory)) {
                $noPermissionDirectories[] = $subDirectory;
            }
        }

        return $noPermissionDirectories;
    }

    /**
     * @param array $directories
     *
     * @return array
     */
    private function getDirectoriesAndSubDirectories(array $directories): array
    {
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

    /**
     * @param string $shopSourcePath
     * @param string $compileDirectory
     *
     * @return array
     */
    private function getDirectories(string $shopSourcePath, string $compileDirectory): array
    {
        $shopSourcePath = $this->checkPathHasSlashAtTheEnd($shopSourcePath);

        $directories = array_map(
            static function ($value) use ($shopSourcePath) {
                return $shopSourcePath . $value;
            },
            self::DIRECTORIES_LIST
        );

        $directories[] = $this->checkPathHasSlashAtTheEnd($compileDirectory);

        return $directories;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function checkPathHasSlashAtTheEnd(string $path): string
    {
        $lastChar = substr($path, -1);

        if ($lastChar !== DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }

        return $path;
    }
}
