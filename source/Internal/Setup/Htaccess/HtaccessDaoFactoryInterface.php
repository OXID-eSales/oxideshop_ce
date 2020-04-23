<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Setup\Htaccess;

interface HtaccessDaoFactoryInterface
{
    public function createRootHtaccessDao(): HtaccessDaoInterface;
}
