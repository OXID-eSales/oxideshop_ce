<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

class MasterImageHandler implements MasterImageHandlerInterface
{
    /** @var Filesystem */
    private $filesystem;
    /** @var ContextInterface */
    private $context;

    public function __construct(
        Filesystem $filesystem,
        ContextInterface $context
    ) {
        $this->filesystem = $filesystem;
        $this->context = $context;
    }

    public function copy(string $source, string $destination): void
    {
        $destinationPath = $this->getAbsolutePath($destination);
        $this->filesystem->copy($source, $destinationPath, true);
        $this->filesystem->chmod($destinationPath, 0644);
    }

    public function upload(string $source, string $destination): void
    {
        $destinationPath = $this->getAbsolutePath($destination);
        $destinationDirectory = \dirname($destinationPath);
        $this->filesystem->mkdir($destinationDirectory, 0744);
        if ($source !== $destinationPath && !move_uploaded_file($source, $destinationPath)) {
            throw new IOException('Can not move uploaded file');
        }
        $this->filesystem->chmod($destinationPath, 0644);
    }

    public function remove(string $path): void
    {
        $this->filesystem->remove($this->getAbsolutePath($path));
    }

    public function exists(string $path): bool
    {
        return $this->filesystem->exists($this->getAbsolutePath($path));
    }

    private function getAbsolutePath(string $path): string
    {
        return Path::join($this->context->getSourcePath(), $path);
    }
}
