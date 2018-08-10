<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */


namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper;


use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ProjectConfiguration;

/**
 * Class ProjectConfigurationDataMapper
 *
 * @package OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper
 */
class ProjectConfigurationDataMapper
{
    /**
     * @var ProjectConfiguration
     */
    private $projectConfiguration;
    /**
     * @var EnvironmentConfigurationMapper
     */
    private $environmentConfigurationMapper;
    /**
     * @var ShopConfigurationMapper
     */
    private $shopConfigurationMapper;
    /**
     * @var ModuleConfigurationMapper
     */
    private $moduleConfigurationMapper;
    /**
     * @var ModuleSettingMapper
     */
    private $moduleSettingMapper;

    /**
     * ProjectConfigurationDataMapper constructor.
     *
     * @param ProjectConfiguration           $configuration
     * @param EnvironmentConfigurationMapper $environmentConfigurationMapper
     * @param ShopConfigurationMapper        $shopConfigurationMapper
     * @param ModuleConfigurationMapper      $moduleConfigurationMapper
     * @param ModuleSettingMapper            $moduleSettingMapper
     */
    public function __construct(
        ProjectConfiguration $configuration,
        EnvironmentConfigurationMapper $environmentConfigurationMapper,
        ShopConfigurationMapper $shopConfigurationMapper,
        ModuleConfigurationMapper $moduleConfigurationMapper,
        ModuleSettingMapper $moduleSettingMapper
    )
    {
        $this->projectConfiguration = $configuration;
        $this->environmentConfigurationMapper = $environmentConfigurationMapper;
        $this->shopConfigurationMapper = $shopConfigurationMapper;
        $this->moduleConfigurationMapper = $moduleConfigurationMapper;
        $this->moduleSettingMapper = $moduleSettingMapper;
    }

    /**
     * @param array $data
     *
     * @return ProjectConfiguration
     */
    public function fromData(array $data): ProjectConfiguration
    {
        foreach ($data['environments'] as $environmentName => $environmentData) {

            $environmentConfiguration = $this->environmentConfigurationMapper->fromData(
                $environmentData,
                $this->shopConfigurationMapper,
                $this->moduleConfigurationMapper,
                $this->moduleSettingMapper
            );

            $this->projectConfiguration->setEnvironmentConfiguration($environmentName, $environmentConfiguration);
        }

        return $this->projectConfiguration;
    }

    /**
     * @return array
     */
    public function toData(): array
    {
        return [];
    }
}