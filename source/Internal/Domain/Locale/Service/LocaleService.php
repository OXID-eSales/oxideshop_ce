<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Locale\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\Dao\LocaleDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\Locale;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\Event\LocaleChangedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class LocaleService implements LocaleServiceInterface
{
    public function __construct(
        private LocaleDaoInterface $localeDao,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function getByCode(string $code): Locale
    {
        return $this->localeDao->getByCode($code);
    }

    public function getAll(): array
    {
        return $this->localeDao->getAll();
    }

    public function getForShop(int $shopId): array
    {
        return $this->localeDao->getByShopId($shopId);
    }

    public function add(Locale $locale): void
    {
        $this->localeDao->add($locale);

        $this->dispatchChange($locale->getCode());
    }

    public function update(Locale $locale): void
    {
        $this->localeDao->update($locale);

        $this->dispatchChange($locale->getCode());
    }

    public function delete(string $code): void
    {
        $this->localeDao->delete($code);

        $this->dispatchChange($code);
    }

    public function addToShop(string $localeCode, int $shopId): void
    {
        $this->localeDao->addToShop($localeCode, $shopId);

        $this->dispatchChange($localeCode);
    }

    public function removeFromShop(string $localeCode, int $shopId): void
    {
        $this->localeDao->removeFromShop($localeCode, $shopId);

        $this->dispatchChange($localeCode);
    }

    private function dispatchChange(string $localeCode): void
    {
        $this->eventDispatcher->dispatch(
            new LocaleChangedEvent($localeCode)
        );
    }
}
