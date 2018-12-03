<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Validator\ClassExtensionsModuleSettingValidator;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ClassExtensionsModuleSettingValidatorTest extends TestCase
{
    public function testCanValidate()
    {
        $validator = new ClassExtensionsModuleSettingValidator(
            $this->getMockBuilder(ShopAdapterInterface::class)->getMock()
        );

        $this->assertTrue(
            $validator->canValidate(
                new ModuleSetting(ModuleSetting::CLASS_EXTENSIONS, [])
            )
        );
    }

    public function testCanNotValidate()
    {
        $validator = new ClassExtensionsModuleSettingValidator(
            $this->getMockBuilder(ShopAdapterInterface::class)->getMock()
        );

        $this->assertFalse(
            $validator->canValidate(
                new ModuleSetting('anotherSetting', [])
            )
        );
    }

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

        $validator = new ClassExtensionsModuleSettingValidator($shopAdapter);

        $this->assertNull(
            $validator->validate($classExtensions, 'testModule', 1)
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

        $validator = new ClassExtensionsModuleSettingValidator($shopAdapter);
        $validator->validate($classExtensions, 'testModule', 1);
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

        $validator = new ClassExtensionsModuleSettingValidator($shopAdapter);
        $validator->validate($classExtensions, 'testModule', 1);
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\WrongModuleSettingException
     */
    public function testValidateThrowsExceptionIfNotAbleToValidateSetting()
    {
        $validator = new ClassExtensionsModuleSettingValidator(
            $this->getMockBuilder(ShopAdapterInterface::class)->getMock()
        );

        $moduleSetting = new ModuleSetting(
            'SettingWhichIsNotAbleToBeValidated',
            ['onActivate' => 'MyClass::activate']
        );
        $validator->validate($moduleSetting, 'testModule', 1);
    }
}
