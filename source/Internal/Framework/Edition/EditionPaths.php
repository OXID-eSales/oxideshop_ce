<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Edition;

enum EditionPaths: string
{
    case Community = Edition::Community->value;
    case Professional = Edition::Professional->value;
    case Enterprise = Edition::Enterprise->value;

    public function getVendorFolderName(): string
    {
        return 'oxid-esales';
    }

    public function getProjectFolderName(): string
    {
        return match ($this) {
            self::Community => 'oxideshop-ce',
            self::Professional => 'oxideshop-pe',
            self::Enterprise => 'oxideshop-ee',
        };
    }

    public function getSourceFolderName(): string
    {
        return match ($this) {
            self::Community => 'source',
            default => '',
        };
    }
}
