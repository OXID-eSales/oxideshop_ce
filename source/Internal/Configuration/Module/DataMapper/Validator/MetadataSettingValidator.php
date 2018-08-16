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
     * @param string        $metadataVersion
     * @param ModuleSetting $moduleSetting
     */
    public function validate(string $metadataVersion, ModuleSetting $moduleSetting)
    {
        $this->validateMetadataVersion($metadataVersion);
        $this->validateSettingExistence($metadataVersion, $moduleSetting);
    }

    /**
     * @param string $metadataVersion
     * @param array  $settingList
     */
    public function addAllowableMetadataSettings(string $metadataVersion, array $settingList)
    {
        $this->allowableSettings[$metadataVersion] = $settingList;
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
