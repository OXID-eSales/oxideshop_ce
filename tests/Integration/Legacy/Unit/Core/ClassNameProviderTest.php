<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\EshopCommunity\Core\BackwardsCompatibleClassNameProvider;
use OxidEsales\TestingLibrary\UnitTestCase;

class ClassNameProviderTest extends \PHPUnit\Framework\TestCase
{
    public function providerReturnsClassNameFromClassAlias(): \Iterator
    {
        yield ['oxdbmetadatahandler', \OxidEsales\EshopEnterprise\Core\DbMetaDataHandler::class];
        yield ['OxidEsales\EshopEnterprise\Core\NonExisting', 'OxidEsales\EshopEnterprise\Core\NonExisting'];
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
            'oxdbmetadatahandler' => \OxidEsales\EshopEnterprise\Core\DbMetaDataHandler::class,
        ];

        $utilsObject = new BackwardsCompatibleClassNameProvider($map);

        $this->assertSame($className, $utilsObject->getClassName($classAlias));
    }

    public function providerReturnsClassNameAliasFromClassName(): \Iterator
    {
        yield [\OxidEsales\EshopEnterprise\Core\DbMetaDataHandler::class, 'oxdbmetadatahandler'];
        yield ['OxidEsales\EshopEnterprise\Core\NonExisting', null];
        yield [\OxidEsales\EshopEnterprise\Core\DbMetaDataHandler::class, 'oxdbmetadatahandler'];
        yield ['OxidEsales\EshopEnterprise\Core\NonExisting', null];
    }

    /**
     * @param string $className
     * @param string $classAliasName
     *
     * @dataProvider providerReturnsClassNameAliasFromClassName
     */
    public function testReturnsClassNameAliasFromClassName($className, $classAliasName)
    {
        $map = ['oxdbmetadatahandler' => \OxidEsales\EshopEnterprise\Core\DbMetaDataHandler::class];

        $utilsObject = new BackwardsCompatibleClassNameProvider($map);

        $this->assertSame($classAliasName, $utilsObject->getClassAliasName($className));
    }
}
