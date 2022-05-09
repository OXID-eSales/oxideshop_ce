<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Converter\MetaDataConverterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\InvalidMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator\MetaDataValidatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MetaDataProvider implements MetaDataProviderInterface
{
    public const METADATA_ID = 'id';
    public const METADATA_METADATA_VERSION = 'metaDataVersion';
    public const METADATA_MODULE_DATA = 'moduleData';
    public const METADATA_TITLE = 'title';
    public const METADATA_DESCRIPTION = 'description';
    public const METADATA_LANG = 'lang';
    public const METADATA_THUMBNAIL = 'thumbnail';
    public const METADATA_AUTHOR = 'author';
    public const METADATA_URL = 'url';
    public const METADATA_EMAIL = 'email';
    public const METADATA_VERSION = 'version';
    public const METADATA_EXTEND = 'extend';
    public const METADATA_BLOCKS = 'blocks';
    public const METADATA_CONTROLLERS = 'controllers';
    public const METADATA_EVENTS = 'events';
    public const METADATA_TEMPLATES = 'templates';
    public const METADATA_SETTINGS = 'settings';
    public const METADATA_SMARTY_PLUGIN_DIRECTORIES = 'smartyPluginDirectories';
    /**
     * @deprecated will be removed in v7.0
     */
    public const METADATA_FILEPATH = 'metaDataFilePath';
    /**
     * @deprecated 6.6 Will be removed completely
     */
    public const METADATA_FILES = 'files';

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var MetaDataNormalizerInterface
     */
    private $metaDataNormalizer;

    /**
     * @var BasicContextInterface
     */
    private $context;
    /**
     * @var MetaDataValidatorInterface
     */
    private $metaDataValidatorService;

    /**
     * @var MetaDataConverterInterface
     */
    private $metaDataConverter;

    /**
     * @param MetaDataNormalizerInterface $metaDataNormalizer
     * @param BasicContextInterface       $context
     * @param MetaDataValidatorInterface  $metaDataValidator
     * @param MetaDataConverterInterface  $metaDataConverter
     */
    public function __construct(
        MetaDataNormalizerInterface $metaDataNormalizer,
        BasicContextInterface $context,
        MetaDataValidatorInterface $metaDataValidator,
        MetaDataConverterInterface $metaDataConverter
    ) {
        $this->metaDataNormalizer = $metaDataNormalizer;
        $this->context = $context;
        $this->metaDataValidatorService = $metaDataValidator;
        $this->metaDataConverter = $metaDataConverter;
    }

    /**
     * @param string $filePath
     *
     * @return array
     * @throws InvalidMetaDataException
     */
    public function getData(string $filePath): array
    {
        if (!is_readable($filePath) || is_dir($filePath)) {
            throw new \InvalidArgumentException('File ' . $filePath . ' is not readable or not even a file.');
        }
        $this->filePath = $filePath;
        $normalizedMetaData = $this->getNormalizedMetaDataFileContent();
        $normalizedMetaData = $this->addFilePathToData($normalizedMetaData);

        return $normalizedMetaData;
    }

    /**
     * @return array
     * @throws InvalidMetaDataException
     */
    private function getNormalizedMetaDataFileContent(): array
    {
        /**
         * The following variables will be overwritten when the metadata file is included.
         */
        $sMetadataVersion = null;
        $aModule = null;
        include $this->filePath;
        $metadataVersion = $sMetadataVersion;
        $moduleData = $aModule;

        $this->validateMetaDataFileVariables($metadataVersion, $moduleData);
        $this->metaDataValidatorService->validate($moduleData);
        $moduleData = $this->metaDataConverter->convert($moduleData);
        $normalizedMetaData = $this->metaDataNormalizer->normalizeData($moduleData);

        if (isset($normalizedMetaData[static::METADATA_EXTEND])) {
            $normalizedMetaData[static::METADATA_EXTEND] = $this->sanitizeExtendedClasses($normalizedMetaData);
        }

        return [
            static::METADATA_METADATA_VERSION => $metadataVersion,
            static::METADATA_MODULE_DATA      => $normalizedMetaData
        ];
    }

    /**
     * @param array $normalizedMetaData
     *
     * @return mixed
     */
    private function addFilePathToData(array $normalizedMetaData): array
    {
        $normalizedMetaData[static::METADATA_FILEPATH] = $this->filePath;

        return $normalizedMetaData;
    }

    /**
     * @param mixed $metaDataVersion
     * @param mixed $moduleData
     *
     * @throws InvalidMetaDataException
     */
    private function validateMetaDataFileVariables($metaDataVersion, $moduleData): void
    {
        if ($metaDataVersion === null || !is_scalar($metaDataVersion)) {
            throw new InvalidMetaDataException(
                'The variable $sMetadataVersion must be present in '
                . $this->filePath . ' and it must be a scalar.'
            );
        }
        if ($moduleData === null || !is_array($moduleData)) {
            throw new InvalidMetaDataException(
                'The variable $aModule must be present in '
                . $this->filePath . ' and it must be an array'
            );
        }
    }

    /**
     * @param array $normalizedMetaData
     *
     * @return array
     */
    private function sanitizeExtendedClasses(array $normalizedMetaData): array
    {
        $sanitizedExtendedClasses = [];
        $extendedClasses = $normalizedMetaData[static::METADATA_EXTEND] ?? [];
        foreach ($extendedClasses as $shopClass => $moduleClass) {
            if ($this->isBackwardsCompatibleClass($shopClass)) {
                $sanitizedShopClass = $this->getBackwardsCompatibilityClassMap()[strtolower($shopClass)];
            } else {
                $sanitizedShopClass = $shopClass;
            }
            $sanitizedExtendedClasses[$sanitizedShopClass] = $moduleClass;
        }

        return $sanitizedExtendedClasses;
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    private function isBackwardsCompatibleClass(string $className): bool
    {
        return array_key_exists(strtolower($className), $this->getBackwardsCompatibilityClassMap());
    }

    /**
     * @return array
     */
    private function getBackwardsCompatibilityClassMap(): array
    {
        return $this->context->getBackwardsCompatibilityClassMap();
    }
}
