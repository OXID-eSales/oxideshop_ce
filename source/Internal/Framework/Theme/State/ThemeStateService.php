<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\State;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeActivatedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeConfigurationChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\ThemeInheritanceResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ThemeStateService implements ThemeStateServiceInterface, EventSubscriberInterface
{
    private array $activeThemeIds = [];
    private array $activeThemes = [];

    public function __construct(
        private readonly ThemeConfigurationDaoInterface $themeConfigurationDao,
        private readonly ThemeInheritanceResolverInterface $themeInheritanceResolver,
    ) {
    }

    public function isActive(string $themeId, int $shopId): bool
    {
        return $this->themeConfigurationDao->exists($themeId, $shopId)
            && $this->themeConfigurationDao->get($themeId, $shopId)->isActivated();
    }

    public function getActiveThemeId(int $shopId): string
    {
        return $this->activeThemeIds[$shopId] ??= $this->findActiveThemeId($shopId);
    }

    public function getActiveTheme(int $shopId): ActiveTheme
    {
        return $this->activeThemes[$shopId] ??= new ActiveTheme(
            $this->themeInheritanceResolver->resolve($this->getActiveThemeId($shopId), $shopId)
        );
    }

    public function invalidateActiveThemeCache(ThemeActivatedEvent|ThemeConfigurationChangedEvent $event): void
    {
        unset($this->activeThemeIds[$event->getShopId()], $this->activeThemes[$event->getShopId()]);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ThemeActivatedEvent::class => 'invalidateActiveThemeCache',
            ThemeConfigurationChangedEvent::class => 'invalidateActiveThemeCache',
        ];
    }

    private function findActiveThemeId(int $shopId): string
    {
        foreach ($this->themeConfigurationDao->getAll($shopId) as $themeConfiguration) {
            if ($themeConfiguration->isActivated()) {
                return $themeConfiguration->getId();
            }
        }

        throw new ActiveThemeNotFoundException();
    }
}
