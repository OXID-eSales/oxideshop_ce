<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

/**
 * @stable
 *
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
interface ModuleConfigurationDaoBridgeInterface
{
    public function get(string $moduleId): ModuleConfiguration;

    public function save(ModuleConfiguration $moduleConfiguration);
}
