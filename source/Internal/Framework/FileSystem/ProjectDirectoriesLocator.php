<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem;

use Symfony\Component\Filesystem\Path;

readonly class ProjectDirectoriesLocator
{
    private string $projectRoot;

    public function __construct()
    {
        $this->projectRoot = (new ProjectRootLocator())->getProjectRoot();
    }

    public function getRootPath(): string
    {
        return $this->projectRoot;
    }

    public function getSourcePath(): string
    {
        return Path::join($this->projectRoot, 'source');
    }

    public function getVendorPath(): string
    {
        return Path::join($this->projectRoot, 'vendor');
    }

    public function getOutPath(): string
    {
        return Path::join($this->getSourcePath(), 'out');
    }
}
