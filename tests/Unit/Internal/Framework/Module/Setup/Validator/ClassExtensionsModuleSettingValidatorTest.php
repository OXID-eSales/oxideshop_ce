<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator\ClassExtensionsValidator;
use PHPUnit\Framework\TestCase;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ClassExtension;

/**
 * @internal
 */
class ClassExtensionsModuleSettingValidatorTest extends TestCase
{
    public function testValidClassExtensionsModuleSetting()
    {
        $anyExistentClass = self::class;

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
        $moduleConfiguration->addClassExtension(new ClassExtension($anyExistentClass, 'moduleClass'));

        $this->assertNull(
            $validator->validate($moduleConfiguration, 1)
        );
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\InvalidClassExtensionNamespaceException
     */
    public function testNamespaceOfPatchedClassMustNotBeShopEditionNamespace()
    {
        $shopAdapter = $this->getMockBuilder(ShopAdapterInterface::class)->getMock();
        $shopAdapter
            ->method('isNamespace')
            ->willReturn(true);
        $shopAdapter
            ->method('isShopEditionNamespace')
            ->willReturn(true);

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addClassExtension(new ClassExtension('shopClass', 'moduleClass'));

        $validator = new ClassExtensionsValidator($shopAdapter);
        $validator->validate($moduleConfiguration, 1);
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\InvalidClassExtensionNamespaceException
     */
    public function testNamespaceOfPatchedClassIsShopUnifiedNamespaceButClassDoesNotExist()
    {
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
        $moduleConfiguration->addClassExtension(new ClassExtension('nonExistentClass', 'moduleClass'));

        $validator->validate($moduleConfiguration, 1);
    }
}
