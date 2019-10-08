<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\DataMapper;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\TemplateBlocksMappingKeys;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\TemplateBlock;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Template;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataValueTypeException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Service\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator\MetaDataSchemaValidator;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator\MetaDataSchemaValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ClassExtension;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\SmartyPluginDirectory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Controller;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ClassWithoutNamespace;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Event;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;

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

        if (isset($moduleData[MetaDataProvider::METADATA_EVENTS])) {
            foreach ($moduleData[MetaDataProvider::METADATA_EVENTS] as $action => $method) {
                $moduleConfiguration->addEvent(
                    new Event($action, $method)
                );
            }
        }

        if (isset($moduleData[MetaDataProvider::METADATA_FILES])) {
            if (count($moduleData[MetaDataProvider::METADATA_FILES]) === 0) {
                $moduleConfiguration->addClassWithoutNamespace(
                    new ClassWithoutNamespace(
                        '',
                        ''
                    )
                );
            } else {
                foreach ($moduleData[MetaDataProvider::METADATA_FILES] as $shopClass => $moduleClass) {
                    $moduleConfiguration->addClassWithoutNamespace(
                        new ClassWithoutNamespace(
                            $shopClass,
                            $moduleClass
                        )
                    );
                }
            }
        }

        if (isset($moduleData[MetaDataProvider::METADATA_BLOCKS])) {
            foreach ($moduleData[MetaDataProvider::METADATA_BLOCKS] as $templateBlockData) {
                $templateBlock = new TemplateBlock(
                    $templateBlockData[TemplateBlocksMappingKeys::SHOP_TEMPLATE_PATH],
                    $templateBlockData[TemplateBlocksMappingKeys::BLOCK_NAME],
                    $templateBlockData[TemplateBlocksMappingKeys::MODULE_TEMPLATE_PATH]
                );
                if (isset($templateBlockData[TemplateBlocksMappingKeys::POSITION])) {
                    $templateBlock->setPosition((int) $templateBlockData[TemplateBlocksMappingKeys::POSITION]);
                }
                if (isset($templateBlockData[TemplateBlocksMappingKeys::THEME])) {
                    $templateBlock->setTheme($templateBlockData[TemplateBlocksMappingKeys::THEME]);
                }
                $moduleConfiguration->addTemplateBlock($templateBlock);
            }
        }

        $moduleConfiguration = $this->mapSettings($moduleConfiguration, $moduleData);

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

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param $moduleData
     * @return ModuleConfiguration
     */
    private function mapSettings(ModuleConfiguration $moduleConfiguration, $moduleData): ModuleConfiguration
    {
        if (isset($moduleData[MetaDataProvider::METADATA_SETTINGS])) {
            foreach ($moduleData[MetaDataProvider::METADATA_SETTINGS] as $data) {
                $setting = new Setting();
                $setting->setName($data['name']);
                $setting->setType($data['type']);

                if (isset($data['group'])) {
                    $setting->setGroupName($data['group']);
                }

                if (isset($data['value'])) {
                    $setting->setValue($data['value']);
                }

                if (isset($data['constraints'])) {
                    $setting->setConstraints($data['constraints']);
                }

                if (isset($data['position'])) {
                    $setting->setPositionInGroup((int)$data['position']);
                }

                $moduleConfiguration->addModuleSetting($setting);
            }
        }

        return $moduleConfiguration;
    }
}
