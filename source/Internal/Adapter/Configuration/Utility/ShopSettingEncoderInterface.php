<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Utility;

/**
 * @internal
 */
interface ShopSettingEncoderInterface
{
    /**
     * @param string $encodingType
     * @param mixed  $value
     * @return mixed
     */
    public function encode(string $encodingType, $value);

    /**
     * @param string $encodingType
     * @param mixed  $value
     * @return mixed
     */
    public function decode(string $encodingType, $value);
}
