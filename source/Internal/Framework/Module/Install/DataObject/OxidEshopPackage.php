<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject;

class OxidEshopPackage
{
    /**
     * Package path is the absolute path to the root directory, e.g. /var/www/oxideshop/vendor/oxid-esales/paypal-module
     *
     * @var string
     */
    private $packagePath;

    /**
     * @param string $packagePath
     */
    public function __construct(string $packagePath)
    {
        $this->packagePath = $packagePath;
    }

    /**
     * @return string
     */
    public function getPackagePath(): string
    {
        return $this->packagePath;
    }
}
