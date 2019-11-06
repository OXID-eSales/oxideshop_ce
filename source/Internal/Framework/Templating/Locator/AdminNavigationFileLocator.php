<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator;

/**
 * Class AdminNavigationFileLocator
 * @package OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator
 */
class AdminNavigationFileLocator implements NavigationFileLocatorInterface
{
    /**
     * @var NavigationFileLocatorInterface[]
     */
    private $menuFileLocators;

    /**
     * AdminNavigationFileLocator constructor.
     *
     * @param iterable $menuFileLocators
     */
    public function __construct(iterable $menuFileLocators = [])
    {
        $this->menuFileLocators = $menuFileLocators;
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
