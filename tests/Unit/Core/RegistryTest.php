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

use oxConfig;
use OxidEsales\EshopCommunity\Core\Registry;
use oxLang;
use oxRegistry;
use oxSession;
use oxStr;
use oxUtils;
use stdClass;

/**
 * Test case for OxReg
 */
class RegistryTest extends \OxidTestCase
{

    /**
     * Test, that the method get creates the object of the correct current edition namespace.
     */
    public function testEditionSpecificObjectIsCreatedCorrect()
    {
        $utilsObject = \OxidEsales\Eshop\Core\Registry::get('oxUtilsObject');

        $edition = $this->getConfig()->getEdition();
        $expectedClass = 'OxidEsales\EshopCommunity\Core\UtilsObject';

        switch ($edition) {
            case 'CE':
                $expectedClass = 'OxidEsales\EshopCommunity\Core\UtilsObject';
                break;
            case 'PE':
                $expectedClass = 'OxidEsales\EshopProfessional\Core\UtilsObject';
                break;
            case 'EE':
                $expectedClass = 'OxidEsales\EshopEnterprise\Core\UtilsObject';
                break;
        }

        $this->assertEquals($expectedClass, get_class($utilsObject));
    }

    /**
     * test for OxReg::get()
     */
    public function testGet()
    {
        $oStr = Registry::get("oxstr");
        $this->assertTrue($oStr instanceof \OxidEsales\EshopCommunity\Core\Str);
    }

    /**
     * Tests that Registry is functioning in non case sensitive way
     */
    public function testSetGetCaseInsensitive()
    {
        $oStr = Registry::get("oxSTR");
        $oStr->test = "testValue";
        //differen case
        $oStr2 = Registry::get("OxStr");
        $this->assertEquals("testValue", $oStr2->test);
    }

    /**
     * tests OxReg::get() if the same instance is given every time
     */
    public function testGetSameInstance()
    {
        $oStr = Registry::get("oxstr");
        $oStr->test = "testValue";
        $oStr = Registry::get("oxstr");
        $this->assertEquals("testValue", $oStr->test);
    }

    /**
     * Tests OxReg::get() and OxReg::set()
     */
    public function testSetGetInstance()
    {
        $oTest = new stdClass();
        $oTest->testPublic = "testPublicVal";

        Registry::set("testCase", $oTest);
        $oTest2 = Registry::get("testCase");

        $this->assertEquals("testPublicVal", $oTest2->testPublic);
        Registry::set("testCase", null);
    }

    /**
     * Test for OxReg::getConfig()
     */
    public function testGetConfig()
    {
        $oSubj = $this->getConfig();
        $this->assertTrue($oSubj instanceof \OxidEsales\EshopCommunity\Core\Config);
    }

    public function testGetSession()
    {
        $oSubj = Registry::getSession();
        $this->assertTrue($oSubj instanceof \OxidEsales\EshopCommunity\Core\Session);
    }

    public function testGetLang()
    {
        $oSubj = Registry::getLang();
        $this->assertTrue($oSubj instanceof \OxidEsales\EshopCommunity\Core\Language);
    }

    public function testGetLUtils()
    {
        $oSubj = Registry::getUtils();
        $this->assertTrue($oSubj instanceof \OxidEsales\EshopCommunity\Core\Utils);
    }

    public function testGetKeys()
    {
        Registry::set("testKey", "testVal");
        $this->assertTrue(in_array(strtolower("testKey"), Registry::getKeys()));
        oxRegistry::set("testKey", null);
    }

    public function testUnset()
    {
        oxRegistry::set("testKey", "testVal");
        $this->assertTrue(in_array(strtolower("testKey"), Registry::getKeys()));
        oxRegistry::set("testKey", null);
        $this->assertFalse(in_array(strtolower("testKey"), Registry::getKeys()));
    }

    public function testInstanceExists()
    {
        oxRegistry::set("testKey", "testVal");
        $this->assertTrue(Registry::instanceExists('testKey'));
        oxRegistry::set("testKey", null);
        $this->assertFalse(Registry::instanceExists('testKey'));
    }

    /**
     * Test getter for ControllerClassNameProvider.
     */
    public function testGetControllerClassNameResolver()
    {
        $object = Registry::getControllerClassNameResolver();
        $this->assertTrue(is_a($object, '\OxidEsales\EshopCommunity\Core\Contract\ClassNameResolverInterface'));
        $this->assertTrue(is_a($object, '\OxidEsales\EshopCommunity\Core\Routing\ControllerClassNameResolver'));
    }

    /**
     * Test Registry::get() for UtilsObject.
     * NOTE: unit tests always get a brand new instance of UtilsObject.
     */
    public function testRegistryGetForBcUtilsObjectClassName()
    {
        $object = Registry::get('oxUtilsObject');
        $this->assertTrue(is_a($object, \OxidEsales\Eshop\Core\UtilsObject::class));
    }

