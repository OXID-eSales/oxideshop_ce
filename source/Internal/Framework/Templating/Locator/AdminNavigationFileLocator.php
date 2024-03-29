<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator\NavigationFileLocatorInterface;

/**
 * Class AdminNavigationFileLocator
 * @package OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator
 */
class AdminNavigationFileLocator implements NavigationFileLocatorInterface
{
    /**
     * @param NavigationFileLocatorInterface[] $menuFileLocators
     */
    public function __construct(private iterable $menuFileLocators = [])
    {
    }

    /**
     * Returns a full path for a given file name.
     *
     * @return array An array of file paths
     *
     * @throws \Exception
     */
    public function locate(): array
    {
        $menuFilePaths = [];
        foreach ($this->menuFileLocators as $locator) {
            $menuFilePaths[] = $locator->locate();
        }
        return array_merge([], ...$menuFilePaths);
    }
}
