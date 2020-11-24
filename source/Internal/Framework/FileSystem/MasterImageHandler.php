<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class MasterImageHandler implements MasterImageHandlerInterface
{
    /** @var Filesystem */
    private $filesystem;

    public function __construct(
        Filesystem $filesystem
    ) {
        $this->filesystem = $filesystem;
    }

    public function copy(string $source, string $destination): void
    {
        $this->filesystem->copy($source, $destination, true);
        $this->filesystem->chmod($destination, 0644);
    }

    public function upload(string $source, string $destination): void
    {
        $destinationDirectory = \dirname($destination);
        $this->filesystem->mkdir($destinationDirectory, 0744);
        if ($source !== $destination && !move_uploaded_file($source, $destination)) {
            throw new IOException('Can not move uploaded file');
        }
        $this->filesystem->chmod($destination, 0644);
    }

    public function remove(string $path): void
    {
        $this->filesystem->remove($path);
    }

    public function exists(string $path): bool
    {
        return $this->filesystem->exists($path);
    }
}
