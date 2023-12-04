<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject;

class UnresolvedModuleDependencies
{
    private array $moduleDependencyIds = [];

    public function getModuleIds(): array
    {
        return $this->moduleDependencyIds;
    }

    public function addModuleId(string $moduleId): static
    {
        $this->moduleDependencyIds[] = $moduleId;

        return $this;
    }

    public function hasModuleDependencies(): bool
    {
        return !empty($this->moduleDependencyIds);
    }
}
