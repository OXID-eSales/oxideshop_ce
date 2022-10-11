<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\DataMapper;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ClassExtension;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Controller;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Event;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Template;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataValueTypeException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator\MetaDataSchemaValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;

class MetaDataMapper implements MetaDataToModuleConfigurationDataMapperInterface
{
    public function __construct(private MetaDataSchemaValidatorInterface $validator)
    {
    }

    /**
     * @param array $metaData
     *
     * @return ModuleConfiguration
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

        return $this->mapModuleConfigurationSettings($moduleConfiguration, $metaData);
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
            foreach ($moduleData[MetaDataProvider::METADATA_TEMPLATES] as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $themedTemplateKey => $themedTemplatePath) {
                        $moduleConfiguration->addTemplate(
                            new ModuleConfiguration\ThemedTemplate($themedTemplateKey, $themedTemplatePath, $key)
                        );
                    }
                } else {
                    $moduleConfiguration->addTemplate(
                        new Template($key, $value)
                    );
                }
            }
        }

        if (isset($moduleData[MetaDataProvider::METADATA_CONTROLLERS])) {
            foreach ($moduleData[MetaDataProvider::METADATA_CONTROLLERS] as $id => $controllerClassNameSpace) {
                $moduleConfiguration->addController(
                    new Controller($id, $controllerClassNameSpace)
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

        return $this->mapSettings($moduleConfiguration, $moduleData);
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
            if (false === \array_key_exists($mandatoryKey, $data)) {
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
