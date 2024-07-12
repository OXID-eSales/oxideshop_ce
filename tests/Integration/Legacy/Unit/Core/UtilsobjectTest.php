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
    /**
     * Does nothing
     *
     * @param bool $a [optional]
     * @param bool $b [optional]
     * @param bool $c [optional]
     * @param bool $d [optional]
     * @param bool $e [optional]
     *
     * @return null
     */
    public function __construct($a = false, $b = false, $c = false, $d = false, $e = false)
    {
    }
}

class oxModuleUtilsObject extends \oxUtilsObject
{
    public function getActiveModuleChain($aClassChain)
    {
        return parent::getActiveModuleChain($aClassChain);
    }
}

class UtilsobjectTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    public function tearDown(): void
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
        $this->assertEquals($expectedClass, $utilsObject::class);
    }

    /**
     * Testing oxUtilsObject object creation.
     *
     * @return null
     */
    public function testGetObject()
    {
        $this->assertTrue(oxNew(\OxidEsales\EshopCommunity\Tests\Unit\Core\_oxutils_test::class) instanceof _oxutils_test);
        $this->assertTrue(oxNew(\OxidEsales\EshopCommunity\Tests\Unit\Core\_oxutils_test::class, 1) instanceof _oxutils_test);
        $this->assertTrue(oxNew(\OxidEsales\EshopCommunity\Tests\Unit\Core\_oxutils_test::class, 1, 2) instanceof _oxutils_test);
        $this->assertTrue(oxNew(\OxidEsales\EshopCommunity\Tests\Unit\Core\_oxutils_test::class, 1, 2, 3) instanceof _oxutils_test);
        $this->assertTrue(oxNew(\OxidEsales\EshopCommunity\Tests\Unit\Core\_oxutils_test::class, 1, 2, 3, 4) instanceof _oxutils_test);
    }

    public function testOxNewSettingParameters()
    {
        $oArticle = oxNew('oxarticle', ['aaa' => 'bbb']);

        $this->assertTrue($oArticle instanceof \OxidEsales\EshopCommunity\Application\Model\Article);
        $this->assertTrue(isset($oArticle->aaa));
        $this->assertEquals('bbb', $oArticle->aaa);
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
        $this->assertNotEquals($id1, $id2);
    }

    public function testResetInstanceCacheSingle()
    {
        $oTestInstance = modOxUtilsObject_oxUtilsObject::getInstance();
        $aInstanceCache = ["oxArticle" => oxNew('oxArticle'), "oxattribute" => new oxAttribute()];
        $oTestInstance->setClassNameCache($aInstanceCache);

        $oTestInstance->resetInstanceCache("oxArticle");

        $aGotInstanceCache = $oTestInstance->getClassNameCache();

        $this->assertEquals(1, count($aGotInstanceCache));
        $this->assertTrue($aGotInstanceCache["oxattribute"] instanceof \OxidEsales\EshopCommunity\Application\Model\Attribute);
    }

    public function testResetInstanceCacheAll()
    {
        $oTestInstance = modOxUtilsObject_oxUtilsObject::getInstance();
        $aInstanceCache = ["oxArticle" => oxNew('oxArticle'), "oxattribute" => new oxAttribute()];
        $oTestInstance->setClassNameCache($aInstanceCache);

        $oTestInstance->resetInstanceCache();

        $aGotInstanceCache = $oTestInstance->getClassNameCache();

        $this->assertEquals(0, count($aGotInstanceCache));
    }
}
