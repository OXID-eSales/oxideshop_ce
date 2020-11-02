<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Translation\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Translation\{
    Locator\AdminAreaModuleTranslationFileLocatorInterface};

class AdminAreaModuleTranslationFileLocatorBridge implements AdminAreaModuleTranslationFileLocatorBridgeInterface
{
    /**
     * @var AdminAreaModuleTranslationFileLocatorInterface
     */
    private $moduleTranslationFileLocator;

    public function __construct(AdminAreaModuleTranslationFileLocatorInterface $moduleTranslationFileLocator)
    {
        $this->moduleTranslationFileLocator = $moduleTranslationFileLocator;
    }

    public function locate(string $lang): array
    {
        return $this->moduleTranslationFileLocator->locate($lang);
    }
}
