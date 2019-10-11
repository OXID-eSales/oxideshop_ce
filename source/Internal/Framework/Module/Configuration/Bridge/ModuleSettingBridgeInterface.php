<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge;

interface ModuleSettingBridgeInterface
{
    /**
     * @param string $name
     * @param bool|int|string|array $value
     * @param string $moduleId
     */
    public function save(string $name, $value, string $moduleId): void;

    public function get(string $name, string $moduleId);
}
