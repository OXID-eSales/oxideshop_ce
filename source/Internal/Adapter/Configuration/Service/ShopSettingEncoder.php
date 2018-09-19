<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Exception\InvalidShopSettingValueException;
use function unserialize;
use function serialize;

/**
 * @internal
 */
class ShopSettingEncoder implements ShopSettingEncoderInterface
{
    const BOOLEAN           = 'bool';
    const ARRAY             = 'arr';
    const ASSOCIATIVE_ARRAY = 'aarr';

    /**
     * @param string $encodingType
     * @param mixed  $value
     * @return mixed
     */
    public function encode(string $encodingType, $value)
    {
        $this->validateSettingValue($value);

        switch ($encodingType) {
            case self::ARRAY:
            case self::ASSOCIATIVE_ARRAY:
                $encodedValue = serialize($value);
                break;
            case self::BOOLEAN:
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
            case self::ARRAY:
            case self::ASSOCIATIVE_ARRAY:
                $decodedValue = unserialize($value, ['allowed_classes' => false]);
                break;
            case self::BOOLEAN:
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
            throw new InvalidShopSettingValueException();
        }
    }
}
