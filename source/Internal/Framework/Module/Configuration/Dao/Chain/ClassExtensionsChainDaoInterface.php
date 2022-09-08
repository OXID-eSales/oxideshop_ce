<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\Chain;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ClassExtensionsChain;

interface ClassExtensionsChainDaoInterface
{
    public function getChain(int $shopId): ClassExtensionsChain;
    public function saveChain(int $shopId, ClassExtensionsChain $chain): void;
}