<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use oxConfig;
use OxidEsales\Eshop\Core\Registry;
use oxLang;
use oxSession;
use oxStr;
use oxUtils;
use Psr\Log\LoggerInterface;
use stdClass;

/**
 * Test case for \OxidEsales\Eshop\Core\Registry
 */
class RegistryTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Test, that the method get creates the object of the correct current edition namespace.
     */
    public function testEditionSpecificObjectIsCreatedCorrect()
    {
        $utilsObject = \OxidEsales\Eshop\Core\Registry::getUtilsObject();
        $expectedClass = \OxidEsales\Eshop\Core\UtilsObject::class;
        $this->assertSame($expectedClass, $utilsObject::class);
    }

    /**
     * test for OxReg::get()
     */
    public function testGet()
    {
        $oStr = Registry::get("oxstr");
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\Str::class, $oStr);
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
        $this->assertSame("testValue", $oStr2->test);
    }

    /**
     * tests OxReg::get() if the same instance is given every time
     */
    public function testGetSameInstance()
    {
        $oStr = Registry::get("oxstr");
        $oStr->test = "testValue";
        $oStr = Registry::get("oxstr");
        $this->assertSame("testValue", $oStr->test);
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

        $this->assertSame("testPublicVal", $oTest2->testPublic);
        Registry::set("testCase", null);
    }

    /**
     * Test for OxReg::getConfig()
     */
    public function testGetConfig()
    {
        $oSubj = $this->getConfig();
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\Config::class, $oSubj);
    }

    public function testGetSession()
    {
        $oSubj = Registry::getSession();
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\Session::class, $oSubj);
    }

    public function testGetLang()
    {
        $oSubj = Registry::getLang();
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\Language::class, $oSubj);
    }

    public function testGetLUtils()
    {
        $oSubj = Registry::getUtils();
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\Utils::class, $oSubj);
    }

    public function testGetKeys()
    {
        Registry::set("testKey", "testVal");
        $this->assertContains("testkey", Registry::getKeys());
        \OxidEsales\Eshop\Core\Registry::set("testKey", null);
    }

    public function testUnset()
    {
        \OxidEsales\Eshop\Core\Registry::set("testKey", "testVal");
        $this->assertContains("testkey", Registry::getKeys());
        \OxidEsales\Eshop\Core\Registry::set("testKey", null);
        $this->assertNotContains("testKey", Registry::getKeys());
        $this->assertNotContains("testkey", Registry::getKeys());
    }

    public function testInstanceExists()
    {
        \OxidEsales\Eshop\Core\Registry::set("testKey", "testVal");
        $this->assertTrue(Registry::instanceExists('testKey'));
        \OxidEsales\Eshop\Core\Registry::set("testKey", null);
        $this->assertFalse(Registry::instanceExists('testKey'));
    }

    /**
     * Test getter for ControllerClassNameProvider.
     */
    public function testGetControllerClassNameResolver()
    {
        $object = Registry::getControllerClassNameResolver();
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\Contract\ClassNameResolverInterface::class, $object);
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver::class, $object);
    }

    /**
     * Test Registry::get() for UtilsObject.
     * NOTE: unit tests always get a brand new instance of UtilsObject.
     */
    public function testRegistryGetForBcUtilsObjectClassName()
    {
        $object = Registry::get('oxUtilsObject');
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\UtilsObject::class, $object);
    }

    /**
     * Test Registry::get() for UtilsObject.
     * NOTE: unit tests always get a brand new instance of UtilsObject.
     */
    public function testRegistryGetForNamespaceUtilsObject()
    {
        $object = Registry::get(\OxidEsales\Eshop\Core\UtilsObject::class);
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\UtilsObject::class, $object);
    }

    /**
     * Test Registry::getUtilsObject().
     */
    public function testRegistryGetUtilsObject()
    {
        $object = Registry::getUtilsObject();
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\UtilsObject::class, $object);
    }

    /**
     * Verify that Registry::get can be called with unified namespace classname as well as bc class name to get the same object.
     */
    public function testRegistryGetSupportsNamespaces()
    {
        $className = 'oxArticle';
        $unifiedNamespaceClassName = \OxidEsales\Eshop\Application\Model\Article::class;
        Registry::get($className);
        $this->assertTrue(Registry::instanceExists($className));
        $this->assertTrue(Registry::instanceExists($unifiedNamespaceClassName));
        $this->assertSame(Registry::get($className), Registry::get($unifiedNamespaceClassName));
    }

    /**
     * Verify that Registry::get can be called with unified namespace classname as well as bc class name to get the same object.
     */
    public function testRegistryGetSupportsBcClasses()
    {
        $className = 'oxArticle';
        $unifiedNamespaceClassName = \OxidEsales\Eshop\Application\Model\Article::class;
        Registry::get($unifiedNamespaceClassName);
        $this->assertTrue(Registry::instanceExists($className));
        $this->assertTrue(Registry::instanceExists($unifiedNamespaceClassName));
        $this->assertSame(Registry::get($className), Registry::get($unifiedNamespaceClassName));
    }

    /**
     * Verify that Registry::set can be called with unified namespace classname as well as bc class name to get the same object.
     */
    public function testRegistrySetSupportsNamespacesBc()
    {
        $bcClassName = 'oxArticle';
        $unifiedNamespaceClassName = \OxidEsales\Eshop\Application\Model\Article::class;
        $object = oxNew($bcClassName);
        Registry::set($bcClassName, $object);
        $this->assertTrue(Registry::instanceExists($bcClassName));
        $this->assertTrue(Registry::instanceExists($unifiedNamespaceClassName));
        $this->assertSame(Registry::get($bcClassName), Registry::get($unifiedNamespaceClassName));
    }

    /**
     * Verify that Registry::set can be called with unified namespace classname as well as bc class name to get the same object.
     */
    public function testRegistrySetSupportsNamespaces()
    {
        $bcClassName = 'oxbasket';
        $unifiedNamespaceClassName = \OxidEsales\Eshop\Application\Model\Basket::class;
        $object = oxNew($unifiedNamespaceClassName);
        Registry::set($bcClassName, $object);
        $this->assertTrue(Registry::instanceExists($bcClassName));
        $this->assertTrue(Registry::instanceExists($unifiedNamespaceClassName));
        $this->assertSame(Registry::get($bcClassName), Registry::get($unifiedNamespaceClassName));
    }

    /**
     * Test Registry::getStorageKey.
     */
    public function testGetStorageKeyBcClass()
    {
        $bcClassName = 'oxArticle';
        $unifiedNamespaceClassName = \OxidEsales\Eshop\Application\Model\Article::class;
        $this->assertSame($unifiedNamespaceClassName, Registry::getStorageKey($bcClassName));
    }

    /**
     * Test Registry::getStorageKey.
     */
    public function testGetStorageKeyConfigFile()
    {
        $bcClassName = 'oxConfigFile';
        $unifiedNamespaceClassName = \OxidEsales\Eshop\Core\ConfigFile::class;
        $this->assertSame($unifiedNamespaceClassName, Registry::getStorageKey($bcClassName));
    }

    /**
     * Test Registry::getStorageKey.
     */
    public function testGetStorageKeyNamespaceClass()
    {
        $unifiedNamespaceClassName = \OxidEsales\Eshop\Application\Model\Article::class;
        $this->assertSame($unifiedNamespaceClassName, Registry::getStorageKey($unifiedNamespaceClassName));
    }

    /**
     * Have a look at the registry keys.
     */
    public function testRegistryKeys()
    {
        $storageKeys = Registry::getKeys();
        $this->assertContains(\OxidEsales\Eshop\Core\UtilsObject::class, $storageKeys);
        $this->assertContains(\OxidEsales\Eshop\Core\ConfigFile::class, $storageKeys);
    }

    /**
     * IMPORTANT: When you explicitly set/get edition classes, the edition namespace is
     *            used as storage key and not the unified namespace classname.
     *            It is not intended to use edition namespaces!
     */
    public function testRegistryAndEditionNamespace()
    {
        $className = \OxidEsales\EshopCommunity\Application\Model\Order::class;
        $unifiedNamespaceClassName = \OxidEsales\Eshop\Application\Model\Order::class;
        Registry::get($className);
        $this->assertTrue(Registry::instanceExists($className));
        //When you explicitly request an EDITION namespace object this is NOT stored under the unified namespace key.
        $this->assertFalse(Registry::instanceExists($unifiedNamespaceClassName));
    }

    /**
     * Test Registry::getInputValidator().
     */
    public function testRegistryGetInputValidator()
    {
        $object = Registry::getInputValidator();
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\InputValidator::class, $object);
    }

    /**
     * Test Registry::getPictureHandler().
     */
    public function testRegistryGetPictureHandler()
    {
        $object = Registry::getPictureHandler();
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\PictureHandler::class, $object);
    }

    /**
     * Test Registry::getRequest().
     */
    public function testRegistryGetRequest()
    {
        $object = Registry::getRequest();
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\Request::class, $object);
    }

    /**
     * Test Registry::getSeoDecoder().
     */
    public function testRegistryGetSeoDecoder()
    {
        $object = Registry::getSeoDecoder();
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\SeoDecoder::class, $object);
    }

    /**
     * Test Registry::getSeoEncoder().
     */
    public function testRegistryGetSeoEncoder()
    {
        $object = Registry::getSeoEncoder();
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\SeoEncoder::class, $object);
    }

    /**
     * Test Registry::getUtilsCount().
     */
    public function testRegistryGetUtilsCount()
    {
        $object = Registry::getUtilsCount();
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\UtilsCount::class, $object);
    }

    /**
     * Test Registry::getUtilsDate().
     */
    public function testRegistryGetUtilsDate()
    {
        $object = Registry::getUtilsDate();
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\UtilsDate::class, $object);
    }

    /**
     * Test Registry::getUtilsFile().
     */
    public function testRegistryGetUtilsFile()
    {
        $object = Registry::getUtilsFile();
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\UtilsFile::class, $object);
    }

    /**
     * Test Registry::getUtilsPic().
     */
    public function testRegistryGetUtilsPic()
    {
        $object = Registry::getUtilsPic();
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\UtilsPic::class, $object);
    }

    /**
     * Test Registry::getUtilsServer().
     */
    public function testRegistryGetUtilsServer()
    {
        $object = Registry::getUtilsServer();
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\UtilsServer::class, $object);
    }

    /**
     * Test Registry::getUtilsString().
     */
    public function testRegistryGetUtilsString()
    {
        $object = Registry::getUtilsString();
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\UtilsString::class, $object);
    }

    /**
     * Test Registry::getUtilsUrl().
     */
    public function testRegistryGetUtilsUrl()
    {
        $object = Registry::getUtilsUrl();
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\UtilsUrl::class, $object);
    }

    /**
     * Test Registry::getUtilsView().
     */
    public function testRegistryGetUtilsView()
    {
        $object = Registry::getUtilsView();
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\UtilsView::class, $object);
    }

    /**
     * Test Registry::getUtilsXml().
     */
    public function testRegistryGetUtilsXml()
    {
        $object = Registry::getUtilsXml();
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\UtilsXml::class, $object);
    }

    /**
     * Test Registry dedicated getters vs. Registry::get() for BC classes.
     * Test belongs to BC layer.
     */
    public function testRegistryBCGet()
    {
        $this->assertSame(Registry::get('oxInputValidator'), Registry::getInputValidator());
        $this->assertSame(Registry::get('oxPictureHandler'), Registry::getPictureHandler());
        $this->assertSame(Registry::get('oxSeoDecoder'), Registry::getSeoDecoder());
        $this->assertSame(Registry::get('oxSeoEncoder'), Registry::getSeoEncoder());
        $this->assertSame(Registry::get('oxUtilsCount'), Registry::getUtilsCount());
        $this->assertSame(Registry::get('oxUtilsDate'), Registry::getUtilsDate());
        $this->assertSame(Registry::get('oxUtilsFile'), Registry::getUtilsFile());
        $this->assertSame(Registry::get('oxUtilsPic'), Registry::getUtilsPic());
        $this->assertSame(Registry::get('oxUtilsServer'), Registry::getUtilsServer());
        $this->assertSame(Registry::get('oxUtilsString'), Registry::getUtilsString());
        $this->assertSame(Registry::get('oxUtilsUrl'), Registry::getUtilsUrl());
        $this->assertSame(Registry::get('oxUtilsView'), Registry::getUtilsView());
        $this->assertSame(Registry::get('oxUtilsXml'), Registry::getUtilsXml());
        $this->assertSame(Registry::get('oxConfig'), Registry::getConfig());
        $this->assertSame(Registry::get('oxSession'), Registry::getSession());
        $this->assertSame(Registry::get('oxLang'), Registry::getLang());
        $this->assertSame(Registry::get('oxUtils'), Registry::getUtils());
    }

    /**
     * @dataProvider dataProviderTestRegistryGettersReturnProperInstances
     *
     * @param $method
     * @param $instance
     */
    public function testRegistryGettersReturnProperInstances($method, $instance)
    {
        $object = Registry::$method();

        $this->assertInstanceOf($instance, $object, 'Registry::' . $method . '() returns an object, which is an instance of ' . $instance);
    }

    /**
     * @return array
     */
    public function dataProviderTestRegistryGettersReturnProperInstances(): \Iterator
    {
        yield ['getInputValidator', \OxidEsales\Eshop\Core\InputValidator::class];
        yield ['getPictureHandler', \OxidEsales\Eshop\Core\PictureHandler::class];
        yield ['getRequest', \OxidEsales\Eshop\Core\Request::class];
        yield ['getSeoDecoder', \OxidEsales\Eshop\Core\SeoDecoder::class];
        yield ['getSeoEncoder', \OxidEsales\Eshop\Core\SeoEncoder::class];
        yield ['getUtilsCount', \OxidEsales\Eshop\Core\UtilsCount::class];
        yield ['getUtilsDate', \OxidEsales\Eshop\Core\UtilsDate::class];
        yield ['getUtilsFile', \OxidEsales\Eshop\Core\UtilsFile::class];
        yield ['getUtilsPic', \OxidEsales\Eshop\Core\UtilsPic::class];
        yield ['getUtilsServer', \OxidEsales\Eshop\Core\UtilsServer::class];
        yield ['getUtilsString', \OxidEsales\Eshop\Core\UtilsString::class];
        yield ['getUtilsUrl', \OxidEsales\Eshop\Core\UtilsUrl::class];
        yield ['getUtilsView', \OxidEsales\Eshop\Core\UtilsView::class];
        yield ['getUtilsXml', \OxidEsales\Eshop\Core\UtilsXml::class];
        yield ['getConfig', \OxidEsales\Eshop\Core\Config::class];
        yield ['getSession', \OxidEsales\Eshop\Core\Session::class];
        yield ['getLang', \OxidEsales\Eshop\Core\Language::class];
        yield ['getUtils', \OxidEsales\Eshop\Core\Utils::class];
        yield ['getUtilsObject', \OxidEsales\Eshop\Core\UtilsObject::class];
        yield ['getLogger', LoggerInterface::class];
    }

    /**
     * @dataProvider dataProviderTestRegistryGettersReturnIdenticalObjects
     *
     * @param $method
     */
    public function testRegistryGettersReturnIdenticalObjects($method)
    {
        $object_1 = Registry::$method();
        $object_2 = Registry::$method();

        $this->assertSame($object_2, $object_1, '2 consecutive calls to Registry::' . $method . '() will return identical objects');
    }

    /**
     * @return array
     */
    public function dataProviderTestRegistryGettersReturnIdenticalObjects(): \Iterator
    {
        yield ['getInputValidator'];
        yield ['getPictureHandler'];
        yield ['getRequest'];
        yield ['getSeoDecoder'];
        yield ['getSeoEncoder'];
        yield ['getUtilsCount'];
        yield ['getUtilsDate'];
        yield ['getUtilsFile'];
        yield ['getUtilsPic'];
        yield ['getUtilsServer'];
        yield ['getUtilsString'];
        yield ['getUtilsUrl'];
        yield ['getUtilsView'];
        yield ['getUtilsXml'];
        yield ['getConfig'];
        yield ['getSession'];
        yield ['getLang'];
        yield ['getUtils'];
        yield ['getLogger'];
    }
}
