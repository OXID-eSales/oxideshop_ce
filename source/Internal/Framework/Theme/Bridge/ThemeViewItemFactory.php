<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Validator\ThemeConfigurationValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;

readonly class ThemeViewItemFactory implements ThemeViewItemFactoryInterface
{
    public function __construct(
        private ThemeConfigurationDaoInterface $themeConfigurationDao,
        private ThemeStateServiceInterface $themeStateService,
        private ThemeConfigurationValidatorInterface $themeConfigurationValidator
    ) {
    }

    /**
     * @return ThemeViewItem[]
     */
    public function getAll(int $shopId): array
    {
        $items = [];
        foreach ($this->themeConfigurationDao->getAll($shopId) as $themeConfiguration) {
            $items[] = $this->build($themeConfiguration, $shopId);
        }

        return $items;
    }

    public function get(string $themeId, int $shopId): ?ThemeViewItem
    {
        if (!$this->themeConfigurationDao->exists($themeId, $shopId)) {
            return null;
        }

        return $this->build($this->themeConfigurationDao->get($themeId, $shopId), $shopId);
    }

    private function build(ThemeConfiguration $themeConfiguration, int $shopId): ThemeViewItem
    {
        $parent = null;
        if (
            $themeConfiguration->hasParentTheme()
            && $this->themeConfigurationDao->exists($themeConfiguration->getParentTheme(), $shopId)
        ) {
            $parent = $this->build(
                $this->themeConfigurationDao->get($themeConfiguration->getParentTheme(), $shopId),
                $shopId
            );
        }

        return new ThemeViewItem(
            [
                'id' => $themeConfiguration->getId(),
                'title' => $this->firstValue($themeConfiguration->getTitle()),
                'description' => $this->firstValue($themeConfiguration->getDescription()),
                'thumbnail' => $themeConfiguration->getThumbnail(),
                'author' => $themeConfiguration->getAuthor(),
                'version' => $themeConfiguration->getVersion(),
                'parentTheme' => $themeConfiguration->getParentTheme(),
                'parentVersions' => [],
                'active' => $this->themeStateService->isActive($themeConfiguration->getId(), $shopId),
            ],
            $parent,
            $this->resolveActivationError($themeConfiguration, $shopId)
        );
    }

    private function resolveActivationError(ThemeConfiguration $themeConfiguration, int $shopId): string
    {
        try {
            $this->themeConfigurationValidator->validate($themeConfiguration, $shopId);
        } catch (\Throwable) {
            return 'EXCEPTION_PARENT_THEME_NOT_FOUND';
        }

        return '';
    }

    private function firstValue(array $values): string
    {
        $value = reset($values);

        return $value === false ? '' : (string) $value;
    }
}
