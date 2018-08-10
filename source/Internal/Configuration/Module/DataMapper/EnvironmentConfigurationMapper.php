<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */


namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper;


use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\EnvironmentConfiguration;

/**
 * Class EnvironmentConfigurationMapper
 *
 * @package OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper
 */
class EnvironmentConfigurationMapper
{

    /**
     * @param $object
     *
     * @return array
     */
    public function toData($object): array
    {
        return [];
    }

    /**
     * @param array                     $data
     *
     * @param ShopConfigurationMapper   $shopConfigurationMapper
     * @param ModuleConfigurationMapper $moduleConfigurationMapper
     * @param ModuleSettingMapper       $moduleSettingMapper
     *
     * @return mixed|EnvironmentConfiguration
     */
    public function fromData(
        array $data,
        ShopConfigurationMapper $shopConfigurationMapper,
        ModuleConfigurationMapper $moduleConfigurationMapper,
        ModuleSettingMapper $moduleSettingMapper
    )
    {
        $environmentConfiguration = new EnvironmentConfiguration();

        foreach ($data['shops'] as $shopId => $shopData) {
            $shopConfiguration = $shopConfigurationMapper->fromData(
                $shopData // ,
                // TODO $moduleConfigurationMapper,
                // TODO $moduleSettingMapper
            );

            $environmentConfiguration->setShopConfiguration($shopId, $shopConfiguration);
        }

        return $environmentConfiguration;
    }
}