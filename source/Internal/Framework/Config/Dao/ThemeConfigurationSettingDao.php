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

    public function save(ThemeConfigurationSetting $themeConfigurationSetting): void
    {
        $this->delete($themeConfigurationSetting);

        $moduleIdentifier = $this->getModuleIdentifier($themeConfigurationSetting->getThemeId());
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
                'shopId' => $themeConfigurationSetting->getShopId(),
                'module' => $moduleIdentifier,
                'name' => $themeConfigurationSetting->getName(),
                'type' => $themeConfigurationSetting->getType(),
                'value' => $this->shopSettingEncoder->encode(
                    $themeConfigurationSetting->getType(),
                    $themeConfigurationSetting->getValue()
                ),
            ]);

        $queryBuilder->executeStatement();

        $this->eventDispatcher->dispatch(
            new ThemeSettingChangedEvent(
                $themeConfigurationSetting->getName(),
                $themeConfigurationSetting->getShopId(),
                $moduleIdentifier
            )
        );
    }

    public function get(string $name, int $shopId, string $themeId): ThemeConfigurationSetting
    {
        $moduleIdentifier = $this->getModuleIdentifier($themeId);

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

        return $setting;
    }

    public function delete(ThemeConfigurationSetting $themeConfigurationSetting): void
    {
        $moduleIdentifier = $this->getModuleIdentifier($themeConfigurationSetting->getThemeId());

        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->delete('oxconfig')
            ->where('oxshopid = :shopId')
            ->andWhere('oxvarname = :name')
            ->andWhere('oxmodule = :module')
            ->setParameters([
                'shopId' => $themeConfigurationSetting->getShopId(),
                'name' => $themeConfigurationSetting->getName(),
                'module' => $moduleIdentifier,
            ]);

        $queryBuilder->executeStatement();
    }

    private function getModuleIdentifier(string $themeId): string
    {
        return self::THEME_MODULE_PREFIX . $themeId;
    }
}
