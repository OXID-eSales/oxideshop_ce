<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\SettingNotValidException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Service\MetaDataProvider;

class ModuleSettingBooleanValidator implements MetaDataValidatorInterface
{
    private const ALLOWED_VALUES = [
        0,
        1,
        '0',
        '1',
        'true',
        'false',
        true,
        false,
    ];

    /**
     * @param array $metaData
     *
     * @throws SettingNotValidException
     */
    public function validate(array $metaData): void
    {
        if (isset($metaData[MetaDataProvider::METADATA_SETTINGS])) {
            $settings = $metaData[MetaDataProvider::METADATA_SETTINGS];
            foreach ($settings as $setting) {
                $this->validateSetting($metaData, $setting);
            }
        }
    }

    /**
     * @param array $metaData
     * @param array $setting
     * @throws SettingNotValidException
     */
    private function validateSetting(array $metaData, array $setting): void
    {
        if (isset($setting['type']) && $setting['type'] === 'bool') {
            $value = is_string($setting['value']) ? strtolower($setting['value']) : $setting['value'];
            if (!in_array($value, self::ALLOWED_VALUES, true)) {
                throw new SettingNotValidException(
                    'Invalid boolean value- "' . $setting['value'] . '" was used for module setting. '
                    . 'Please update setting value in module "' . $metaData[MetaDataProvider::METADATA_ID] . '".'
                );
            }
        }
    }
}
