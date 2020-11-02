<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Container;

use Psr\Container\ContainerInterface;

class BootstrapContainerFactory
{
    /**
     * This is a minimal container that does not need the shop
     * to be installed.
     */
    public static function getBootstrapContainer(): ContainerInterface
    {
        $symfonyContainer = (new BootstrapContainerBuilder())->create();
        $symfonyContainer->compile();

        return $symfonyContainer;
    }
}
