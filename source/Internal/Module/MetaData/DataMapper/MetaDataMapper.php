<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\MetaData\DataMapper;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration\Template;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Exception\UnsupportedMetaDataValueTypeException;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Service\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Validator\MetaDataSchemaValidator;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Validator\MetaDataSchemaValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration\ClassExtension;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration\SmartyPluginDirectory;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration\Controller;

/**
 * @internal
 */
class MetaDataMapper implements MetaDataToModuleConfigurationDataMapperInterface
{
    /**
     * @var MetaDataSchemaValidator
     */
    private $validator;

    /**
     * MetaDataMapper constructor.
     *
     * @param MetaDataSchemaValidatorInterface $validator
     */
    public function __construct(MetaDataSchemaValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array $metaData
     *
     * @return ModuleConfiguration
     * @throws UnsupportedMetaDataValueTypeException
     */
    public function fromData(array $metaData): ModuleConfiguration
    {
        $this->validateParameterFormat($metaData);

        $this->validator->validate(
            $metaData[MetaDataProvider::METADATA_FILEPATH],
            $metaData[MetaDataProvider::METADATA_METADATA_VERSION],
            $metaData[MetaDataProvider::METADATA_MODULE_DATA]
        );

        $moduleData = $metaData[MetaDataProvider::METADATA_MODULE_DATA];

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId($moduleData[MetaDataProvider::METADATA_ID])
            ->setVersion($moduleData[MetaDataProvider::METADATA_VERSION] ?? '')
            ->setDescription($moduleData[MetaDataProvider::METADATA_DESCRIPTION] ?? [])
            ->setLang($moduleData[MetaDataProvider::METADATA_LANG] ?? '')
            ->setThumbnail($moduleData[MetaDataProvider::METADATA_THUMBNAIL] ?? '')
            ->setAuthor($moduleData[MetaDataProvider::METADATA_AUTHOR] ?? '')
            ->setUrl($moduleData[MetaDataProvider::METADATA_URL] ?? '')
            ->setEmail($moduleData[MetaDataProvider::METADATA_EMAIL] ?? '');

        if (isset($moduleData[MetaDataProvider::METADATA_TITLE])) {
            $moduleConfiguration->setTitle($moduleData[MetaDataProvider::METADATA_TITLE]);
        }

        $moduleConfiguration = $this->mapModuleConfigurationSettings($moduleConfiguration, $metaData);

        return $moduleConfiguration;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param array $metaData
     * @return ModuleConfiguration
     */
    private function mapModuleConfigurationSettings(
        ModuleConfiguration $moduleConfiguration,
        array $metaData
    ): ModuleConfiguration {
        $moduleData = $metaData[MetaDataProvider::METADATA_MODULE_DATA];

        if (isset($moduleData[MetaDataProvider::METADATA_EXTEND])) {
            foreach ($moduleData[MetaDataProvider::METADATA_EXTEND] as $shopClass => $moduleClass) {
                $moduleConfiguration->addClassExtension(
                    new ClassExtension($shopClass, $moduleClass)
                );
            }
        }

        if (isset($moduleData[MetaDataProvider::METADATA_TEMPLATES])) {
            foreach ($moduleData[MetaDataProvider::METADATA_TEMPLATES] as $templateKey => $templatePath) {
                $moduleConfiguration->addTemplate(
                    new Template($templateKey, $templatePath)
                );
            }
        }

        if (isset($moduleData[MetaDataProvider::METADATA_CONTROLLERS])) {
            foreach ($moduleData[MetaDataProvider::METADATA_CONTROLLERS] as $id => $controllerClassNameSpace) {
                $moduleConfiguration->addController(
                    new Controller($id, $controllerClassNameSpace)
                );
            }
        }

        if (isset($moduleData[MetaDataProvider::METADATA_SMARTY_PLUGIN_DIRECTORIES])) {
            foreach ($moduleData[MetaDataProvider::METADATA_SMARTY_PLUGIN_DIRECTORIES] as $directory) {
                $moduleConfiguration->addSmartyPluginDirectory(
                    new SmartyPluginDirectory($directory)
                );
            }
        }

        if (isset($moduleData[MetaDataProvider::METADATA_FILES])) {
            $moduleConfiguration->addSetting(
                new ModuleSetting(
                    ModuleSetting::CLASSES_WITHOUT_NAMESPACE,
                    $moduleData[MetaDataProvider::METADATA_FILES]
                )
            );
        }

        if (isset($moduleData[MetaDataProvider::METADATA_BLOCKS])) {
            $moduleConfiguration->addSetting(
                new ModuleSetting(ModuleSetting::TEMPLATE_BLOCKS, $moduleData[MetaDataProvider::METADATA_BLOCKS])
            );
        }

        if (isset($moduleData[MetaDataProvider::METADATA_EVENTS])) {
            $moduleConfiguration->addSetting(
                new ModuleSetting(ModuleSetting::EVENTS, $moduleData[MetaDataProvider::METADATA_EVENTS])
            );
        }

        if (isset($moduleData[MetaDataProvider::METADATA_SETTINGS])) {
            $moduleConfiguration->addSetting(
                new ModuleSetting(ModuleSetting::SHOP_MODULE_SETTING, $moduleData[MetaDataProvider::METADATA_SETTINGS])
            );
        }

        return $moduleConfiguration;
    }

    /**
     * @param array $data
     */
    private function validateParameterFormat(array $data)
    {
        $mandatoryKeys = [
            MetaDataProvider::METADATA_METADATA_VERSION,
            MetaDataProvider::METADATA_MODULE_DATA,
        ];
        foreach ($mandatoryKeys as $mandatoryKey) {
            if (false === array_key_exists($mandatoryKey, $data)) {
                throw new \InvalidArgumentException(
                    'The key "' . $mandatoryKey . '" must be present in the array passed in the parameter'
                );
            }
        }
    }
}
