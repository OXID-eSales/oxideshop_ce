<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Locale\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\Dao\LocaleDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\Locale;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ActiveLocaleProvider implements ActiveLocaleProviderInterface
{
    private Locale $cachedLocale;

    public function __construct(
        private readonly ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao,
        private readonly ContextInterface $context,
        private readonly LocaleDaoInterface $localeDao,
        private readonly string $defaultFallbackLocale,
    ) {
    }

    public function getActiveLocale(): Locale
    {
        if (!isset($this->cachedLocale)) {
            $abbreviation = $this->context->getCurrentLanguageAbbreviation();

            $languageParams = $this->shopConfigurationSettingDao
                ->get('aLanguageParams', $this->context->getCurrentShopId())
                ->getValue();

            $localeCode = $languageParams[$abbreviation]['locale'] ?? $this->defaultFallbackLocale;

            $this->cachedLocale = $this->localeDao->getByCode($localeCode);
        }

        return $this->cachedLocale;
    }
}
