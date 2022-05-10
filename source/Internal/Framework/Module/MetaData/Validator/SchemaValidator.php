<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator;

use InvalidArgumentException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataSchemaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\DataObject\MetaDataSchema;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataKeyException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataValueTypeException;

use function array_key_exists;
use function in_array;
use function is_array;
use function is_scalar;
use function sprintf;

class SchemaValidator implements MetaDataValidatorInterface
{
    private array $validatedData;
    private MetaDataSchema $metaDataSchema;

    public function __construct(
        private MetaDataSchemaDaoInterface $metaDataSchemaDao,
    ) {
    }

    /**
     * @inheritDoc
     * @throws UnsupportedMetaDataValueTypeException
     * @throws UnsupportedMetaDataKeyException
     */
    public function validate(array $metadata): void
    {
        $this->validatedData = $metadata;
        $this->validateMandatoryKeys();
        $this->getMetadataSchema();
        $this->validateModuleData();
    }

    /**
     * @param array $data
     */
    private function validateMandatoryKeys(): void
    {
        foreach ($this->getMandatoryKeys() as $mandatoryKey) {
            if (!array_key_exists($mandatoryKey, $this->validatedData)) {
                throw new InvalidArgumentException(
                    "The key \"{$mandatoryKey}\" must be present in the array passed in the parameter"
                );
            }
        }
    }

    /**
     * @return array
     */
    private function getMandatoryKeys(): array
    {
        return [
            MetaDataProvider::METADATA_METADATA_VERSION,
            MetaDataProvider::METADATA_MODULE_DATA,
        ];
    }

    private function getMetadataSchema(): void
    {
        $this->metaDataSchema = $this->metaDataSchemaDao->get($this->getValidatedMetadataVersion());
    }

    /**
     * @return void
     * @throws UnsupportedMetaDataKeyException
     * @throws UnsupportedMetaDataValueTypeException
     */
    private function validateModuleData(): void
    {
        foreach ($this->validatedData[MetaDataProvider::METADATA_MODULE_DATA] as $key => $value) {
            if (!is_scalar($value) && !is_array($value)) {
                $this->throwUnsupportedValueTypeException(\gettype($value));
            }
            $this->validateMetaDataKey($key);
            if (is_array($value) && !$this->skipValidationOfSectionItems($key)) {
                $this->validateMetaDataSectionItems($key);
            }
        }
    }

    /**
     * @throws UnsupportedMetaDataKeyException
     */
    private function validateMetaDataKey(string $key): void
    {
        if (!$this->metaDataSchema->hasKey($key)) {
            $this->throwUnsupportedKeyException($key);
        }
    }

    /**
     * @param string $sectionName
     */
    private function validateMetaDataSectionItems(string $sectionName): void
    {
        foreach ($this->validatedData[MetaDataProvider::METADATA_MODULE_DATA][$sectionName] as $sectionItem) {
            if (!is_array($sectionItem)) {
                continue;
            }
            foreach (array_keys($sectionItem) as $sectionKey) {
                $this->validateSectionKey($sectionName, $sectionKey);
            }
        }
    }

    private function validateSectionKey(string $sectionName, string $key): void
    {
        if (!$this->metaDataSchema->hasSectionKey($sectionName, $key)) {
            $this->throwUnsupportedKeyException($key);
        }
    }

    /**
     * @return string
     */
    private function getValidatedMetadataVersion(): string
    {
        return $this->validatedData[MetaDataProvider::METADATA_METADATA_VERSION];
    }

    /**
     * @param string $sectionName
     * @return bool
     */
    private function skipValidationOfSectionItems(string $sectionName): bool
    {
        return in_array($sectionName, $this->getSectionsExcludedFromItemValidation(), true);
    }

    /**
     * @return array
     */
    private function getSectionsExcludedFromItemValidation(): array
    {
        return [
            MetaDataProvider::METADATA_EXTEND,
            MetaDataProvider::METADATA_CONTROLLERS,
            MetaDataProvider::METADATA_TEMPLATES,
            MetaDataProvider::METADATA_EVENTS,
            MetaDataProvider::METADATA_SMARTY_PLUGIN_DIRECTORIES,
        ];
    }

    /**
     * @param string $key
     * @return void
     * @throws UnsupportedMetaDataKeyException
     */
    private function throwUnsupportedKeyException(string $key): void
    {
        throw new UnsupportedMetaDataKeyException(
            sprintf(
                'The metadata key "%s" is not supported in metadata version "%s".',
                $key,
                $this->getValidatedMetadataVersion()
            )
        );
    }

    /**
     * @param string $valueType
     * @throws UnsupportedMetaDataValueTypeException
     */
    private function throwUnsupportedValueTypeException(string $valueType): void
    {
        throw new UnsupportedMetaDataValueTypeException(
            sprintf(
                'The value type "%s" is not supported in metadata version %s',
                $valueType,
                $this->getValidatedMetadataVersion()
            )
        );
    }
}
