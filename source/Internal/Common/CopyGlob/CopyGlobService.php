<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\CopyGlob;

use OxidEsales\ComposerPlugin\Utilities\CopyFileManager\GlobMatcher\GlobMatcher;
use OxidEsales\ComposerPlugin\Utilities\CopyFileManager\GlobMatcher\Iteration\BlacklistFilterIterator;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

/**
 * Class CopyGlobService
 *
 * @internal
 *
 * @package OxidEsales\EshopCommunity\Internal\Module\Setup\Install
 */
class CopyGlobService implements CopyGlobServiceInterface
{
    /**
     * Copy files/directories from source to destination.
     *
     * @param string $sourcePath         Absolute path to file or directory.
     * @param string $destinationPath    Absolute path to file or directory.
     * @param array  $globExpressionList List of glob expressions, e.g. ["*.txt", "*.pdf"].
     *
     * @throws \InvalidArgumentException If given $sourcePath is not a file.
     *
     */
    public function copy(string $sourcePath, string $destinationPath, array $globExpressionList = [])
    {
        if (!file_exists($sourcePath)) {
            $message = "Given value \"$sourcePath\" is not a valid source path entry. ".
                       "Valid entry must be an absolute path to an existing file or directory.";

            throw new \InvalidArgumentException($message);
        }

        if (is_dir($sourcePath)) {
            $this->copyDirectory($sourcePath, $destinationPath, $globExpressionList);
        } else {
            $this->copyFile($sourcePath, $destinationPath, $globExpressionList);
        }
    }

    /**
     * Copy whole directory using given glob filters.
     *
     * @param string $sourcePath         Absolute path to directory.
     * @param string $destinationPath    Absolute path to directory.
     * @param array  $globExpressionList List of glob expressions, e.g. ["*.txt", "*.pdf"].
     */
    private function copyDirectory($sourcePath, $destinationPath, $globExpressionList)
    {
        $filesystem = new FileSystem();

        $flatFileListIterator = $this->getFlatFileListIterator($sourcePath);
        $filteredFileListIterator = new BlacklistFilterIterator(
            $flatFileListIterator,
            $sourcePath,
            $globExpressionList
        );

        $filesystem->mirror($sourcePath, $destinationPath, $filteredFileListIterator, ["override" => true]);
    }

    /**
     * Returns relative path from an absolute path to a file.
     *
     * @param string $sourcePath Absolute path to a file.
     *
     * @return string
     */
    private static function getRelativePathForSingleFile($sourcePath)
    {
        return Path::makeRelative($sourcePath, Path::getDirectory($sourcePath));
    }

    /**
     * Copy file using given glob filters.
     *
     * @param string $sourcePathOfFile   Absolute path to file.
     * @param string $destinationPath    Absolute path to directory.
     * @param array  $globExpressionList List of glob expressions, e.g. ["*.txt", "*.pdf"].
     */
    private function copyFile($sourcePathOfFile, $destinationPath, $globExpressionList)
    {
        $filesystem = new Filesystem();

        $relativeSourcePath = $this->getRelativePathForSingleFile($sourcePathOfFile);

        if (!GlobMatcher::matchAny($relativeSourcePath, $globExpressionList)) {
            $filesystem->copy($sourcePathOfFile, $destinationPath, ["override" => true]);
        }
    }

    /**
     * Return an iterator which iterates through a given directory tree in a one-dimensional fashion.
     *
     * Consider the following file/directory structure as an example:
     *
     *   * directory_a
     *     * file_a_a
     *   * directory_b
     *     * file_b_a
     *     * file_b_b
     *   * file_c
     *
     * RecursiveDirectoryIterator would iterate through:
     *   * directory_a [iterator]
     *   * directory_b [iterator]
     *   * file_c [SplFileInfo]
     *
     * In contrast current method would iterate through:
     *   * directory_a [SplFileInfo]
     *   * directory_a/file_a_a [SplFileInfo]
     *   * directory_b [SplFileInfo]
     *   * directory_b/file_b_a [SplFileInfo]
     *   * directory_b/file_b_b [SplFileInfo]
     *   * file_c [SplFileInfo]
     *
     * @param string $sourcePath Absolute path to directory.
     *
     * @return \Iterator
     */
    private static function getFlatFileListIterator($sourcePath)
    {
        $recursiveFileIterator = new \RecursiveDirectoryIterator($sourcePath, \FilesystemIterator::SKIP_DOTS);
        $flatFileListIterator = new \RecursiveIteratorIterator($recursiveFileIterator);
        return $flatFileListIterator;
    }
}
