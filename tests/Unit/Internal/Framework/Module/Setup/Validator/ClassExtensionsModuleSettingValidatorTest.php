<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator\ClassExtensionsValidator;
use PHPUnit\Framework\TestCase;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ClassExtension;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\InvalidClassExtensionNamespaceException;

/**
 * @internal
 */
final class ClassExtensionsModuleSettingValidatorTest extends TestCase
{
    public function testValidClassExtensionsModuleSetting(): void
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

    public function testNamespaceOfPatchedClassMustNotBeShopEditionNamespace(): void
    {
        $this->expectException(InvalidClassExtensionNamespaceException::class);
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
        $this->expectException(InvalidClassExtensionNamespaceException::class);
        $validator->validate($moduleConfiguration, 1);
    }

    public function testNamespaceOfPatchedClassIsShopUnifiedNamespaceButClassDoesNotExist(): void
    {
        $this->expectException(InvalidClassExtensionNamespaceException::class);
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

        $this->expectException(InvalidClassExtensionNamespaceException::class);
        $validator->validate($moduleConfiguration, 1);
    }
}
