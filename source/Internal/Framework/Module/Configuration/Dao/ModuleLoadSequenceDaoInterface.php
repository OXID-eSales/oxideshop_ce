<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleLoadSequence;

interface ModuleLoadSequenceDaoInterface
{
    /**
     * @param int $shopId
     * @return ModuleLoadSequence
     */
    public function get(int $shopId): ModuleLoadSequence;
}
