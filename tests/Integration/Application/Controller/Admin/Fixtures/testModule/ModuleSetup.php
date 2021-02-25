<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin\Fixtures\testModule;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin\Fixtures\testModule\RendererInterface;

class ModuleSetup
{
    /**
     * Activation function for the module
     */
    public static function onActivate(): void
    {
        ContainerFactory::getInstance()->getContainer()->get(RendererInterface::class);
    }

    /**
     * Deactivation function for the module
     */
    public static function onDeactivate(): void
    {
    }
}