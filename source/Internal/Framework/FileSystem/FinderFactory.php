<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem;

use Symfony\Component\Finder\Finder;

class FinderFactory implements FinderFactoryInterface
{
    /**
     * @return Finder
     */
    public function create(): Finder
    {
        return new Finder();
    }
}
