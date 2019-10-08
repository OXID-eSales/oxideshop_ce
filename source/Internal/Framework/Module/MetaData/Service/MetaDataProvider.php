<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Service;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Converter\MetaDataConverterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\InvalidMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator\MetaDataValidatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MetaDataProvider implements MetaDataProviderInterface
{
    const METADATA_ID = 'id';
    const METADATA_METADATA_VERSION = 'metaDataVersion';
    const METADATA_MODULE_DATA = 'moduleData';
    const METADATA_TITLE = 'title';
    const METADATA_DESCRIPTION = 'description';
    const METADATA_LANG = 'lang';
    const METADATA_THUMBNAIL = 'thumbnail';
    const METADATA_AUTHOR = 'author';
    const METADATA_URL = 'url';
    const METADATA_EMAIL = 'email';
    const METADATA_VERSION = 'version';
    const METADATA_EXTEND = 'extend';
    const METADATA_BLOCKS = 'blocks';
    const METADATA_CONTROLLERS = 'controllers';
    const METADATA_EVENTS = 'events';
    const METADATA_TEMPLATES = 'templates';
    const METADATA_SETTINGS = 'settings';
    const METADATA_SMARTY_PLUGIN_DIRECTORIES = 'smartyPluginDirectories';
    const METADATA_FILEPATH = 'metaDataFilePath';
    const METADATA_FILES = 'files';

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
