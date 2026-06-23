<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeActivatedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Event\BeforeThemeDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Event\FinalizingThemeActivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Event\FinalizingThemeDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Validator\ThemeConfigurationValidatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class ThemeActivationService implements ThemeActivationServiceInterface
{
    public function __construct(
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private EventDispatcherInterface $eventDispatcher,
        private ThemeConfigurationValidatorInterface $themeConfigurationValidator
    ) {
    }

    public function activate(string $themeId, int $shopId): void
    {
        $themeConfiguration = $this->themeConfigurationDao->get($themeId, $shopId);

        $this->themeConfigurationValidator->validate($themeConfiguration, $shopId);

        $this->deactivateActiveThemes($shopId, $themeId);

        $themeConfiguration->setActivated(true);
        $this->themeConfigurationDao->save($themeConfiguration, $shopId);

        $this->eventDispatcher->dispatch(new FinalizingThemeActivationEvent($shopId, $themeId));
        $this->eventDispatcher->dispatch(new ThemeActivatedEvent($shopId, $themeId));
    }

    public function deactivate(string $themeId, int $shopId): void
    {
        $themeConfiguration = $this->themeConfigurationDao->get($themeId, $shopId);

        $this->eventDispatcher->dispatch(new BeforeThemeDeactivationEvent($shopId, $themeId));

        $themeConfiguration->setActivated(false);
        $this->themeConfigurationDao->save($themeConfiguration, $shopId);

        $this->eventDispatcher->dispatch(new FinalizingThemeDeactivationEvent($shopId, $themeId));
    }

    private function deactivateActiveThemes(int $shopId, string $exceptThemeId): void
    {
        foreach ($this->themeConfigurationDao->getAll($shopId) as $themeConfiguration) {
            if ($themeConfiguration->getId() !== $exceptThemeId && $themeConfiguration->isActivated()) {
                $this->deactivate($themeConfiguration->getId(), $shopId);
            }
        }
    }
}
