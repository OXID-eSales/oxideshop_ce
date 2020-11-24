<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Facade;

class ActiveModulesDataProviderBridge implements ActiveModulesDataProviderBridgeInterface
{
    /**
     * @var ActiveModulesDataProviderInterface
     */
    private $activeModulesDataProvider;

    public function __construct(ActiveModulesDataProviderInterface $activeModulesDataProvider)
    {
        $this->activeModulesDataProvider = $activeModulesDataProvider;
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
     * @inheritDoc
     */
    public function getTemplates(): array
    {
        return $this->activeModulesDataProvider->getTemplates();
    }
}
