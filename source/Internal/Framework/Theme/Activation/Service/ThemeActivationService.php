<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\Service;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ParameterDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeActivatedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class ThemeActivationService implements ThemeActivationServiceInterface
{
    public function __construct(
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private ParameterDaoInterface $parameterDao,
        private EventDispatcherInterface $eventDispatcher,
        private ThemeParentCompatibilityCheckerInterface $themeParentCompatibilityChecker,
    ) {
    }

    public function activate(string $themeId, int $shopId): void
    {
        if (!$this->themeConfigurationDao->exists($themeId, $shopId)) {
            throw new ThemeConfigurationNotFoundException(
                sprintf('Theme configuration "%s" not found for shop %d', $themeId, $shopId)
            );
        }
        $this->themeParentCompatibilityChecker->validate($themeId, $shopId);

        $this->parameterDao->add('oxid_esales.theme.active', $themeId, $shopId);

        $this->eventDispatcher->dispatch(new ThemeActivatedEvent($shopId, $themeId));
    }
}
