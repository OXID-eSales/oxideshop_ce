<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Provider;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Service\ActiveModulesDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class NamespaceHierarchyProvider implements NamespaceHierarchyProviderInterface
{
    /**
     * @var ActiveModulesDataProviderInterface
     */
    private $activeModulesDataProvider;

    public function __construct(ActiveModulesDataProviderInterface $activeModulesDataProvider)
    {
        $this->activeModulesDataProvider = $activeModulesDataProvider;
    }

    /** @inheritDoc */
    public function getHierarchyAscending(): array
    {
        return $this->activeModulesDataProvider->getModuleIds();
    }
}
