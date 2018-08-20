<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper\Validator;

use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleSetting;

/**
 * @internal
 */
class MetadataSettingValidator implements SettingValidatorInterface
{
    /**
     * @var array
     */
    private $allowableSettings;

    /**
     * MetadataSettingValidator constructor.
     * @param array $allowableSettings
     */
    public function __construct(array $allowableSettings)
    {
        $this->allowableSettings = $allowableSettings;
    }

    /**
     * @param string $metadataVersion
     * @param array  $moduleSettings
     *
     * @throws MetadataSettingException
     * @throws MetadataVersionException
     */
    public function validate(string $metadataVersion, array $moduleSettings)
    {
        $this->validateMetadataVersion($metadataVersion);

        foreach ($moduleSettings as $moduleSetting) {
            $this->validateSettingExistence($metadataVersion, $moduleSetting);
        }
    }

    /**
     * @param string $metadataVersion
     *
     * @throws MetadataVersionException
     */
    private function validateMetadataVersion(string $metadataVersion)
    {
        if (isset($this->allowableSettings[$metadataVersion]) === false) {
            throw new MetadataVersionException('Metadata version ' . $metadataVersion . ' does not exist');
        }
    }

    /**
     * @param string        $metadataVersion
     * @param ModuleSetting $moduleSetting
     *
     * @throws MetadataSettingException
     */
    public function validateSettingExistence(string $metadataVersion, ModuleSetting $moduleSetting)
    {
        if (!in_array(
            $moduleSetting->getName(),
            $this->allowableSettings[$metadataVersion]
        )) {
            throw new MetadataSettingException(
                'Setting ' . $moduleSetting->getName() . ' does not exist for metadata version' . $metadataVersion
            );
        }
    }
}
