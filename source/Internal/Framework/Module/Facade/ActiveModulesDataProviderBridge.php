<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Controller;

class ActiveModulesDataProviderBridge implements ActiveModulesDataProviderBridgeInterface
{
    public function __construct(private ActiveModulesDataProviderInterface $activeModulesDataProvider)
    {
    }

    /**
     * @inheritDoc
     */
    public function getModuleIds(): array
    {
        return $this->activeModulesDataProvider->getModuleIds();
    }

    /**
     * @inheritDoc
     */
    public function getModulePaths(): array
    {
        return $this->activeModulesDataProvider->getModulePaths();
    }

    /**
     * @return Controller[]
     */
    public function getControllers(): array
    {
        return $this->activeModulesDataProvider->getControllers();
    }

    public function getClassExtensions(): array
    {
        return $this->activeModulesDataProvider->getClassExtensions();
    }
}
