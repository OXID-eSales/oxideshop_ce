<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject;

class OxidEshopPackage
{
    public function __construct(private string $packagePath)
    {
    }

    /**
     * @return string
     */
    public function getPackagePath(): string
    {
        return $this->packagePath;
    }
}
