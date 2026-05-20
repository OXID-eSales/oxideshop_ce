<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Locale\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\Dao\LocaleDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\LocaleChain;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class LocaleChainResolver implements LocaleChainResolverInterface
{
    /** @var array<int, array<string, LocaleChain>> */
    private array $cache = [];

    public function __construct(
        private readonly LocaleDaoInterface $localeDao,
        private readonly ContextInterface $context,
        private readonly string $globalFallbackLocaleCode,
    ) {
    }

    public function getActiveFallbackChain(string $localeCode): LocaleChain
    {
        return $this->cache[$this->context->getCurrentShopId()][$localeCode]
            ??= $this->createActiveFallbackChain($localeCode);
    }

    private function createActiveFallbackChain(string $localeCode): LocaleChain
    {
        $activeLocale = $this->localeDao->getByCode($localeCode);
        $activeLocaleCodes = $this->getActiveLocaleCodes();
        $candidates = array_unique([
            $activeLocale->getCode(),
            $activeLocale->getFallbackCode(),
            $this->globalFallbackLocaleCode,
        ]);

        $codes = [];
        foreach ($candidates as $candidateCode) {
            if (in_array($candidateCode, $activeLocaleCodes, true)) {
                $codes[] = $candidateCode;
            }
        }
        return new LocaleChain($codes);
    }

    /** @return string[] */
    private function getActiveLocaleCodes(): array
    {
        $codes = [];
        foreach ($this->localeDao->getByShopId($this->context->getCurrentShopId()) as $locale) {
            $codes[] = $locale->getCode();
        }
        return $codes;
    }
}
