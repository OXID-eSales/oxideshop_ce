<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Install;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;

/**
 * @internal
 */
class DefaultProjectConfigurationGenerator implements DefaultProjectConfigurationGeneratorInterface
{
    /**
     * @var ProjectConfigurationDaoInterface
     */
    private $projectConfigurationDao;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * DefaultProjectConfigurationGenerator constructor.
     * @param ProjectConfigurationDaoInterface $projectConfigurationDao
     * @param ContextInterface                 $context
     */
    public function __construct(ProjectConfigurationDaoInterface $projectConfigurationDao, ContextInterface $context)
    {
        $this->projectConfigurationDao = $projectConfigurationDao;
        $this->context = $context;
    }

    /**
     * Generates default project configuration.
     */
    public function generate()
    {
        $projectConfiguration = new ProjectConfiguration();

        $projectConfiguration->addEnvironmentConfiguration(
            $this->context->getEnvironment(),
            $this->createEnvironmentConfiguration()
        );

        $this->projectConfigurationDao->persistConfiguration($projectConfiguration);
    }

    /**
     * @return EnvironmentConfiguration
     */
    private function createEnvironmentConfiguration(): EnvironmentConfiguration
    {
        $environmentConfiguration = new EnvironmentConfiguration();

        foreach ($this->getShopIds() as $shopId) {
            $environmentConfiguration->addShopConfiguration($shopId, new ShopConfiguration());
        }

        return $environmentConfiguration;
    }

    /**
     * @return array
     */
    private function getShopIds(): array
    {
        if ($this->context->isShopSetUp()) {
            return $this->context->getAllShopIds();
        }

        return [
            $this->context->getDefaultShopId(),
        ];
    }
}
