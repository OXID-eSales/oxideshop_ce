<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge;

interface ModuleSettingBridgeInterface
{
    /**
     * @param bool|int|string|array $value
     */
    public function save(string $name, $value, string $moduleId): void;

    public function get(string $name, string $moduleId);
}
