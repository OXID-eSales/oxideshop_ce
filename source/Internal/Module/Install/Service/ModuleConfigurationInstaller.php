<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
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
     * @param BasicContextInterface                            $context
     */
    public function __construct(
        ProjectConfigurationDaoInterface                    $projectConfigurationDao,
        MetaDataProviderInterface                           $metadataProvider,
        MetaDataToModuleConfigurationDataMapperInterface    $metadataMapper,
        BasicContextInterface                               $context
    ) {
        $this->projectConfigurationDao = $projectConfigurationDao;
        $this->metadataProvider = $metadataProvider;
        $this->metadataMapper = $metadataMapper;
        $this->context = $context;
    }


    /**
     * @param string $moduleFullPath
     */
    public function install(string $moduleFullPath)
    {
        $metadata = $this->metadataProvider->getData($this->getMetadataFilePath($moduleFullPath));
        $moduleConfiguration = $this->metadataMapper->fromData($metadata);

        $projectConfiguration = $this->projectConfigurationDao->getConfiguration();
        $projectConfiguration = $this->addModuleConfigurationToAllShops($moduleConfiguration, $projectConfiguration);
        $projectConfiguration = $this->addClassExtensionsToChainForAllShops($moduleConfiguration, $projectConfiguration);

        $this->projectConfigurationDao->persistConfiguration($projectConfiguration);
    }

    /**
     * @param string $moduleFullPath
     * @return bool
     */
    public function isInstalled(string $moduleFullPath): bool
    {
        $metadata = $this->metadataProvider->getData($this->getMetadataFilePath($moduleFullPath));
        $moduleConfiguration = $this->metadataMapper->fromData($metadata);

        $projectConfiguration = $this->projectConfigurationDao->getConfiguration();
        $environmentConfiguration = $projectConfiguration->getEnvironmentConfiguration(
            $this->context->getEnvironment()
        );

        foreach ($environmentConfiguration->getShopConfigurations() as $shopConfiguration) {
            /** @var $shopConfiguration ShopConfiguration */
            if ($shopConfiguration->hasModuleConfiguration($moduleConfiguration->getId())) {
                return true;
            }
        }

        return false;
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
    private function addClassExtensionsToChainForAllShops(
        ModuleConfiguration $moduleConfiguration,
        ProjectConfiguration $projectConfiguration
    ): ProjectConfiguration {
        if ($moduleConfiguration->hasSetting(ModuleSetting::CLASS_EXTENSIONS)) {
            $classExtensions = $moduleConfiguration->getSetting(ModuleSetting::CLASS_EXTENSIONS);

            $environmentConfiguration = $projectConfiguration->getEnvironmentConfiguration(
                $this->context->getEnvironment()
            );

            foreach ($environmentConfiguration->getShopConfigurations() as $shopConfiguration) {
                $classExtensionChain = $shopConfiguration->getChain(Chain::CLASS_EXTENSIONS);
                $classExtensionChain->addExtensions($classExtensions->getValue());
            }
        }

        return $projectConfiguration;
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
