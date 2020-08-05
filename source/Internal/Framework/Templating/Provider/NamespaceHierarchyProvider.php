<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Provider;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class NamespaceHierarchyProvider implements NamespaceHierarchyProviderInterface
{
    /** @var ShopConfigurationDaoInterface */
    private $shopConfigurationDao;
    /** @var ContextInterface */
    private $context;

    public function __construct(
        ShopConfigurationDaoInterface $shopConfigurationDao,
        ContextInterface $context
    ) {
        $this->shopConfigurationDao = $shopConfigurationDao;
        $this->context = $context;
    }

    /** @inheritDoc */
    public function getHierarchyAscending(): array
    {
        $moduleHierarchy = $this->shopConfigurationDao
            ->get($this->context->getCurrentShopId())
            ->getModuleIdsOfModuleConfigurations();

        return array_reverse($moduleHierarchy);
    }
}
