<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\MetaData\DataMapper;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Service\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Validator\MetaDataValidator;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Validator\MetaDataValidatorInterface;

/**
 * Class MetaDataMapper
 *
 * @internal
 *
 * @package OxidEsales\EshopCommunity\Internal\Module\MetaData\DataMapper
 */
class MetaDataMapper implements MetaDataToModuleConfigurationDataMapperInterface
{
    /**
     * @var MetaDataValidator
     */
    private $validator;

    /**
     * MetaDataMapper constructor.
     *
     * @param MetaDataValidatorInterface $validator
     */
    public function __construct(MetaDataValidatorInterface $validator)
    {
        $this->validator = $validator;
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
            $metaData[MetaDataProvider::METADATA_METADATA_VERSION],
            $metaData[MetaDataProvider::METADATA_MODULE_DATA]
        );

        $mappedData = $this->getMappedData(
            $metaData[MetaDataProvider::METADATA_MODULE_DATA],
            $metaData[MetaDataProvider::METADATA_PATH],
            $metaData[MetaDataProvider::METADATA_CHECKSUM]
        );

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setMetaDataCheckSum($mappedData[MetaDataProvider::METADATA_CHECKSUM])
            ->setId($mappedData[MetaDataProvider::METADATA_ID])
            ->setTitle($mappedData[MetaDataProvider::METADATA_TITLE])
            ->setDescription($mappedData[MetaDataProvider::METADATA_DESCRIPTION])
            ->setLang($mappedData[MetaDataProvider::METADATA_LANG])
            ->setThumbnail($mappedData[MetaDataProvider::METADATA_THUMBNAIL])
            ->setAuthor($mappedData[MetaDataProvider::METADATA_AUTHOR])
            ->setUrl($mappedData[MetaDataProvider::METADATA_URL])
            ->setEmail($mappedData[MetaDataProvider::METADATA_EMAIL])
            ->setSettings($mappedData[MetaDataProvider::METADATA_SETTINGS]);

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
            MetaDataProvider::METADATA_PATH,
            MetaDataProvider::METADATA_CHECKSUM
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
     * @param string $metaDataChecksum
     *
     * @return array
     */
    private function getMappedData(array $metaData, string $path, string $metaDataChecksum): array
    {
        $mappedData = [
            MetaDataProvider::METADATA_ID          => $metaData[MetaDataProvider::METADATA_ID] ?? '',
            MetaDataProvider::METADATA_CHECKSUM    => $metaDataChecksum ?? '',
            MetaDataProvider::METADATA_TITLE       => $metaData[MetaDataProvider::METADATA_TITLE] ?? '',
            MetaDataProvider::METADATA_DESCRIPTION => $metaData[MetaDataProvider::METADATA_DESCRIPTION] ?? [],
            MetaDataProvider::METADATA_LANG        => $metaData[MetaDataProvider::METADATA_LANG] ?? '',
            MetaDataProvider::METADATA_THUMBNAIL   => $metaData[MetaDataProvider::METADATA_THUMBNAIL] ?? '',
            MetaDataProvider::METADATA_AUTHOR      => $metaData[MetaDataProvider::METADATA_AUTHOR] ?? '',
            MetaDataProvider::METADATA_URL         => $metaData[MetaDataProvider::METADATA_URL] ?? '',
            MetaDataProvider::METADATA_EMAIL       => $metaData[MetaDataProvider::METADATA_EMAIL] ?? '',
            MetaDataProvider::METADATA_SETTINGS    => [
                ModuleSetting::PATH                      => $path,
                ModuleSetting::VERSION                   => $metaData[MetaDataProvider::METADATA_VERSION] ?? '',
                ModuleSetting::CLASS_EXTENSIONS          => $metaData[MetaDataProvider::METADATA_EXTEND] ?? [],
                ModuleSetting::TEMPLATE_BLOCKS           => $metaData[MetaDataProvider::METADATA_BLOCKS] ?? [],
                ModuleSetting::CONTROLLERS               => $metaData[MetaDataProvider::METADATA_CONTROLLERS] ?? [],
                ModuleSetting::EVENTS                    => $metaData[MetaDataProvider::METADATA_EVENTS] ?? [],
                ModuleSetting::TEMPLATES                 => $metaData[MetaDataProvider::METADATA_TEMPLATES] ?? [],
                ModuleSetting::SHOP_MODULE_SETTING       => $metaData[MetaDataProvider::METADATA_SETTINGS] ?? [],
                ModuleSetting::SMARTY_PLUGIN_DIRECTORIES => $metaData[MetaDataProvider::METADATA_SMARTY_PLUGIN_DIRECTORIES] ?? []
            ],
        ];

        return $mappedData;
    }
}
