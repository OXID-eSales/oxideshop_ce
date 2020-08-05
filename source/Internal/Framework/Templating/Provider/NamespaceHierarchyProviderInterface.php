<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Provider;

interface NamespaceHierarchyProviderInterface
{
    /**
     * Returns array with template namespace hierarchy starting with the child
     * [child, parent, ancestor]
     * @return array
     */
    public function getHierarchyAscending(): array;
}
