<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Install;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\Chain;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\DataMapper\MetaDataToModuleConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Service\MetaDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;

/**
 * @internal
 */
class ModuleConfigurationInstaller implements ModuleConfigurationInstallerInterface
{
    /**
     * @var string
     */
    private $metadataFileName = 'metadata.php';

    /**
     * @var ProjectConfigurationDaoInterface
     */
    private $projectConfigurationDao;

    /**
     * @var MetaDataProviderInterface
     */
    private $metadataProvider;

    /**
     * @var MetaDataToModuleConfigurationDataMapperInterface
     */
    private $metadataMapper;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * ModuleConfigurationInstaller constructor.
     * @param ProjectConfigurationDaoInterface                 $projectConfigurationDao
     * @param MetaDataProviderInterface                        $metadataProvider
     * @param MetaDataToModuleConfigurationDataMapperInterface $metadataMapper
     * @param ContextInterface                                 $context
     */
    public function __construct(
        ProjectConfigurationDaoInterface                    $projectConfigurationDao,
        MetaDataProviderInterface                           $metadataProvider,
        MetaDataToModuleConfigurationDataMapperInterface    $metadataMapper,
        ContextInterface                                    $context
    ) {
        $this->projectConfigurationDao = $projectConfigurationDao;
        $this->metadataProvider = $metadataProvider;
        $this->metadataMapper = $metadataMapper;
        $this->context = $context;
    }


    /**
     * @param string $moduleFullPath
     */
    public function transferMetadataToProjectConfiguration(string $moduleFullPath)
    {
        $metadata = $this->metadataProvider->getData($this->getMetadataFilePath($moduleFullPath));
        $moduleConfiguration = $this->metadataMapper->fromData($metadata);

        $projectConfiguration = $this->projectConfigurationDao->getConfiguration();
        $projectConfiguration = $this->addModuleConfigurationToAllShops($moduleConfiguration, $projectConfiguration);
        $projectConfiguration = $this->updateChainsInAllShops($moduleConfiguration, $projectConfiguration);

        $this->projectConfigurationDao->persistConfiguration($projectConfiguration);
    }

    /**
     * @param ModuleConfiguration  $moduleConfiguration
     * @param ProjectConfiguration $projectConfiguration
     *
     * @return ProjectConfiguration
     */
    private function addModuleConfigurationToAllShops(
        ModuleConfiguration $moduleConfiguration,
        ProjectConfiguration $projectConfiguration
    ): ProjectConfiguration {

        $environmentConfiguration = $projectConfiguration->getEnvironmentConfiguration(
            $this->context->getEnvironment()
        );

        foreach ($environmentConfiguration->getShopConfigurations() as $shopConfiguration) {
            /** @var $shopConfiguration ShopConfiguration */
            $shopConfiguration->addModuleConfiguration($moduleConfiguration);
        }

        return $projectConfiguration;
    }

    /**
     * @param ModuleConfiguration  $moduleConfiguration
     * @param ProjectConfiguration $projectConfiguration
     *
     * @return ProjectConfiguration
     */
    private function updateChainsInAllShops(
        ModuleConfiguration $moduleConfiguration,
        ProjectConfiguration $projectConfiguration
    ): ProjectConfiguration {

        $environmentConfiguration = $projectConfiguration->getEnvironmentConfiguration(
            $this->context->getEnvironment()
        );

        foreach ($environmentConfiguration->getShopConfigurations() as $shopConfiguration) {
            if ($moduleConfiguration->hasSetting(ModuleSetting::CLASS_EXTENSIONS)) {
                $classExtensionChain = $this->addModuleExtensionsToChain(
                    $moduleConfiguration->getSetting(ModuleSetting::CLASS_EXTENSIONS),
                    $shopConfiguration->getChain(Chain::CLASS_EXTENSIONS)
                );

                $shopConfiguration->addChain($classExtensionChain);
            }
        }

        return $projectConfiguration;
    }

    /**
     * @param ModuleSetting $classExtensions
     * @param Chain         $classExtensionChain
     *
     * @return Chain
     */
    private function addModuleExtensionsToChain(ModuleSetting $classExtensions, Chain $classExtensionChain): Chain
    {
        foreach ($classExtensions->getValue() as $shopClass => $extension) {
            $chain = $classExtensionChain->getChain();

            if (array_key_exists($shopClass, $classExtensionChain->getChain())) {
                array_push($chain[$shopClass], $extension);
                $classExtensionChain->setChain($chain);
            } else {
                $chain[$shopClass] = [$extension];
                $classExtensionChain->setChain($chain);
            }
        }

        return $classExtensionChain;
    }

    /**
     * @param string $moduleFullPath
     * @return string
     */
    private function getMetadataFilePath(string $moduleFullPath): string
    {
        return $moduleFullPath . DIRECTORY_SEPARATOR . $this->metadataFileName;
    }
}
