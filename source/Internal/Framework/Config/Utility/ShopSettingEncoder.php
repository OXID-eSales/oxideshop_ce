<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Config\Utility;

use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Exception\InvalidShopSettingValueException;
use function unserialize;
use function serialize;

class ShopSettingEncoder implements ShopSettingEncoderInterface
{
    /**
     * @param string $encodingType
     * @param mixed  $value
     * @return mixed
     */
    public function encode(string $encodingType, $value)
    {
        $this->validateSettingValue($value);

        switch ($encodingType) {
            case ShopSettingType::ARRAY:
            case ShopSettingType::ASSOCIATIVE_ARRAY:
                $encodedValue = serialize($value);
                break;
            case ShopSettingType::BOOLEAN:
                $encodedValue = $value === true ? '1' : '';
                break;
            default:
                $encodedValue = $value;
        }

        return $encodedValue;
    }

    /**
     * @param string $encodingType
     * @param mixed  $value
     * @return mixed
     */
    public function decode(string $encodingType, $value)
    {
        switch ($encodingType) {
            case ShopSettingType::ARRAY:
            case ShopSettingType::ASSOCIATIVE_ARRAY:
                $decodedValue = unserialize($value, ['allowed_classes' => false]);
                break;
            case ShopSettingType::BOOLEAN:
                $decodedValue = ($value === 'true' || $value === '1');
                break;
            default:
                $decodedValue = $value;
        }

        return $decodedValue;
    }

    /**
     * @param mixed $value
     * @throws InvalidShopSettingValueException
     */
    private function validateSettingValue($value)
    {
        if (is_object($value)) {
            throw new InvalidShopSettingValueException(
                'Shop setting value must not be an object.'
            );
        }
    }
}
