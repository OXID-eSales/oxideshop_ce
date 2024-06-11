<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Controller;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ShopConfigurationNotFoundException;
use Psr\Log\LoggerInterface;

class ActiveModulesDataProviderBridge implements ActiveModulesDataProviderBridgeInterface
{
    private array $chain;

    public function __construct(
        private readonly ActiveModulesDataProviderInterface $activeModulesDataProvider,
        private readonly LoggerInterface $logger
    ) {
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
        if (isset($this->chain)) {
            return $this->chain;
        }

        try {
            $this->chain = $this->activeModulesDataProvider->getClassExtensions();
        } catch (ShopConfigurationNotFoundException $exception) {
            $this->chain = [];
            $this->logger->error($exception->getMessage(), [$exception]);
        }

        return $this->chain;
    }
}
