<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject;

class FilesystemConfiguration
{
    private string $installationDirectory;
    private string $tmpDirectory;

    public function getInstallationDirectory(): string
    {
        return $this->installationDirectory;
    }

    public function setInstallationDirectory(string $installationDirectory): void
    {
        $this->installationDirectory = $installationDirectory;
    }

    public function getTmpDirectory(): string
    {
        return $this->tmpDirectory;
    }

    public function setTmpDirectory(string $tmpDirectory): void
    {
        $this->tmpDirectory = $tmpDirectory;
    }
}