    /**
     * Test Registry::get() for UtilsObject.
     * NOTE: unit tests always get a brand new instance of UtilsObject.
     */
    public function testRegistryGetForNamespaceUtilsObject()
    {
        $object = Registry::get(\OxidEsales\Eshop\Core\UtilsObject::class);
        $this->assertTrue(is_a($object, \OxidEsales\Eshop\Core\UtilsObject::class));
    }

    /**
     * Test Registry::getUtilsObject().
     */
    public function testRegistryGetUtilsObject()
    {
        $object = Registry::getUtilsObject();
        $this->assertTrue(is_a($object, \OxidEsales\Eshop\Core\UtilsObject::class));
    }

    /**
     * Verify that Registry::get can be called with virtualClassname as well as bc class name to get the same object.
     */
    public function testRegistryGetSupportsNamespaces()
    {
        $className = 'oxArticle';
        $virtualClassName = \OxidEsales\Eshop\Application\Model\Article::class;
        Registry::get($className);
        $this->assertTrue(Registry::instanceExists($className));
        $this->assertTrue(Registry::instanceExists($virtualClassName));
        $this->assertSame(Registry::get($className), Registry::get($virtualClassName));
    }

    /**
     * Verify that Registry::get can be called with virtualClassname as well as bc class name to get the same object.
     */
    public function testRegistryGetSupportsBcClasses()
    {
        $className = 'oxArticle';
        $virtualClassName = \OxidEsales\Eshop\Application\Model\Article::class;
        Registry::get($virtualClassName);
        $this->assertTrue(Registry::instanceExists($className));
        $this->assertTrue(Registry::instanceExists($virtualClassName));
        $this->assertSame(Registry::get($className), Registry::get($virtualClassName));
    }

    /**
     * Verify that Registry::set can be called with virtualClassname as well as bc class name to get the same object.
     */
    public function testRegistrySetSupportsNamespacesBc()
    {
        $bcClassName = 'oxArticle';
        $virtualClassName = \OxidEsales\Eshop\Application\Model\Article::class;
        $object = oxNew($bcClassName);
        Registry::set($bcClassName, $object);
        $this->assertTrue(Registry::instanceExists($bcClassName));
        $this->assertTrue(Registry::instanceExists($virtualClassName));
        $this->assertSame(Registry::get($bcClassName), Registry::get($virtualClassName));
    }

    /**
     * Verify that Registry::set can be called with virtualClassname as well as bc class name to get the same object.
     */
    public function testRegistrySetSupportsNamespaces()
    {
        $bcClassName = 'oxbasket';
        $virtualClassName = \OxidEsales\Eshop\Application\Model\Basket::class;
        $object = oxNew($virtualClassName);
        Registry::set($bcClassName, $object);
        $this->assertTrue(Registry::instanceExists($bcClassName));
        $this->assertTrue(Registry::instanceExists($virtualClassName));
        $this->assertSame(Registry::get($bcClassName), Registry::get($virtualClassName));
    }

    /**
     * Test Registry::getStorageKey.
     */
    public function testGetStorageKeyBcClass()
    {
        $bcClassName = 'oxArticle';
        $virtualClassName = \OxidEsales\Eshop\Application\Model\Article::class;
        $this->assertEquals(strtolower($virtualClassName), Registry::getStorageKey($bcClassName));
    }

    /**
     * Test Registry::getStorageKey.
     */
    public function testGetStorageKeyConfigFile()
    {
        $bcClassName = 'oxConfigFile';
        $virtualClassName = \OxidEsales\Eshop\Core\ConfigFile::class;
        $this->assertEquals(strtolower($virtualClassName), Registry::getStorageKey($bcClassName));
    }

    /**
     * Test Registry::getStorageKey.
     */
    public function testGetStorageKeyNamespaceClass()
    {
        $virtualClassName = \OxidEsales\Eshop\Application\Model\Article::class;
        $this->assertEquals(strtolower($virtualClassName), Registry::getStorageKey($virtualClassName));
    }

    /**
     * Have a look at the registry keys.
     */
    public function testRegistryKeys()
    {
        $storageKeys = Registry::getKeys();
        $this->assertTrue(in_array('oxidesales\eshop\core\utilsobject', $storageKeys));
        $this->assertTrue(in_array('oxidesales\eshop\core\configfile', $storageKeys));
        $this->assertTrue(in_array('oxidesales\eshop\core\configfile', $storageKeys));
    }

    /**
     * IMPORTANT: When you explicitly set/get edition classes, the edition namespace is
     *            used as storage key and not the virtual namespace class name.
     *            This is no problem as we will always use virtual class names but when edition classes are used
     *            be careful as long as the bc layer exists.
     */
    public function testRegistryAndEditionNamespace()
    {
        $className = \OxidEsales\EshopCommunity\Application\Model\Order::class;
        $virtualClassName = \OxidEsales\Eshop\Application\Model\Order::class;
        Registry::get($className);
        $this->assertTrue(Registry::instanceExists($className));
        //When you explicitly request an EDITION namespace object this is NOT stored under the virtual namespace key.
        $this->assertFalse(Registry::instanceExists($virtualClassName));
    }
}
