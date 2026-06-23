<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Exception\ParentThemeNotInstalledException;

readonly class ParentThemeValidator implements ThemeConfigurationValidatorInterface
{
    public function __construct(
        private ThemeConfigurationDaoInterface $themeConfigurationDao
    ) {
    }

    public function validate(ThemeConfiguration $themeConfiguration, int $shopId): void
    {
        if (
            $themeConfiguration->hasParentTheme()
            && !$this->themeConfigurationDao->exists($themeConfiguration->getParentTheme(), $shopId)
        ) {
            throw new ParentThemeNotInstalledException(
                'Parent theme "' . $themeConfiguration->getParentTheme()
                . '" of theme "' . $themeConfiguration->getId() . '" is not installed.'
            );
        }
    }
}
