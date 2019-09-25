<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Helper;

/**
 * @internal
 */
interface ModuleIdPreparatorInterface
{
    public function prepare(string $moduleId): string;
}
