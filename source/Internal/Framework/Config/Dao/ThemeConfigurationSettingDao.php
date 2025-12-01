<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Config\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ThemeConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Utility\ShopSettingEncoderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeSettingChangedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ThemeConfigurationSettingDao implements ThemeConfigurationSettingDaoInterface
{
    private const THEME_MODULE_PREFIX = 'theme:';

    public function __construct(
        private readonly QueryBuilderFactoryInterface $queryBuilderFactory,
        private readonly ShopSettingEncoderInterface $shopSettingEncoder,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    private array $cache = [];

    public function save(ThemeConfigurationSetting $setting): void
    {
        $this->delete($setting);

        $moduleIdentifier = $this->getModuleIdentifier($setting->getThemeId());
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->insert('oxconfig')
            ->values([
                'oxid' => ':id',
                'oxshopid' => ':shopId',
                'oxmodule' => ':module',
                'oxvarname' => ':name',
                'oxvartype' => ':type',
                'oxvarvalue' => ':value',
            ])
            ->setParameters([
                'id' => Id::generate(),
                'shopId' => $setting->getShopId(),
                'module' => $moduleIdentifier,
                'name' => $setting->getName(),
                'type' => $setting->getType(),
                'value' => $this->shopSettingEncoder->encode(
                    $setting->getType(),
                    $setting->getValue()
                ),
            ]);

        $queryBuilder->executeStatement();

        $this->eventDispatcher->dispatch(
            new ThemeSettingChangedEvent(
                $setting->getName(),
                $setting->getShopId(),
                $moduleIdentifier
            )
        );
    }

    public function get(string $name, int $shopId, string $themeId): ThemeConfigurationSetting
    {
        $moduleIdentifier = $this->getModuleIdentifier($themeId);

        if (!isset($this->cache[$shopId][$themeId][$name])) {
            $queryBuilder = $this->queryBuilderFactory->create();
            $queryBuilder
                ->select('oxvarvalue as value, oxvartype as type, oxvarname as name')
                ->from('oxconfig')
                ->where('oxshopid = :shopId')
                ->andWhere('oxvarname = :name')
                ->andWhere('oxmodule = :module')
                ->setParameters([
                    'shopId' => $shopId,
                    'name' => $name,
                    'module' => $moduleIdentifier,
                ]);

            $result = $queryBuilder->fetchAssociative();

            if ($result === false) {
                throw new EntryDoesNotExistDaoException(
                    'Setting ' . $name . ' for theme ' . $themeId . ' does not exist in the shop with id ' . $shopId
                );
            }

            $setting = new ThemeConfigurationSetting();
            $setting
                ->setThemeId($themeId)
                ->setName($name)
                ->setShopId($shopId)
                ->setType($result['type'])
                ->setValue($this->shopSettingEncoder->decode($result['type'], $result['value']));

            $this->cache[$shopId][$themeId][$name] = $setting;
        }

        return clone $this->cache[$shopId][$themeId][$name];
    }

    public function delete(ThemeConfigurationSetting $setting): void
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->delete('oxconfig')
            ->where('oxshopid = :shopId')
            ->andWhere('oxvarname = :name')
            ->andWhere('oxmodule = :module')
            ->setParameters([
                'shopId' => $setting->getShopId(),
                'name' => $setting->getName(),
                'module' => $this->getModuleIdentifier($setting->getThemeId()),
            ]);

        $queryBuilder->executeStatement();

        unset($this->cache[$setting->getShopId()][$setting->getThemeId()][$setting->getName()]);
    }

    private function getModuleIdentifier(string $themeId): string
    {
        return self::THEME_MODULE_PREFIX . $themeId;
    }
}
