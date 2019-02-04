<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\FileSystem;

use Symfony\Component\Finder\Finder;

/**
 * @internal
 */
interface FinderFactoryInterface
{
    /**
     * @return Finder
     */
    public function create(): Finder;
}
