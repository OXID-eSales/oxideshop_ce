<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\BootstrapLocator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

trait FilesystemTrait
{
    private Filesystem $filesystem;
    private string $varPath = '';
    private string $varBackupPath = '';

    public function backupVarDirectory(): void
    {
        $this->init();
        $this->filesystem->mirror($this->varPath, $this->varBackupPath);
    }

    public function restoreVarDirectory(): void
    {
        $this->filesystem->remove($this->varPath);
        $this->filesystem->mirror($this->varBackupPath, $this->varPath);
        $this->filesystem->remove($this->varBackupPath);
    }

    private function init(): void
    {
        $shopRootPath = (new BootstrapLocator())->getProjectRoot();
        $this->filesystem = new Filesystem();
        $this->varPath = Path::join($shopRootPath, 'var');
        $this->varBackupPath = Path::join(
            $shopRootPath,
            uniqid('var.backup.', true)
        );
    }
}
