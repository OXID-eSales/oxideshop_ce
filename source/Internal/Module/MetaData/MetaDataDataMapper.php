<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\MetaData;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ModuleConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Validator\MetaDataValidator;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Validator\MetaDataValidatorInterface;

/**
 * Class MetaDataDataMapper
 *
 * @internal
 *
 * @package OxidEsales\EshopCommunity\Internal\Module\MetaData
 */
class MetaDataDataMapper implements ModuleConfigurationDataMapperInterface
{
    /**
     * @var MetaDataValidator
     */
    private $validator;

    /**
     * MetaDataDataMapper constructor.
     *
     * @param MetaDataValidatorInterface $validator
     */
    public function __construct(MetaDataValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param ModuleConfiguration $configuration
     *
     * @throws \DomainException
     *
     * @return array
     */
    public function toData(ModuleConfiguration $configuration): array
    {
        throw new \DomainException(__CLASS__ . ' does not support calling method ' . __FUNCTION__);

        return [];
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
            $metaData[MetaDataDataProvider::METADATA_METADATA_VERSION],
            $metaData[MetaDataDataProvider::METADATA_MODULE_DATA]
        );

        $mappedData = $this->getMappedData(
            $metaData[MetaDataDataProvider::METADATA_MODULE_DATA],
            $metaData[MetaDataDataProvider::METADATA_PATH]
        );

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setId($mappedData[MetaDataDataProvider::METADATA_ID])
            ->setTitle($mappedData[MetaDataDataProvider::METADATA_TITLE])
            ->setDescription($mappedData[MetaDataDataProvider::METADATA_DESCRIPTION])
            ->setLang($mappedData[MetaDataDataProvider::METADATA_LANG])
            ->setThumbnail($mappedData[MetaDataDataProvider::METADATA_THUMBNAIL])
            ->setAuthor($mappedData[MetaDataDataProvider::METADATA_AUTHOR])
            ->setUrl($mappedData[MetaDataDataProvider::METADATA_URL])
            ->setEmail($mappedData[MetaDataDataProvider::METADATA_EMAIL])
            ->setSettings($mappedData[MetaDataDataProvider::METADATA_SETTINGS]);

        return $moduleConfiguration;
    }

    /**
     * @param array $data
     */
    private function validateParameterFormat(array $data)
    {
        $mandatoryKeys = [
            MetaDataDataProvider::METADATA_METADATA_VERSION,
            MetaDataDataProvider::METADATA_MODULE_DATA,
            MetaDataDataProvider::METADATA_PATH
        ];
        foreach ($mandatoryKeys as $mandatoryKey) {
            if (false === array_key_exists($mandatoryKey, $data)) {
                throw new \InvalidArgumentException('The key "' . $mandatoryKey . '" must be present in the array passed in the parameter');
            }
        }
    }

    /**
     * @param array  $metaData
     * @param string $path
     *
     * @return array
     */
    private function getMappedData(array $metaData, string $path): array
    {
        $mappedData = [
            'id'          => $metaData[MetaDataDataProvider::METADATA_ID] ?? '',
            'title'       => $metaData[MetaDataDataProvider::METADATA_TITLE] ?? '',
            'description' => $metaData[MetaDataDataProvider::METADATA_DESCRIPTION] ?? [],
            'lang'        => $metaData[MetaDataDataProvider::METADATA_LANG] ?? '',
            'thumbnail'   => $metaData[MetaDataDataProvider::METADATA_THUMBNAIL] ?? '',
            'author'      => $metaData[MetaDataDataProvider::METADATA_AUTHOR] ?? '',
            'url'         => $metaData[MetaDataDataProvider::METADATA_URL] ?? '',
            'email'       => $metaData[MetaDataDataProvider::METADATA_EMAIL] ?? '',
            'settings'    => [
                ModuleSetting::PATH                      => $path,
                ModuleSetting::VERSION                   => $metaData[MetaDataDataProvider::METADATA_VERSION] ?? '',
                ModuleSetting::CLASS_EXTENSIONS          => $metaData[MetaDataDataProvider::METADATA_EXTEND] ?? [],
                ModuleSetting::TEMPLATE_BLOCKS           => $metaData[MetaDataDataProvider::METADATA_BLOCKS] ?? [],
                ModuleSetting::CONTROLLERS               => $metaData[MetaDataDataProvider::METADATA_CONTROLLERS] ?? [],
                ModuleSetting::EVENTS                    => $metaData[MetaDataDataProvider::METADATA_EVENTS] ?? [],
                ModuleSetting::TEMPLATES                 => $metaData[MetaDataDataProvider::METADATA_TEMPLATES] ?? [],
                ModuleSetting::SHOP_MODULE_SETTING       => $metaData[MetaDataDataProvider::METADATA_SETTINGS] ?? [],
                ModuleSetting::SMARTY_PLUGIN_DIRECTORIES => $metaData[MetaDataDataProvider::METADATA_SMARTY_PLUGIN_DIRECTORIES] ?? []
            ],
        ];

        return $mappedData;
    }
}
