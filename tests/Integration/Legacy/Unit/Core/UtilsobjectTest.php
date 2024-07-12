<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use oemodulenameoxorder_parent;
use oxAttribute;
use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use OxidEsales\Eshop\Core\Registry;
use oxNewDummyUserModule2_parent;
use oxNewDummyUserModule_parent;

class modOxUtilsObject_oxUtilsObject extends \oxUtilsObject
{
    public function setClassNameCache($aValue)
    {
        parent::$_aInstanceCache = $aValue;
    }

    public function getClassNameCache()
    {
        return parent::$_aInstanceCache;
    }
}

/**
 * Test class for Unit_Core_oxutilsobjectTest::testGetObject() test case
 */
class _oxutils_test
{
}

class oxModuleUtilsObject extends \oxUtilsObject
{
    public function getActiveModuleChain($aClassChain)
    {
        return parent::getActiveModuleChain($aClassChain);
    }
}

class UtilsobjectTest extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->delete('testArticle');

        Registry::get("oxConfigFile")->setVar("sShopDir", $this->getConfigParam('sShopDir'));
        Registry::set('logger', getLogger());

        parent::tearDown();
    }

    /**
     * Test, that the method getInstance creates the object of the correct current edition namespace.
     */
    public function testEditionSpecificObjectIsCreatedCorrect()
    {
        $utilsObject = \OxidEsales\Eshop\Core\UtilsObject::getInstance();
        $expectedClass = \OxidEsales\Eshop\Core\UtilsObject::class;
        $this->assertSame($expectedClass, $utilsObject::class);
    }

    /**
     * Testing oxUtilsObject object creation.
     */
    public function testGetObject()
    {
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Tests\Unit\Core\_oxutils_test::class, oxNew(\OxidEsales\EshopCommunity\Tests\Unit\Core\_oxutils_test::class));
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Tests\Unit\Core\_oxutils_test::class, oxNew(\OxidEsales\EshopCommunity\Tests\Unit\Core\_oxutils_test::class, 1));
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Tests\Unit\Core\_oxutils_test::class, oxNew(\OxidEsales\EshopCommunity\Tests\Unit\Core\_oxutils_test::class, 1, 2));
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Tests\Unit\Core\_oxutils_test::class, oxNew(\OxidEsales\EshopCommunity\Tests\Unit\Core\_oxutils_test::class, 1, 2, 3));
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Tests\Unit\Core\_oxutils_test::class, oxNew(\OxidEsales\EshopCommunity\Tests\Unit\Core\_oxutils_test::class, 1, 2, 3, 4));
    }

    public function testOxNewSettingParameters()
    {
        $oArticle = oxNew('oxarticle', ['aaa' => 'bbb']);

        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Article::class, $oArticle);
        $this->assertTrue(property_exists($oArticle, 'aaa') && $oArticle->aaa !== null);
        $this->assertSame('bbb', $oArticle->aaa);
    }

    public function testOxNewCreationOfNonExistingClassContainsClassNameInExceptionMessage()
    {
        $this->expectException(SystemComponentException::class);
        $this->expectExceptionMessage('non_existing_class');

        oxNew("non_existing_class");
    }

    /**
     * No real test possible, but at least generated ids should be different
     */
    public function testGenerateUid()
    {
        $id1 = Registry::getUtilsObject()->generateUid();
        $id2 = Registry::getUtilsObject()->generateUid();
        $this->assertNotSame($id1, $id2);
    }

    public function testResetInstanceCacheSingle()
    {
        $oTestInstance = modOxUtilsObject_oxUtilsObject::getInstance();
        $aInstanceCache = ["oxArticle" => oxNew('oxArticle'), "oxattribute" => new oxAttribute()];
        $oTestInstance->setClassNameCache($aInstanceCache);

        $oTestInstance->resetInstanceCache("oxArticle");

        $aGotInstanceCache = $oTestInstance->getClassNameCache();

        $this->assertCount(1, $aGotInstanceCache);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Attribute::class, $aGotInstanceCache["oxattribute"]);
    }

    public function testResetInstanceCacheAll()
    {
        $oTestInstance = modOxUtilsObject_oxUtilsObject::getInstance();
        $aInstanceCache = ["oxArticle" => oxNew('oxArticle'), "oxattribute" => new oxAttribute()];
        $oTestInstance->setClassNameCache($aInstanceCache);

        $oTestInstance->resetInstanceCache();

        $aGotInstanceCache = $oTestInstance->getClassNameCache();

        $this->assertCount(0, $aGotInstanceCache);
    }
}
