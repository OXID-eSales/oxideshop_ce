<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\EshopCommunity\Core\BackwardsCompatibleClassNameProvider;
use OxidEsales\TestingLibrary\UnitTestCase;

class ClassNameProviderTest extends UnitTestCase
{
    public function providerReturnsClassNameFromClassAlias()
    {
        return array(
            array('oxdbmetadatahandler', 'OxidEsales\EshopEnterprise\Core\DbMetaDataHandler'),
            array('OxidEsales\EshopEnterprise\Core\NonExisting', 'OxidEsales\EshopEnterprise\Core\NonExisting'),
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
        $map = [
            'oxdbmetadatahandler' => 'OxidEsales\EshopEnterprise\Core\DbMetaDataHandler',
            'oxmodulecache' => 'OxidEsales\EshopEnterprise\Core\Module\ModuleCache',
            'oxmoduleinstaller' => 'OxidEsales\EshopEnterprise\Core\Module\ModuleInstaller',
        ];

        $utilsObject = new BackwardsCompatibleClassNameProvider($map);

        $this->assertSame($className, $utilsObject->getClassName($classAlias));
    }

    public function providerReturnsClassNameAliasFromClassName()
    {
        return array(
            array('OxidEsales\EshopEnterprise\Core\DbMetaDataHandler', 'oxdbmetadatahandler'),
            array('OxidEsales\EshopEnterprise\Core\NonExisting', null),
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
            'oxdbmetadatahandler' => 'OxidEsales\EshopEnterprise\Core\DbMetaDataHandler',
            'oxmodulecache' => 'OxidEsales\EshopEnterprise\Core\Module\ModuleCache',
            'oxmoduleinstaller' => 'OxidEsales\EshopEnterprise\Core\Module\ModuleInstaller'
        );

        $utilsObject = new BackwardsCompatibleClassNameProvider($map);

        $this->assertSame($classAliasName, $utilsObject->getClassAliasName($className));
    }
}
