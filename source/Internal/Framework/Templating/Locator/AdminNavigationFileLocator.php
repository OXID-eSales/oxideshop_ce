<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator;

use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Exception\AdminUiDirectoryNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Service\AdminUiFileLocatorInterface;

readonly class AdminNavigationFileLocator implements NavigationFileLocatorInterface
{
    private const USER_FILE_NAME = 'user.xml';
    private const MENU_FILE_NAME = 'menu.xml';

    /** @param iterable<NavigationFileLocatorInterface> $menuFileLocators */
    public function __construct(
        private AdminUiFileLocatorInterface $adminUiFileLocator,
        private iterable $menuFileLocators = [],
    ) {
    }

    /**
     * @return string[]
     * @throws AdminUiDirectoryNotFoundException
     */
    public function locate(): array
    {
        $menuFilePaths = [
            $this->adminUiFileLocator->locate(self::USER_FILE_NAME),
            $this->adminUiFileLocator->locate(self::MENU_FILE_NAME),
        ];
        foreach ($this->menuFileLocators as $menuFileLocator) {
            $menuFilePaths[] = $menuFileLocator->locate();
        }

        return array_merge(...$menuFilePaths);
    }
}
