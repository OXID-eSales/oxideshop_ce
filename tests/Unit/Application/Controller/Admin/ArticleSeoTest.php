<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\ArticleSeo;
use OxidEsales\Eshop\Application\Model\Manufacturer;
use OxidEsales\Eshop\Application\Model\Vendor;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * @internal
 */
#[AllowMockObjectsWithoutExpectations]
class ArticleSeoTest extends TestCase
{
    public function testCategoryContextAddsCnid(): void
    {
        $controller = $this->createControllerMock(['getActCatType', 'getActCatId']);
        $controller->method('getActCatType')->willReturn('oxcategory');
        $controller->method('getActCatId')->willReturn('catid123');

        $this->assertSame(['cnid' => 'catid123'], $this->invokeGetContextStdParams($controller));
    }

    public function testVendorContextAddsPrefixedCnidAndListType(): void
    {
        $vendor = $this->getMockBuilder(Vendor::class)
            ->disableOriginalConstructor()->onlyMethods(['getId'])->getMock();
        $vendor->method('getId')->willReturn('vendor123');

        $controller = $this->createControllerMock(['getActCatType', 'getActVendor', 'getListType']);
        $controller->method('getActCatType')->willReturn('oxvendor');
        $controller->method('getActVendor')->willReturn($vendor);
        $controller->method('getListType')->willReturn('vendor');

        $this->assertSame(
            ['cnid' => 'v_vendor123', 'listtype' => 'vendor'],
            $this->invokeGetContextStdParams($controller)
        );
    }

    public function testManufacturerContextAddsMnidAndListType(): void
    {
        $manufacturer = $this->getMockBuilder(Manufacturer::class)
            ->disableOriginalConstructor()->onlyMethods(['getId'])->getMock();
        $manufacturer->method('getId')->willReturn('man123');

        $controller = $this->createControllerMock(['getActCatType', 'getActManufacturer', 'getListType']);
        $controller->method('getActCatType')->willReturn('oxmanufacturer');
        $controller->method('getActManufacturer')->willReturn($manufacturer);
        $controller->method('getListType')->willReturn('manufacturer');

        $this->assertSame(
            ['mnid' => 'man123', 'listtype' => 'manufacturer'],
            $this->invokeGetContextStdParams($controller)
        );
    }

    public function testNoSelectedCategoryReturnsEmptyArray(): void
    {
        $controller = $this->createControllerMock(['getActCatType', 'getActCatId']);
        $controller->method('getActCatType')->willReturn('oxcategory');
        $controller->method('getActCatId')->willReturn(false);

        $this->assertSame([], $this->invokeGetContextStdParams($controller));
    }

    public function testVendorContextWithoutVendorReturnsEmptyArray(): void
    {
        $controller = $this->createControllerMock(['getActCatType', 'getActVendor']);
        $controller->method('getActCatType')->willReturn('oxvendor');
        $controller->method('getActVendor')->willReturn(null);

        $this->assertSame([], $this->invokeGetContextStdParams($controller));
    }

    private function createControllerMock(array $methods): ArticleSeo
    {
        return $this->getMockBuilder(ArticleSeo::class)
            ->disableOriginalConstructor()
            ->onlyMethods($methods)
            ->getMock();
    }

    private function invokeGetContextStdParams(ArticleSeo $controller): array
    {
        $method = new ReflectionMethod(ArticleSeo::class, 'getContextStdParams');
        $method->setAccessible(true);

        return $method->invoke($controller);
    }
}
