<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * Class ControllerClassNameResolverTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Unit\Core
 */
class ControllerClassNameResolverTest extends UnitTestCase
{
    /**
     * Test mapping class name to id, result found in shop controller map.
     */
    public function testGetClassNameByIdFromShop()
    {
        $resolver = $this->getResolver();
        $this->assertEquals('OxidEsales\EshopCommunity\Application\SomeOtherController', $resolver->getClassNameById('bbb'));
    }

    /**
     * Test mapping class name to id, result found in module controller map.
     */
    public function testGetClassNameByIdFromModule()
    {
        $resolver = $this->getResolver();
        $this->assertEquals('Vendor2\OtherTestModule\SomeDifferentController', $resolver->getClassNameById('eee'));
    }

    /**
     * Test mapping class name to id, no result found in either map.
     */
    public function testGetClassNameByIdNoMatch()
    {
        $resolver = $this->getResolver();
        $this->assertNull($resolver->getClassNameById('zzz'));
    }

    /**
     * Verify that finding a match is not type sensitive.
     */
    public function testGetClassNameByIdNotTypeSensitive()
    {
        $resolver = $this->getResolver();
        $this->assertEquals('OxidEsales\EshopCommunity\Application\SomeDifferentController', $resolver->getClassNameById('ccc'));
    }

    /**
     * Test mapping id to class name, result found in shop controller map.
     */
    public function testGetIdByClassNameFromShop()
    {
        $resolver = $this->getResolver();
        $this->assertEquals('bbb', $resolver->getIdByClassName('OxidEsales\EshopCommunity\Application\SomeOtherController'));
    }

    /**
     * Test mapping id to class name, result found in module controller map.
     */
    public function testGetIdByClassNameFromModule()
    {
        $resolver = $this->getResolver();
        $this->assertEquals('eee', $resolver->getIdByClassName('Vendor2\OtherTestModule\SomeDifferentController'));
    }

    /**
     * Test mapping id to class name, no result found in either map.
     */
    public function testGetIdByClassNameNoMatch()
    {
        $resolver = $this->getResolver();
        $this->assertNull($resolver->getIdByClassName('novendor\noclass'));
    }

    /**
     * Verify that finding a match is not type sensitive.
     */
    public function testGetIdByClassNameNotTypeSensitive()
    {
        $resolver = $this->getResolver();
        $this->assertEquals('eee', $resolver->getIdByClassName(strtolower('Vendor2\OtherTestModule\SomeDifferentController')));
    }

    /**
     * Test helper
     *
     * @return OxidEsales\EshopCommunity\Core\Routing\ShopControllerMapProvider mock
     */
    private function getShopControllerMapProviderMock()
    {
        $map = array('aAa' => 'OxidEsales\EshopCommunity\Application\SomeController',
                     'bbb' => 'OxidEsales\EshopCommunity\Application\SomeOtherController',
                     'CCC' => 'OxidEsales\EshopCommunity\Application\SomeDifferentController');

        $mock = $this->getMock(\OxidEsales\Eshop\Core\Routing\ShopControllerMapProvider::class, ['getControllerMap'], [], '', false);
        $mock->expects($this->any())->method('getControllerMap')->will($this->returnValue($map));

        return $mock;
    }

    /**
     * Test helper
     *
     * @return OxidEsales\EshopCommunity\Core\Routing\ModuleControllerMapProvider mock
     */
    private function getModuleControllerMapProviderMock()
    {
        $map = array('cCc' => 'Vendor1\Testmodule\SomeController',
                     'DDD' => 'Vendor1\OtherTestModule\SomeOtherController',
                     'eee' => 'Vendor2\OtherTestModule\SomeDifferentController');

        $mock = $this->getMock(\OxidEsales\Eshop\Core\Routing\ModuleControllerMapProvider::class, ['getControllerMap'], [], '', false);
        $mock->expects($this->any())->method('getControllerMap')->will($this->returnValue($map));

        return $mock;
    }

    /**
     * Test helper to create resolver object.
     *
     * @return ControllerClassNameResolver resolver
     */
    private function getResolver()
    {
        $resolver = oxNew(\OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver::class, $this->getShopControllerMapProviderMock(), $this->getModuleControllerMapProviderMock());
        return $resolver;
    }
}

