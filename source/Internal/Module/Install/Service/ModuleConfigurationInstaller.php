<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Service\ModuleConfigurationMergingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\DataMapper\MetaDataToModuleConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Service\MetaDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use Webmozart\PathUtil\Path;

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
     * @var ModuleConfigurationMergingServiceInterface
     */
    private $moduleConfigurationMergingService;

    /**
     * ModuleConfigurationInstaller constructor.
     * @param ProjectConfigurationDaoInterface                 $projectConfigurationDao
     * @param MetaDataProviderInterface                        $metadataProvider
     * @param MetaDataToModuleConfigurationDataMapperInterface $metadataMapper
     * @param ModuleConfigurationMergingServiceInterface       $moduleConfigurationMergingService
     * @param BasicContextInterface                            $context
     */
    public function __construct(
        ProjectConfigurationDaoInterface                    $projectConfigurationDao,
        MetaDataProviderInterface                           $metadataProvider,
        MetaDataToModuleConfigurationDataMapperInterface    $metadataMapper,
        ModuleConfigurationMergingServiceInterface          $moduleConfigurationMergingService,
        BasicContextInterface                               $context
    ) {
        $this->projectConfigurationDao = $projectConfigurationDao;
        $this->metadataProvider = $metadataProvider;
        $this->metadataMapper = $metadataMapper;
        $this->context = $context;
        $this->moduleConfigurationMergingService = $moduleConfigurationMergingService;
    }


    /**
     * @param string $moduleSourcePath
     * @param string $moduleTargetPath
     */
    public function install(string $moduleSourcePath, string $moduleTargetPath)
    {
        $metadata = $this->metadataProvider->getData($this->getMetadataFilePath($moduleSourcePath));
        $moduleConfiguration = $this->metadataMapper->fromData($metadata);

        $moduleConfiguration->setPath($this->getModuleRelativePath($moduleTargetPath));

        $projectConfiguration = $this->projectConfigurationDao->getConfiguration();
        $projectConfiguration = $this->addModuleConfigurationToAllShops($moduleConfiguration, $projectConfiguration);

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
            $this->moduleConfigurationMergingService->merge($shopConfiguration, $moduleConfiguration);
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

    /**
     * @param string $moduleTargetPath
     * @return string
     */
    private function getModuleRelativePath(string $moduleTargetPath): string
    {
        return Path::isRelative($moduleTargetPath)
            ? $moduleTargetPath
            : Path::makeRelative($moduleTargetPath, $this->context->getModulesPath());
    }
}
