<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject;

use RecursiveArrayIterator;

class ModuleTemplateExtensionChain extends RecursiveArrayIterator
{
    public function getTemplateLoadingPriority(string $templateName): ModuleIdChain
    {
        return new ModuleIdChain(
            $this->offsetExists($templateName) ? $this->offsetGet($templateName) : []
        );
    }
}
