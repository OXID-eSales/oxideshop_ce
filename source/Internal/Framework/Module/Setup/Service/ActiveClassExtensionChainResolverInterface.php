<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ClassExtensionsChain;

interface ActiveClassExtensionChainResolverInterface
{
    /**
     * @param int $shopId
     * @return ClassExtensionsChain
     */
    public function getActiveExtensionChain(int $shopId): ClassExtensionsChain;
}
