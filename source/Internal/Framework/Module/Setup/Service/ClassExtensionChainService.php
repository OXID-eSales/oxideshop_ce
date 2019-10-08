<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ClassExtensionsChain;

class ClassExtensionChainService implements ExtensionChainServiceInterface
{
    /**
     * @var ShopConfigurationSettingDaoInterface
     */
    private $shopConfigurationSettingDao;

    /**
     * @var ActiveClassExtensionChainResolverInterface
     */
    private $activeClassExtensionChainResolver;

    /**
     * @param ShopConfigurationSettingDaoInterface       $shopConfigurationSettingDao
     * @param ActiveClassExtensionChainResolverInterface $activeClassExtensionChainResolver
     */
    public function __construct(
        ShopConfigurationSettingDaoInterface        $shopConfigurationSettingDao,
        ActiveClassExtensionChainResolverInterface  $activeClassExtensionChainResolver
    ) {
        $this->shopConfigurationSettingDao = $shopConfigurationSettingDao;
        $this->activeClassExtensionChainResolver = $activeClassExtensionChainResolver;
    }

    /**
     * @param int $shopId
     */
    public function updateChain(int $shopId)
    {
        $activeClassExtensionChain = $this->activeClassExtensionChainResolver->getActiveExtensionChain($shopId);
        $formattedClassExtensions = $this->formatClassExtensionChain($activeClassExtensionChain);

        $shopConfigurationSetting = $this->getClassExtensionChainShopConfigurationSetting($shopId);
        $shopConfigurationSetting->setValue($formattedClassExtensions);

        $this->shopConfigurationSettingDao->save($shopConfigurationSetting);
    }

    /**
     * @param ClassExtensionsChain $chain
     * @return array
     */
    private function formatClassExtensionChain(ClassExtensionsChain $chain): array
    {
        $classExtensions = [];

        foreach ($chain as $shopClass => $moduleExtensionClasses) {
            $classExtensions[$shopClass] = implode('&', $moduleExtensionClasses);
        }

        return $classExtensions;
    }

    /**
     * @param int $shopId
     * @return ShopConfigurationSetting
     */
    private function getClassExtensionChainShopConfigurationSetting(int $shopId): ShopConfigurationSetting
    {
        try {
            $shopConfigurationSetting = $this->shopConfigurationSettingDao->get(
                ShopConfigurationSetting::MODULE_CLASS_EXTENSIONS_CHAIN,
                $shopId
            );
        } catch (EntryDoesNotExistDaoException $exception) {
            $shopConfigurationSetting = new ShopConfigurationSetting();
            $shopConfigurationSetting
                ->setShopId($shopId)
                ->setName(ShopConfigurationSetting::MODULE_CLASS_EXTENSIONS_CHAIN)
                ->setType(ShopSettingType::ASSOCIATIVE_ARRAY);
        }

        return $shopConfigurationSetting;
    }
}
