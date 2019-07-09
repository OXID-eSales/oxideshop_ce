<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Validator\ClassExtensionsValidator;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ClassExtensionsModuleSettingValidatorTest extends TestCase
{
    public function testValidClassExtensionsModuleSetting()
    {
        $anyExistentClass = self::class;

        $classExtensions = new ModuleSetting(ModuleSetting::CLASS_EXTENSIONS, [
            $anyExistentClass => 'moduleClass',
        ]);

        $shopAdapter = $this->getMockBuilder(ShopAdapterInterface::class)->getMock();
        $shopAdapter
            ->method('isNamespace')
            ->willReturn(true);
        $shopAdapter
            ->method('isShopEditionNamespace')
            ->willReturn(false);
        $shopAdapter
            ->method('isShopUnifiedNamespace')
            ->willReturn(true);

        $validator = new ClassExtensionsValidator($shopAdapter);

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addSetting($classExtensions);

        $this->assertNull(
            $validator->validate($moduleConfiguration, 1)
        );
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\InvalidClassExtensionNamespaceException
     */
    public function testNamespaceOfPatchedClassMustNotBeShopEditionNamespace()
    {
        $classExtensions = new ModuleSetting(ModuleSetting::CLASS_EXTENSIONS, [
            'shopClass' => 'moduleClass',
        ]);

        $shopAdapter = $this->getMockBuilder(ShopAdapterInterface::class)->getMock();
        $shopAdapter
            ->method('isNamespace')
            ->willReturn(true);
        $shopAdapter
            ->method('isShopEditionNamespace')
            ->willReturn(true);

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addSetting($classExtensions);

        $validator = new ClassExtensionsValidator($shopAdapter);
        $validator->validate($moduleConfiguration, 1);
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\InvalidClassExtensionNamespaceException
     */
    public function testNamespaceOfPatchedClassIsShopUnifiedNamespaceButClassDoesNotExist()
    {
        $classExtensions = new ModuleSetting(ModuleSetting::CLASS_EXTENSIONS, [
            'nonExistentClass' => 'moduleClass',
        ]);

        $shopAdapter = $this->getMockBuilder(ShopAdapterInterface::class)->getMock();
        $shopAdapter
            ->method('isNamespace')
            ->willReturn(true);
        $shopAdapter
            ->method('isShopEditionNamespace')
            ->willReturn(false);
        $shopAdapter
            ->method('isShopUnifiedNamespace')
            ->willReturn(true);

        $validator = new ClassExtensionsValidator($shopAdapter);

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addSetting($classExtensions);

        $validator->validate($moduleConfiguration, 1);
    }
}
