<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem;

use Symfony\Component\Finder\Finder;

/**
 * @deprecated will be removed in next major, use \Symfony\Component\Finder\Finder::create() instead
 */
interface FinderFactoryInterface
{
    /**
     * @return Finder
     */
    public function create(): Finder;
}
