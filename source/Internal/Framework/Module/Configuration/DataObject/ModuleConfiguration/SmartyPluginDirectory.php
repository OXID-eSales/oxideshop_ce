<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

class SmartyPluginDirectory
{
    /**
     * @var array
     */
    private $directory;


    /**
     * @param string $directory
     */
    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }
}
