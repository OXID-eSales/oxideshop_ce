<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeActivatedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\Exception\ThemeInheritanceMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\ThemeInheritanceResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class ThemeActivationService implements ThemeActivationServiceInterface
{
    public function __construct(
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private EventDispatcherInterface $eventDispatcher,
        private ThemeParentCompatibilityCheckerInterface $themeParentCompatibilityChecker,
        private ThemeInheritanceResolverInterface $themeInheritanceResolver,
    ) {
    }

    public function activate(string $themeId, int $shopId): void
    {
        $this->validateActivatable($themeId, $shopId);

        $themeConfiguration = $this->themeConfigurationDao->get($themeId, $shopId);

        $this->deactivateActiveThemes($themeId, $shopId);

        $themeConfiguration->setActivated(true);
        $this->themeConfigurationDao->save($themeConfiguration, $shopId);

        $this->eventDispatcher->dispatch(new ThemeActivatedEvent($shopId, $themeId));
    }

    public function validateActivatable(string $themeId, int $shopId): void
    {
        try {
            $inheritance = $this->themeInheritanceResolver->resolve($themeId, $shopId);
        } catch (InvalidThemeMetaDataException $exception) {
            throw new ThemeInheritanceMetaDataException(
                "Could not read metadata of theme '$themeId': {$exception->getMessage()}",
                previous: $exception
            );
        }

        if ($inheritance->hasParentTheme()) {
            $this->themeParentCompatibilityChecker->validate(
                $themeId,
                $inheritance->getParentThemeId(),
                $shopId
            );
        }

        $this->themeConfigurationDao->get($themeId, $shopId);
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
