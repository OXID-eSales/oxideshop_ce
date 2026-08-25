<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeActivatedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeParentProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class ThemeActivationService implements ThemeActivationServiceInterface
{
    public function __construct(
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private EventDispatcherInterface $eventDispatcher,
        private ThemeParentProviderInterface $themeParentProvider,
        private ThemeParentCompatibilityCheckerInterface $themeParentCompatibilityChecker,
    ) {
    }

    public function activate(string $themeId, int $shopId): void
    {
        $this->validateParentCompatibility($themeId, $shopId);

        $themeConfiguration = $this->themeConfigurationDao->get($themeId, $shopId);

        $this->deactivateActiveThemes($themeId, $shopId);

        $themeConfiguration->setActivated(true);
        $this->themeConfigurationDao->save($themeConfiguration, $shopId);

        $this->eventDispatcher->dispatch(new ThemeActivatedEvent($shopId, $themeId));
    }

    private function validateParentCompatibility(string $themeId, int $shopId): void
    {
        if ($this->themeParentProvider->hasParentTheme($themeId, $shopId)) {
            $this->themeParentCompatibilityChecker->validate(
                $themeId,
                $this->themeParentProvider->getParentThemeId($themeId, $shopId),
                $shopId
            );
        }
    }

    private function deactivateActiveThemes(string $exceptThemeId, int $shopId): void
    {
        foreach ($this->themeConfigurationDao->getAll($shopId) as $themeConfiguration) {
            if ($themeConfiguration->getId() !== $exceptThemeId && $themeConfiguration->isActivated()) {
                $themeConfiguration->setActivated(false);
                $this->themeConfigurationDao->save($themeConfiguration, $shopId);
            }
        }
    }
}
