<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Path;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Path;

readonly class ThemePathResolver implements ThemePathResolverInterface
{
    public function __construct(
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private BasicContextInterface $context,
    ) {
    }

    /**
     * @throws ThemeConfigurationNotFoundException
     */
    public function getFullThemePathFromConfiguration(string $themeId, int $shopId): string
    {
        $themeConfiguration = $this->themeConfigurationDao->get($themeId, $shopId);

        return Path::join($this->context->getShopRootPath(), $themeConfiguration->getSource());
    }
}
