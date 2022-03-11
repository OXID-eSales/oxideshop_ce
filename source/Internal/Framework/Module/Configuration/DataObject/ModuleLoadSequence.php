<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject;

final class ModuleLoadSequence
{
    public const KEY = 'loadSequence';

    private array $moduleIds;

    /** @param string[] $moduleIds */
    public function __construct(array $moduleIds)
    {
        $this->moduleIds = $moduleIds;
    }

    /** @return string[] */
    public function getConfiguredModulesIds(): array
    {
        return $this->moduleIds;
    }
}
