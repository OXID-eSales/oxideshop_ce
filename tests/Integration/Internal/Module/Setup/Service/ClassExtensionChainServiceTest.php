<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ClassExtensionsChain;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Service\ActiveClassExtensionChainResolverInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Service\ClassExtensionChainService;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ClassExtensionChainServiceTest extends TestCase
{
    use ContainerTrait;

    public function testUpdateChain()
    {
        $activeClassExtensionChain = new ClassExtensionsChain();
        $activeClassExtensionChain->setChain(
            [
                'shopClassNamespace' => [
                    'activeModule2ExtensionClass',
                    'activeModuleExtensionClass',
                ],
                'anotherShopClassNamespace' => [
                    'activeModuleExtensionClass',
                    'activeModule2ExtensionClass',
                ],
            ]
        );

        $activeClassExtensionChainResolver = $this
            ->getMockBuilder(ActiveClassExtensionChainResolverInterface::class)
            ->getMock();

        $activeClassExtensionChainResolver
            ->method('getActiveExtensionChain')
            ->willReturn($activeClassExtensionChain);

        $shopConfigurationSetting = new ShopConfigurationSetting();
        $shopConfigurationSetting
            ->setName(ShopConfigurationSetting::MODULE_CLASS_EXTENSIONS_CHAIN)
            ->setShopId(1)
            ->setType(ShopSettingType::ASSOCIATIVE_ARRAY)
            ->setValue(
                [
                    'shopClassNamespace'        => 'activeModule2ExtensionClass&activeModuleExtensionClass',
                    'anotherShopClassNamespace' => 'activeModuleExtensionClass&activeModule2ExtensionClass',
                ]
            );

        $shopConfigurationSettingDao = $this->get(ShopConfigurationSettingDaoInterface::class);

        $classExtensionChainService = new ClassExtensionChainService(
            $shopConfigurationSettingDao,
            $activeClassExtensionChainResolver
        );

        $classExtensionChainService->updateChain(1);

        $this->assertEquals(
            $shopConfigurationSetting,
            $shopConfigurationSettingDao->get(ShopConfigurationSetting::MODULE_CLASS_EXTENSIONS_CHAIN, 1)
        );
    }
}
