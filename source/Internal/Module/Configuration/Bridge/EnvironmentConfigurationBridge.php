<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\EnvironmentConfiguration;

/**
 * @internal
 */
class EnvironmentConfigurationBridge implements EnvironmentConfigurationBridgeInterface
{
    /**
     * @var BasicContextInterface
     */
    private $context;

    /**
     * @var ProjectConfigurationDaoInterface
     */
    private $projectConfigurationDao;

    /**
     * @param BasicContextInterface            $context
     * @param ProjectConfigurationDaoInterface $projectConfigurationDao
     */
    public function __construct(
        BasicContextInterface $context,
        ProjectConfigurationDaoInterface $projectConfigurationDao
    ) {
        $this->context = $context;
        $this->projectConfigurationDao = $projectConfigurationDao;
    }

    /**
     * @return EnvironmentConfiguration
     */
    public function get(): EnvironmentConfiguration
    {
        return $this
            ->projectConfigurationDao
            ->getConfiguration()
            ->getEnvironmentConfiguration(
                $this->context->getEnvironment()
            );
    }

    /**
     * @param EnvironmentConfiguration $environmentConfiguration
     */
    public function save(EnvironmentConfiguration $environmentConfiguration)
    {
        $projectConfiguration = $this->projectConfigurationDao->getConfiguration();
        $projectConfiguration->addEnvironmentConfiguration(
            $this->context->getEnvironment(),
            $environmentConfiguration
        );
        $this->projectConfigurationDao->persistConfiguration($projectConfiguration);
    }
}
