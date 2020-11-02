<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ExtensionChainServiceInterface;

class ClassExtensionChainBridge implements ClassExtensionChainBridgeInterface
{
    /**
     * @var ExtensionChainServiceInterface
     */
    private $classExtensionChainService;

    /**
     * ClassExtensionChainBridge constructor.
     */
    public function __construct(ExtensionChainServiceInterface $classExtensionChainService)
    {
        $this->classExtensionChainService = $classExtensionChainService;
    }

    public function updateChain(int $shopId): void
    {
        $this->classExtensionChainService->updateChain($shopId);
        Registry::getConfig()->reinitialize();
    }
}
