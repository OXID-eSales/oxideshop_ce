<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\Chain;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleTemplateExtensionChain;

interface TemplateExtensionChainDaoInterface
{
    public function getChain(int $shopId): ModuleTemplateExtensionChain;
}
