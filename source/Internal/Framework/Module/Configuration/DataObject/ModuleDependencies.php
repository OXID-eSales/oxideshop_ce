<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject;

use RecursiveArrayIterator;

class ModuleDependencies extends RecursiveArrayIterator
{
    public function getRequiredModuleIds(): array
    {
        return $this->offsetExists('modules') ? $this->offsetGet('modules') : [];
    }

    public function isRequiredModule(string $moduleId): bool
    {
        return in_array($moduleId, $this->getRequiredModuleIds(), true);
    }
}
