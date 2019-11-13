<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator;

/**
 * Interface NavigationFileLocatorInterface
 * @package OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator
 */
interface NavigationFileLocatorInterface
{
    /**
     * Returns a full path for a given file name.
     *
     * @return array An array of file paths
     */
    public function locate(): array;
}
