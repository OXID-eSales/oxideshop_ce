<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\Configuration;

/**
 * @internal
 */
interface ShopConfigurationSettingDaoInterface
{
    /**
     * @param string $name
     * @param mixed  $value
     * @param int    $shopId
     */
    public function save(string $name, $value, int $shopId);

    /**
     * @param string $name
     * @param int    $shopId
     * @return mixed
     */
    public function get(string $name, int $shopId);
}
