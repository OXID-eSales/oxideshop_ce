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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Core;

use OxidEsales\EshopCommunity\Core\ClassNameProvider;
use OxidEsales\TestingLibrary\UnitTestCase;

class ClassNameProviderTest extends UnitTestCase
{
    public function providerReturnsClassNameFromClassAlias()
    {
        return array(
            array('oxdbmetadatahandler', '\OxidEsales\EshopEnterprise\Core\DbMetaDataHandler'),
            array('\OxidEsales\EshopEnterprise\Core\NonExisting', '\OxidEsales\EshopEnterprise\Core\NonExisting'),
        );
    }

    /**
     * @param string $classAlias
     * @param string $className
     *
     * @dataProvider providerReturnsClassNameFromClassAlias
     */
    public function testReturnsClassNameFromClassAlias($classAlias, $className)
    {
        $map = array(
            'oxdbmetadatahandler' => '\OxidEsales\EshopEnterprise\Core\DbMetaDataHandler',
            'oxmodulecache' => '\OxidEsales\EshopEnterprise\Core\Module\ModuleCache',
            'oxmoduleinstaller' => '\OxidEsales\EshopEnterprise\Core\Module\ModuleInstaller',
        );

        $utilsObject = new ClassNameProvider($map);

        $this->assertSame($className, $utilsObject->getClassName($classAlias));
    }

    public function providerReturnsClassNameAliasFromClassName()
    {
        return array(
            array('\OxidEsales\EshopEnterprise\Core\DbMetaDataHandler', 'oxdbmetadatahandler'),
            array('\OxidEsales\EshopEnterprise\Core\NonExisting', null),
            array('OxidEsales\EshopEnterprise\Core\DbMetaDataHandler', 'oxdbmetadatahandler'),
            array('OxidEsales\EshopEnterprise\Core\NonExisting', null),
        );
    }

    /**
     * @param string $className
     * @param string $classAliasName
     *
     * @dataProvider providerReturnsClassNameAliasFromClassName
     */
    public function testReturnsClassNameAliasFromClassName($className, $classAliasName)
    {
        $map = array(
            'oxdbmetadatahandler' => '\OxidEsales\EshopEnterprise\Core\DbMetaDataHandler',
            'oxmodulecache' => '\OxidEsales\EshopEnterprise\Core\Module\ModuleCache',
            'oxmoduleinstaller' => '\OxidEsales\EshopEnterprise\Core\Module\ModuleInstaller',
        );

        $utilsObject = new ClassNameProvider($map);

        $this->assertSame($classAliasName, $utilsObject->getClassAliasName($className));
    }
}
