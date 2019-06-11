<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\MetaData\Validator;

use OxidEsales\EshopCommunity\Internal\Module\MetaData\Event\BadMetaDataFoundEvent;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Exception\UnsupportedMetaDataValueTypeException;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Service\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Service\MetaDataSchemataProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
class MetaDataSchemaValidator implements MetaDataSchemaValidatorInterface
{
    /**
     * @var MetaDataSchemataProviderInterface
     */
    private $metaDataSchemataProvider;

    /**
     * @var array
     */
    private static $sectionsExcludedFromItemValidation = [
        MetaDataProvider::METADATA_EXTEND,
        MetaDataProvider::METADATA_CONTROLLERS,
        MetaDataProvider::METADATA_TEMPLATES,
        MetaDataProvider::METADATA_EVENTS,
        MetaDataProvider::METADATA_SMARTY_PLUGIN_DIRECTORIES,
    ];

    /**
     * @var string
     */
    private $currentValidationMetaDataVersion;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var string
     */
    private $metaDataFilePath;

    /**
     * MetaDataValidator constructor.
     *
     * @param MetaDataSchemataProviderInterface $metaDataSchemataProvider
     * @param EventDispatcherInterface          $eventDispatcher
     */
    public function __construct(MetaDataSchemataProviderInterface $metaDataSchemataProvider, EventDispatcherInterface $eventDispatcher)
    {
        $this->metaDataSchemataProvider = $metaDataSchemataProvider;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Validate that a given metadata meets the specifications of a given metadata version
     *
     * @param string $metaDataFilePath
     * @param string $metaDataVersion
     * @param array  $metaData
     *
     * @throws UnsupportedMetaDataValueTypeException
     */
    public function validate(string $metaDataFilePath, string $metaDataVersion, array $metaData)
    {
        $this->currentValidationMetaDataVersion = $metaDataVersion;
        $this->metaDataFilePath = $metaDataFilePath;

        $supportedMetaDataKeys = $this->metaDataSchemataProvider->getFlippedMetaDataSchemaForVersion($this->currentValidationMetaDataVersion);
        foreach ($metaData as $metaDataKey => $value) {
            if (is_scalar($value)) {
                $this->validateMetaDataKey($supportedMetaDataKeys, (string) $metaDataKey);
            } elseif (true === \is_array($value)) {
                $this->validateMetaDataSection($supportedMetaDataKeys, $metaDataKey, $value);
            } else {
                throw new UnsupportedMetaDataValueTypeException('The value type "' . \gettype($value) . '" is not supported in metadata version ' . $this->currentValidationMetaDataVersion);
            }
        }
    }

    /**
     * @param array  $supportedMetaDataKeys
     *
     * @param string $metaDataKey
     */
    private function validateMetaDataKey(array $supportedMetaDataKeys, string $metaDataKey)
    {
        if (false === array_key_exists($metaDataKey, $supportedMetaDataKeys)) {
            $message = 'The metadata key "' . $metaDataKey . '" is not supported in metadata version "' . $this->currentValidationMetaDataVersion . '"';

            $event = new BadMetaDataFoundEvent($this->metaDataFilePath, $message);
            $this->eventDispatcher->dispatch($event::NAME, $event);
        }
    }

    /**
     * Validate well defined section items
     *
     * @param array  $supportedMetaDataKeys
     * @param string $sectionName
     * @param array  $sectionData
     */
    private function validateMetaDataSectionItems(array $supportedMetaDataKeys, string $sectionName, array $sectionData)
    {
        foreach ($sectionData as $sectionItem) {
            if (\is_array($sectionItem)) {
                $metaDataKeys = array_keys($sectionItem);
                foreach ($metaDataKeys as $metaDataKey) {
                    $this->validateMetaDataKey($supportedMetaDataKeys[$sectionName], $metaDataKey);
                }
            }
        }
    }

    /**
     * Validate a section of metadata like 'blocks' or 'settings', which are multidimensional arrays of well defined items.
     * There are sections (e.g. extend or templates, ), which are arrays or multidimensional arrays of not well defined items.
     * In these cases the items cannot be validated.
     *
     * @param array  $supportedMetaDataKeys
     * @param string $sectionName
     * @param array  $sectionData
     *
     * @return null
     */
    private function validateMetaDataSection(array $supportedMetaDataKeys, string $sectionName, array $sectionData)
    {
        $this->validateMetaDataKey($supportedMetaDataKeys, $sectionName);
        if (\in_array($sectionName, static::$sectionsExcludedFromItemValidation, true)) {
            return;
        }
        $this->validateMetaDataSectionItems($supportedMetaDataKeys, $sectionName, $sectionData);
    }
}
