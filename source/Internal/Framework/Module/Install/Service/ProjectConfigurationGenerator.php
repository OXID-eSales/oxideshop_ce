<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;

class ProjectConfigurationGenerator implements ProjectConfigurationGeneratorInterface
{
    public function __construct(
        private ProjectConfigurationDaoInterface $projectConfigurationDao,
        private BasicContextInterface $context
    ) {
    }

    /**
     * Generates default project configuration.
     */
    public function generate(): void
    {
        $this->projectConfigurationDao->save($this->createProjectConfiguration());
    }

    /**
     * @return ProjectConfiguration
     */
    private function createProjectConfiguration(): ProjectConfiguration
    {
        $projectConfiguration = new ProjectConfiguration();

        foreach ($this->context->getAllShopIds() as $shopId) {
            $projectConfiguration->addShopConfiguration($shopId, new ShopConfiguration());
        }

        return $projectConfiguration;
    }
}
