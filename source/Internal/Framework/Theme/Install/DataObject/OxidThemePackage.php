<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\DataObject;

readonly class OxidThemePackage
{
    public function __construct(private string $packagePath)
    {
    }

    public function getPackagePath(): string
    {
        return $this->packagePath;
    }
}
