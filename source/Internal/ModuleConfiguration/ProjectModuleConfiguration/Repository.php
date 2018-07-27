<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ModuleConfiguration\ProjectModuleConfiguration;

/**
 * @internal
 */
class Repository implements RepositoryInterface
{
    /**
     * @var ConfigurationFactoryInterface
     */
    private $factory;

    /**
     * @var DataStorageInterface
     */
    private $dataStorage;

    /**
     * @var ConfigurationMapperInterface
     */
    private $mapper;

    /**
     * Repository constructor.
     * @param ConfigurationFactoryInterface $factory
     * @param DataStorageInterface          $dataStorage
     * @param ConfigurationMapperInterface  $mapper
     */
    public function __construct(
        ConfigurationFactoryInterface   $factory,
        DataStorageInterface            $dataStorage,
        ConfigurationMapperInterface    $mapper
    ) {
        $this->factory = $factory;
        $this->dataStorage = $dataStorage;
        $this->mapper = $mapper;
    }


    /**
     * @return ConfigurationInterface
     */
    public function getConfiguration(): ConfigurationInterface
    {
        $configuration = $this->factory->create();

        return $this->mapper->getConfiguration(
            $configuration,
            $this->dataStorage->get()
        );
    }

    /**
     * @param ConfigurationInterface $configuration
     */
    public function saveConfiguration(ConfigurationInterface $configuration)
    {
        $configurationData = $this->mapper->getConfigurationData($configuration);

        $this->dataStorage->save($configurationData);
    }
}
