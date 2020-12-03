<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use Exception;
use oxBase;
use oxBaseHelper;
use oxDb;
use oxField;
use OxidEsales\Eshop\Core\Registry;
use oxRegistry;
use oxUtils;
use stdClass;

require_once TEST_LIBRARY_HELPERS_PATH . 'oxBaseHelper.php';

/**
 * Test oxBase module
 */
class _oxBase extends oxBase
{
    /**
     * Constructor, with clean cache key.
     *
     * @return null
     */
    public function __construct()
    {
        parent::__construct();
        $this->_sCacheKey = null;
    }

    /**
     * Get any field value.
     *
     * @param string $sName Field name
     *
     * @return mixed
     */
    public function getProperty($sName)
    {
        return $this->$sName;
    }

    /**
     * Get any class field value.
     *
     * @param string $sName Field name
     *
     * @return mixed
     */
    public function getClassVar($sName)
    {
        return $this->$sName;
    }

    /**
     * Set any class field value.
     *
     * @param string $sName  Field name
     * @param string $sValue Field value
     *
     * @return mixed
     */
    public function setClassVar($sName, $sValue)
    {
        return $this->$sName = $sValue;
    }

    /**
     * Set field data value.
     *
     * @param string $sName  Field name
     * @param string $sValue Field value
     *
     * @return mixed
     */
    public function setFieldData($sName, $sValue, $dataType = \OxidEsales\Eshop\Core\Field::T_TEXT)
    {
        return parent::_setFieldData($sName, $sValue, $dataType);
    }

    /**
     * Force isDerived property.
     *
     * @param bool $blIsDerived is derived
     *
     * @return null
     */
    public function setIsDerived($blIsDerived)
    {
        $this->_blIsDerived = $blIsDerived;
    }

    /**
     * Force enable lazy loading.
     *
     * @return null
     */
    public function enableLazyLoading()
    {
        $this->_blUseLazyLoading = true;
    }

    /**
     * Force update.
     *
     * @return mixed
     */
    public function update()
    {
        return parent::_update();
    }

    /**
     * Force insert.
     *
     * @return mixed
     */
    public function insert()
    {
        return parent::_insert();
    }


    /**
     * Force getObjectViewName.
     *
     * @param string $sTable table name
     *
     * @return string
     */
    public function getObjectViewName($sTable, $shopId = null)
    {
        return parent::_getObjectViewName($sTable, $shopId);
    }

    /**
     * Force initDataStructure.
     *
     * @param bool $blForceFullStructure full structure
     *
     * @return string
     */
    public function initDataStructure($blForceFullStructure = false)
    {
        return parent::_initDataStructure($blForceFullStructure);
    }

    /**
     * Force _blUseAccessProp to true thus enabling access property usage.
     *
     * @return null
     */
    public function enableAccessPropUsage()
    {
        $this->_blUseAccessProp = true;
    }

    /**
     * Force getUpdateFieldValue.
     *
     * @param string $sFieldName fiels name
     * @param object $oField     field object
     *
     * @return mixed
     */
    public function getUpdateFieldValue($sFieldName, $oField)
    {
        return parent::_getUpdateFieldValue($sFieldName, $oField);
    }
}

/**
 * Test oxUtils module
 */
class oxUtilsNoCaching extends oxUtils
{
    /**
     * Force oxFileCache.
     *
     * @param bool   $blMode mode
     * @param string $sName  name
     * @param string $sInput input
     *
     * @return mixed
     */
    public function oxFileCache($blMode, $sName, $sInput = null)
    {
        return null;
    }
}

/**
 * Testing oxBase class.
 */
class BaseTest extends \OxidTestCase
{
    private static $count = 0;

    protected function setUp(): void
    {
        self::$count++;

        parent::setUp();

        $this->cleanUpTable('oxactions');
        $this->cleanUpTable('oxattribute');
        $this->cleanUpTable('oxarticles');
        $this->cleanUpTable('oxcategories');
        $this->cleanUpTable('oxdiscount');
        $this->cleanUpTable('oxorder');

        $this->getConfig();
        $this->getSession();
    }

    protected function tearDown(): void
    {
        $this->cleanUpTable('oxactions');
        $this->cleanUpTable('oxattribute');
        $this->cleanUpTable('oxarticles');
        $this->cleanUpTable('oxcategories');
        $this->cleanUpTable('oxdiscount');
        $this->cleanUpTable('oxorder');

        oxRemClassModule('modoxCacheAdminForBase');
        oxRemClassModule('modoxCacheForBase');

        parent::teardown();
    }

    public function getUpdateShopId()
    {
        $shopId = 1;
        return $shopId;
    }

    public function getArticlesViewName()
    {
        $articlesViewName = $this->getConfig()->getEdition() === 'EE' ? 'oxv_oxarticles_1' : 'oxv_oxarticles';
        return $articlesViewName;
    }

    /**
     * Test is loaded.
     *
     * @return null
     */
    public function testIsLoaded()
    {
        $oBase = $this->getMock(\OxidEsales\Eshop\Core\Model\BaseModel::class, array("_addField", "buildSelectString", "assignRecord", "getViewName"));
        $oBase->expects($this->once())->method('_addField')->with($this->equalTo('oxid'), $this->equalTo(0));
        $oBase->expects($this->once())->method('getViewName')->will($this->returnValue("testView"));
        $oBase->expects($this->once())->method('buildSelectString')->with($this->equalto(array("testView.oxid" => "xxx")))->will($this->returnValue("testSql"));
        $oBase->expects($this->once())->method('assignRecord')->with($this->equalTo("testSql"))->will($this->returnValue(true));

        $this->assertFalse($oBase->isLoaded());

        $oBase->load("xxx");
        $this->assertTrue($oBase->isLoaded());
    }

    /**
     * Test is derived when both shop ids are null.
     *
     * @return null
     */
    public function testIsDerivedBothShopIdsAreNull()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getShopId'));
        $oConfig->expects($this->any())->method('getShopId')->will($this->returnValue(null));

        $oBase = $this->getMock(\OxidEsales\Eshop\Core\Model\BaseModel::class, array('getConfig', 'getShopId'), array(), '', false);
        Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oBase->expects($this->any())->method('getShopId')->will($this->returnValue(null));

        $this->assertNull($oBase->isDerived());
    }

    /**
     * Test is derived when shop ids match.
     *
     * @return null
     */
    public function testIsDerivedShopIdsMatch()
    {
        $expected = $this->getConfig()->getEdition() === 'EE' ? false : null;

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getShopId'));
        $oConfig->expects($this->any())->method('getShopId')->will($this->returnValue('xxx'));

        $oBase = $this->getMock(\OxidEsales\Eshop\Core\Model\BaseModel::class, array('getConfig', 'getShopId'), array(), '', false);
        Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oBase->expects($this->any())->method('getShopId')->will($this->returnValue('xxx'));

        $this->assertSame($expected, $oBase->isDerived());
    }

    /**
     * Test is derived when shop ids does not match.
     *
     * @return null
     */
    public function testIsDerivedShopIdsDeosNotMatch()
    {
        $expected = $this->getConfig()->getEdition() === 'EE' ? true : null;

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getShopId'));
        $oConfig->expects($this->any())->method('getShopId')->will($this->returnValue('xxx'));

        $oBase = $this->getMock(\OxidEsales\Eshop\Core\Model\BaseModel::class, array('getConfig', 'getShopId'), array(), '', false);
        Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oBase->expects($this->any())->method('getShopId')->will($this->returnValue('yyy'));

        $this->assertSame($expected, $oBase->isDerived());
    }

    /**
     * Test allow derived update.
     *
     * @return null
     */
    public function testAllowDerivedUpdate()
    {
        $oBase = $this->getMock(\OxidEsales\Eshop\Core\Model\BaseModel::class, array('isDerived'));
        $oBase->expects($this->once())->method('isDerived')->will($this->returnValue(false));
        $this->assertTrue($oBase->allowDerivedUpdate());
    }

    /**
     * Test allow derived delete.
     *
     * @return null
     */
    public function testAllowDerivedDelete()
    {
        $oBase = $this->getMock(\OxidEsales\Eshop\Core\Model\BaseModel::class, array('isDerived'));
        $oBase->expects($this->once())->method('isDerived')->will($this->returnValue(false));
        $this->assertTrue($oBase->allowDerivedDelete());
    }

    /**
     * Test converting fields.
     *
     * @return null
     */
    public function testConvertingFields()
    {
        $oBase = $this->getMock(\OxidEsales\Eshop\Core\Model\BaseModel::class, array('_initDataStructure', 'isAdmin', 'exists', 'isDerived', '_update', '_insert', 'onChange', 'getId'));
        $oBase->expects($this->once())->method('_initDataStructure');
        $oBase->expects($this->once())->method('isAdmin')->will($this->returnValue(true));
        $oBase->expects($this->once())->method('exists')->will($this->returnValue(false));
        $oBase->expects($this->never())->method('isDerived');
        $oBase->expects($this->never())->method('_update');
        $oBase->expects($this->once())->method('_insert')->will($this->returnValue(true));
        $oBase->expects($this->once())->method('onChange')->with($this->equalTo(ACTION_INSERT));
        $oBase->expects($this->once())->method('getId')->will($this->returnValue('ggg'));

        // initing ..
        $oBase->init('oxsomething');

        // adding some test fields ..
        $oBase->UNITaddField('oxfield1', 1, 'datetime');
        $oBase->UNITaddField('oxfield2', 1, 'timestamp');
        $oBase->UNITaddField('oxfield3', 1, 'date');

        $this->assertEquals('ggg', $oBase->save());

        $this->assertEquals("0000-00-00 00:00:00", $oBase->oxsomething__oxfield1->value);
        $this->assertEquals("00000000000000", $oBase->oxsomething__oxfield2->value);
        $this->assertEquals("0000-00-00", $oBase->oxsomething__oxfield3->value);
    }

    /**
     * Constructor test
     *
     * @return null
     */
    public function testOxBase()
    {
        $oBase = oxNew('oxBase');

        $this->assertEquals($this->getConfig()->getShopId(), $oBase->getShopId());
        $this->assertNotEquals(0, $this->getConfig()->getShopID());
    }

    /**
     * Testing object cloning (actually properties copying) function.
     *
     * @return null
     */
    public function testOxClone()
    {
        $o = oxNew('oxBase');
        $u = oxNew('oxBase');
        $u->aa = 'a';
        $handle = $u->ab = new stdClass();
        $handle->z = 'z';
        $o->oxClone($u);
        $handle->z = 'x';
        $this->assertEquals('a', $o->aa);
        $this->assertEquals('z', $o->ab->z);
    }

    /**
     * Testing blIsDerived magic getter.
     *
     * @return null
     */
    public function testMagicGetIsDerived()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_blIsDerived", true);

        $this->assertFalse($oBase->isPropertyLoaded('blIsDerived'));
        $this->assertTrue($oBase->blIsDerived);
    }

    /**
     * Testing blIsDerived magic getter.
     *
     * @return null
     */
    public function testMagicGetOXID()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_sOXID", 'test id');

        $this->assertFalse($oBase->isPropertyLoaded('sOXID'));
        $this->assertEquals('test id', $oBase->sOXID);
    }

    /**
     * Testing isReadOnly magic getter.
     *
     * @return null
     */
    public function testIsReadOnly()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_blReadOnly", true);
        $this->assertFalse(isset($oBase->blReadOnly));
        $this->assertTrue($oBase->blReadOnly);
    }

    /**
     * Testing simple lazy loading .
     *
     * @return null
     */
    public function testMagicGetLazyLoading()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_blUseLazyLoading", true);
        $oBase->init("oxarticles");
        $oBase->setId("2000");
        $sTitle = $oBase->oxarticles__oxtitle->value;
        $this->assertEquals("Wanduhr ROBOT", $sTitle);
    }

    /**
     * Simple lazy loading test, if loaded object do not exists.
     *
     * @return null
     */
    public function testMagicGetLazyLoadingNoObjectFound()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_blUseLazyLoading", true);
        $oBase->init("oxarticles");
        $oBase->setId("test");
        $this->assertNull($oBase->oxarticles__oxtitle->value);
    }

    /**
     * Simple lazy loading test, if loaded non existing field with debug.
     *
     * @return null
     */
    public function testMagicGetLazyLoadingNonExistingFieldWithDebug()
    {
        $oBase = new _oxBase();
        Registry::getConfig()->setConfigParam('iDebug', -1);
        $oBase->setClassVar("_blUseLazyLoading", true);
        $oBase->init("oxarticles");
        $oBase->setId("2000");
        $sNonExistentTitle = $oBase->oxarticles__oxtitle_nonexistent;
        $this->assertNull($sNonExistentTitle);
    }

    /**
     * Simple lazy loading test, if loaded non existing field without debug.
     *
     * @return null
     */
    public function testMagicGetLazyLoadingNonExistingFieldWithoutDebug()
    {
        $oBase = new _oxBase();
        Registry::getConfig()->setConfigParam('iDebug', 0);
        $oBase->setClassVar("_blUseLazyLoading", true);
        $oBase->init("oxarticles");
        $oBase->setId("2000");
        $sNonExistentTitle = $oBase->oxarticles__oxtitle_nonexistent;
        $this->assertNull($sNonExistentTitle);
    }

    /**
     * Simple lazy loading id is always set.
     *
     * @return null
     */
    public function testLazyLoadingIdIsAlwaysSet()
    {
        //cleaning cache
        oxRegistry::getUtils()->toFileCache('fieldnames_oxarticles_lazyloadingtest', null);

        $oBase = new _oxBase();
        $oBase->setClassVar("_sCoreTable", "oxarticles");
        $oBase->setClassVar("_blUseLazyLoading", true);
        $oBase->modifyCacheKey("lazyloadingtest", true);
        $oBase->init();
        $oBase->load(2000);

        $this->assertFalse($oBase->isPropertyLoaded('oxid'));
        $this->assertEquals("2000", $oBase->getId());
        $this->assertEquals("2000", $oBase->oxarticles__oxid->value);
    }

    /**
     * Tests whether lazy loading really works
     *
     * @return null
     */
    public function testLazyLoading()
    {
        //cleaning cache
        oxRegistry::getUtils()->toFileCache('fieldnames_oxarticles_lazyloadingtest', null);

        $oBase = new _oxBase();
        $oBase->setClassVar("_sCoreTable", "oxarticles");
        $oBase->setClassVar("_blUseLazyLoading", true);
        $oBase->modifyCacheKey("lazyloadingtest", true);
        $oBase->init();
        $oBase->load(2000);

        $this->assertFalse($oBase->isPropertyLoaded('oxarticles__oxtitle'));
        $this->assertFalse($oBase->isPropertyLoaded('oxarticles__oxshortdesc'));

        //making sure 2 fields are used
        $sVal = $oBase->oxarticles__oxtitle->value;
        $sVal = $oBase->oxarticles__oxshortdesc->value;

        $this->assertTrue($oBase->isPropertyLoaded('oxarticles__oxtitle'));
        $this->assertTrue($oBase->isPropertyLoaded('oxarticles__oxshortdesc'));

        $oBase = new _oxBase();
        $oBase->setClassVar("_sCoreTable", "oxarticles");
        $oBase->setClassVar("_blUseLazyLoading", true);
        $oBase->modifyCacheKey("lazyloadingtest", true);
        $oBase->init();
        $oBase->load(2000);

        $this->assertFalse($oBase->isPropertyLoaded('oxarticles__oxtitle'));
        $this->assertFalse($oBase->isPropertyLoaded('oxarticles__oxshortdesc'));
    }

    /**
     * Tests whether lazy loading really works from cache
     *
     * @return null
     */
    public function testLazyLoadingFromCache()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_sCoreTable", "oxarticles");
        $oBase->setClassVar("_blUseLazyLoading", true);
        $oBase->modifyCacheKey("lazyloadingtest1", true);
        $oBase->init();
        $oBase->load(2000);

        $this->assertFalse($oBase->isPropertyLoaded('oxarticles__oxtitle'));
        $this->assertFalse($oBase->isPropertyLoaded('oxarticles__oxshortdesc'));

        //making sure 2 fields are used
        $sVal = $oBase->oxarticles__oxtitle->value;
        $sVal = $oBase->oxarticles__oxshortdesc->value;

        $this->assertTrue($oBase->isPropertyLoaded('oxarticles__oxtitle'));
        $this->assertTrue($oBase->isPropertyLoaded('oxarticles__oxshortdesc'));

        oxBaseHelper::cleanup();
        $oBase = new _oxBase();
        $oBase->setClassVar("_sCoreTable", "oxarticles");
        $oBase->setClassVar("_blUseLazyLoading", true);
        $oBase->modifyCacheKey("lazyloadingtest1", true);
        $oBase->init();
        $oBase->load(2000);

        $this->assertTrue($oBase->isPropertyLoaded('oxarticles__oxtitle'));
        $this->assertTrue($oBase->isPropertyLoaded('oxarticles__oxshortdesc'));
    }

    /**
     * Testing init without table name.
     *
     * @return null
     */
    public function testInitWithoutTableName()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_sCoreTable", "oxarticles");
        $oBase->init();
        $this->assertEquals("oxarticles", $oBase->getClassVar("_sCoreTable"));
    }

    /**
     * Testing init.
     *
     * @return null
     */
    public function testInit()
    {
        $expected = $this->getArticlesViewName();
        $oBase = new _oxBase();
        $oBase->init("oxarticles");
        $this->assertEquals("oxarticles", $oBase->getCoreTableName());
        $this->assertEquals($expected, $oBase->getViewName());
    }

    /**
     * Testing init data partial structure.
     *
     * @return null
     */
    public function testInitDataPartialStructure()
    {
        $oBase = new _oxBase();
        $oBase->modifyCacheKey(null, true);
        $oBase->setClassVar("_sCoreTable", "oxarticles");
        $oBase->setClassVar("_blUseLazyLoading", "true");
        $oBase->initDataStructure();

        $this->assertFalse($oBase->isPropertyLoaded('oxactions__oxtitle'));

        $aFieldNames = $oBase->getClassVar("_aFieldNames");
        $this->assertFalse(isset($aFieldNames['oxtitle']));
        ;
    }

    /**
     * Testing init data full structure.
     *
     * @return null
     */
    public function testInitDataFullStructure()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_sCoreTable", "oxarticles");
        $oBase->setClassVar("_blUseLazyLoading", "true");
        $oBase->initDataStructure(true);
        $this->assertTrue(isset($oBase->oxarticles__oxtitle));
        $aFieldNames = $oBase->getClassVar("_aFieldNames");
        $this->assertTrue(isset($aFieldNames['oxtitle']));
    }

    /**
     * Test get field longname.
     *
     * @return null
     */
    public function testGetFieldLongName()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_sCoreTable", "oxtesttable");
        $this->assertEquals("oxtesttable__oxtestfield", $oBase->UNITgetFieldLongName('oxtestfield'));
    }

    /**
     * Test get class name.
     *
     * @return null
     */
    public function testGetClassName()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_sClassName", "test class");
        $this->assertEquals("test class", $oBase->getClassName());
    }

    public function testGetUpdateFields()
    {
        $base = new _oxBase();
        $base->init("oxactions");
        $base->oxactions__oxid = new oxField("test1", oxField::T_RAW);
        $base->oxactions__oxtitle = new oxField("title1", oxField::T_RAW);

        $expectedOxid = "oxid = 'test1'";
        $this->assertStringStartsWith($expectedOxid, $base->UNITgetUpdateFields());
    }

    public function testGetUpdateFieldsWithUseSkipSaveFieldsOff()
    {
        $base = new _oxBase();
        $base->init('oxactions');
        $base->oxactions__oxid = new oxField('test1', oxField::T_RAW);
        $base->oxactions__oxtitle = new oxField('title1', oxField::T_RAW);
        $base->setClassVar('_aSkipSaveFields', ['oxtitle']);

        $shopId = $this->getUpdateShopId();

        $expectedOxid = "oxid = 'test1',oxshopid = '{$shopId}',oxtype = '',oxtitle = 'title1'";
        $this->assertStringStartsWith($expectedOxid, $base->UNITgetUpdateFields(false));
    }

    public function testGetUpdateFieldsWithUseSkipSaveFieldsOn()
    {
        $base = new _oxBase();
        $base->init('oxactions');
        $base->oxactions__oxid = new oxField('test1', oxField::T_RAW);
        $base->oxactions__oxtitle = new oxField('title1', oxField::T_RAW);

        $shopId = $this->getUpdateShopId();

        $base->setClassVar('_aSkipSaveFields', ['oxtitle']);
        $expectedOxid = "oxid = 'test1',oxshopid = '{$shopId}',oxtype = '',oxtitle_1 = ''";
        $this->assertStringStartsWith($expectedOxid, $base->UNITgetUpdateFields());
    }

    /**
     * Test get core table name.
     *
     * @return null
     */
    public function testGetCoreTableName()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_sCoreTable", "table1");
        $this->assertEquals("table1", $oBase->getCoreTableName());
    }

    /**
     * Test get seo id.
     *
     * @return null
     */
    public function testSetId()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $oBase->setId("test id");
        $this->assertEquals("test id", $oBase->getClassVar("_sOXID"));
        $this->assertEquals("test id", $oBase->oxactions__oxid->value);
    }

    /**
     * Test get id.
     *
     * @return null
     */
    public function testGetId()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_sOXID", "testId");
        $this->assertEquals("testId", $oBase->getId());
    }

    /**
     * Test set shop id with numeric value.
     *
     * @return null
     */
    public function testSetShopIdNumeric()
    {
        $oBase = new _oxBase();
        $oBase->setShopId(5);
        $this->assertEquals(5, $oBase->getClassVar("_iShopId"));
    }

    /**
     * Test get shop id.
     *
     * @return null
     */
    public function testGetShopId()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_iShopId", "testShopId");
        $this->assertEquals("testShopId", $oBase->getShopId());
    }

    /**
     * Test get view name.
     *
     * @return null
     */
    public function testGetViewName()
    {
        $oBase = oxNew('oxBase');
        $oBase->init('oxarticles');
        $articlesViewNameExpected = $this->getArticlesViewName();
        $this->assertEquals($articlesViewNameExpected, $oBase->getViewName());
    }

    /**
     * Test get view name as core table.
     *
     * @return null
     */
    public function testGetViewNameSameAsCore()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions"); // multilanguage name
        $this->assertEquals("oxv_oxactions", $oBase->getViewName());
        $oBase = new _oxBase();
        $oBase->init("oxconfig"); // non-multilanguage name
        $this->assertEquals("oxconfig", $oBase->getViewName());
    }

    /**
     * Test disable lazy loading.
     *
     * @return null
     */
    public function testDisableLazyLoading()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_blUseLazyLoading", true);
        $oBase->init("oxactions");

        $this->assertFalse($oBase->isPropertyLoaded('oxactions__oxtitle'));

        $oBase->DisableLazyLoading();
        $this->assertFalse($oBase->getClassVar("_blUseLazyLoading"));
        $this->assertTrue($oBase->isPropertyLoaded('oxactions__oxtitle'));
    }

    /**
     * Test modify cache key.
     *
     * @return null
     */
    public function testModifyCacheKey()
    {
        $oBase = new _oxBase();
        $oBase->modifyCacheKey('testCache1', true);
        $this->assertEquals('testCache1', $oBase->getClassVar('_sCacheKey'));
        $oBase->modifyCacheKey('testCache2', true);
        $this->assertEquals('testCache2', $oBase->getClassVar('_sCacheKey'));
        $oBase->modifyCacheKey('testCache3', false);
        $this->assertEquals('testCache2testCache3', $oBase->getClassVar('_sCacheKey'));
    }

    /**
     * Test set is multilang.
     *
     * @return null
     */
    public function testIsMultilang()
    {
        $oBase = new _oxBase();
        $this->assertFalse($oBase->isMultilang());
    }

    /**
     * Test set is derived.
     *
     * @return null
     */
    public function testIsDerived()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_blIsDerived", true);
        $this->assertTrue($oBase->isDerived());
        $oBase->setClassVar("_blIsDerived", false);
        $this->assertFalse($oBase->isDerived());
    }

    /**
     * Test assign.
     */
    public function testAssign()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $select = "select * from oxactions where oxid = 'oxtopstart'";
        $oDB = oxDb::getDB(oxDB::FETCH_MODE_ASSOC);
        $rs = $oDB->select($select);
        $oBase->assign($rs->fields);
        $this->assertEquals("oxtopstart", $oBase->getId());
    }

    /**
     * Test assign without shop id.
     *
     * @return null
     */
    public function testAssignWithoutShopId()
    {
        $oDB = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $oBase->oxactions__oxid = new oxField("oxtopstart", oxField::T_RAW);
        $select = "select * from oxactions where oxid = 'oxtopstart'";
        $rs = $oDB->select($select);
        $oBase->assign($rs->fields);
        $this->assertEquals($oBase->getId(), "oxtopstart");
    }

    /**
     * Test assign with empty data.
     *
     * @return null
     */
    public function testAssignWithEmptyData()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $oBase->oxactions__oxid = new oxField("oxtopstart", oxField::T_RAW);
        $oBase->assign("aaa");
        $this->assertEquals($oBase->getId(), null);
    }

    /**
     * Test assign record.
     *
     * @return null
     */
    public function testAssignRecord()
    {
        $myDB = oxDb::getDb();
        $sInsert = "Insert into oxactions (`oxid`, `oxtitle`) values ('_test', 'testTitle')";
        $myDB->Execute($sInsert);

        $oBase = new _oxBase();
        $oBase->init('oxactions');
        $sQ = "select * from oxactions where oxid = '_test'";
        $oBase->assignRecord($sQ);

        $this->assertEquals('testTitle', $oBase->oxactions__oxtitle->value);
    }

    /**
     * Test set field data.
     *
     * @return null
     */
    public function testSetFieldData()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $rs = array("oxid" => "oxtopstart", "oxtitle" => "Startseite unten");
        foreach ($rs as $name => $value) {
            $oBase->setFieldData($name, $value);
        }
        $this->assertEquals($oBase->oxactions__oxid->value, "oxtopstart");
        $this->assertEquals($oBase->oxactions__oxtitle->value, "Startseite unten");
    }

    /**
     * Test set field data long name.
     *
     * @return null
     */
    public function testSetFieldDataLongName()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $rs = array("oxid" => "oxtopstart", "oxactions__oxtitle" => "Startseite unten");
        foreach ($rs as $name => $value) {
            $oBase->setFieldData($name, $value);
        }
        $this->assertEquals($oBase->oxactions__oxid->value, "oxtopstart");
        $this->assertEquals($oBase->oxactions__oxtitle->value, "Startseite unten");
    }

    /**
     * Test set field data for non existing field with lazy loading enabled.
     *
     * @return null
     */
    public function testSetFieldDataNonExistingLazyLoading()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_blUseLazyLoading", true);
        $oBase->init("oxactions");
        $rs = array("oxid" => "oxtopstart", "oxactions__oxtestval" => "Startseite unten", "oxtestval2" => "TestVal2");
        foreach ($rs as $name => $value) {
            $oBase->setFieldData($name, $value);
        }
        //standard field
        $this->assertEquals($oBase->oxactions__oxid->value, "oxtopstart");
        //was not set before
        //T2008-09-03
        //$this->assertFalse(isset($oBase->oxactions__oxtestval));
        //$this->assertFalse(isset($oBase->oxactions__oxtestval2));
        $this->assertEquals($oBase->oxactions__oxtestval->value, "Startseite unten");
        $this->assertEquals($oBase->oxactions__oxtestval2->value, "TestVal2");
    }

    /**
     * Test set field data for non existing field with lazy loading disabled.
     *
     * @return null
     */
    public function testSetFieldDataNonExistingNonLazyLoading()
    {
        $oBase = $this->getProxyClass("oxbase");
        $oBase->setNonPublicVar("_blUseLazyLoading", false);
        $oBase->setNonPublicVar("_sCoreTable", "oxactions");
        $aFieldNames = $oBase->getNonPublicVar('_aFieldNames');
        $this->assertFalse(isset($aFieldNames['oxtitle']));
        $rs = array("oxid" => "oxtopstart", "oxtitle" => "Startseite unten");
        foreach ($rs as $name => $value) {
            $oBase->UNITsetFieldData($name, $value);
        }
        //standard field
        $this->assertEquals($oBase->oxactions__oxid->value, "oxtopstart");
        //was not set before
        $this->assertTrue(isset($oBase->oxactions__oxtitle));
        $aFieldNames = $oBase->getNonPublicVar('_aFieldNames');
        $this->assertEquals(0, $aFieldNames['oxtitle']);
    }

    /**
     * Test get field data.
     *
     * @return null
     */
    public function testGetFieldData()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $oBase->oxactions__oxid = new oxField("oxtopstart", oxField::T_RAW);
        $this->assertEquals("oxtopstart", $oBase->getFieldData("oxid"));
    }

    /**
     * Test get empty field data.
     *
     * @return null
     */
    public function testGetFieldDataEmpty()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $this->assertNull($oBase->getFieldData("oxid"));
    }

    /**
     * Test load.
     *
     * @return null
     */
    public function testLoad()
    {
        $oBase = new _oxBase();
        $oBase->modifyCacheKey(null, true);
        $oBase->init("oxactions");
        $oBase->load("oxtopstart");
        $this->assertEquals($oBase->getId(), "oxtopstart");
        $this->assertTrue(isset($oBase->oxactions__oxtitle));
    }

    /**
     * Test load in language 1.
     *
     * @return null
     */
    public function testLoadInLang0()
    {
        $oObj = new _oxBase();
        $oObj->init("oxarticles");
        $oObj->modifyCacheKey(null, true);

        if ($this->getConfig()->getEdition() === 'EE') {
            $expectedTranslation = 'Champagne Pliers &amp; Bottle Opener PROFI';
        } else {
            $expectedTranslation = 'Champagne Pliers &amp; Bottle Opener';
        }

        $oObj->load(2080);
        $this->assertEquals(2080, $oObj->getId());
        //load in 2 languages anyway
        $this->assertEquals("Barzange PROFI", $oObj->oxarticles__oxtitle->value);
        $this->assertEquals($expectedTranslation, $oObj->oxarticles__oxtitle_1->value);
    }

    /**
     * Test load with lazy loading 1.
     *
     * @return null
     */
    public function testLoadLazy1()
    {
        $oBase = new _oxBase();
        $oBase->enableLazyLoading();
        $oBase->init("oxactions");
        $oBase->load("oxtopstart");

        $this->assertEquals("oxtopstart", $oBase->getId());

        $this->assertFalse($oBase->isPropertyLoaded('oxactions__oxtitle'));
    }

    /**
     * Test load with lazy loading case 2.
     *
     * @return null
     */
    public function testLoadLazy2()
    {
        $oBase = new _oxBase();
        $oBase->enableLazyLoading();
        $oBase->init("oxarticles");
        $oBase->load("2000");

        $this->assertEquals("2000", $oBase->getId());
        $this->assertEquals("Wanduhr ROBOT", $oBase->oxarticles__oxtitle->value);
        $this->assertFalse($oBase->isPropertyLoaded('oxactions__oxtitle'));
    }

    /**
     * Test load non existin field with lazy loading.
     *
     * @return null
     */
    public function testLoadLazyNonExistingField()
    {
        $oBase = new _oxBase();
        $oBase->enableLazyLoading();
        $oBase->init("oxarticles");
        $oBase->load("2000");

        $this->assertEquals("2000", $oBase->getId());
        $sFakeValue = $oBase->oxarticles__oxnonexistingfield->value;
        $aFieldList = $oBase->getClassVar("_aFieldNames");
        $this->assertFalse(isset($aFieldList["oxnonexistingfield"]));
    }

    /**
     * Test build select string.
     *
     * @return null
     */
    public function testBuildSelectString()
    {
        $oBase = oxNew('oxBase');
        $oBase->init("oxactions");
        $sView = getViewName("oxactions", -1);
        $sSelect = $oBase->buildSelectString(array("$sView.oxid" => "oxtopstart"));
        $sSelect = str_replace("  ", " ", $sSelect);

        $this->assertEquals("select `$sView`.`oxid`, `$sView`.`oxshopid`, `$sView`.`oxtype`, `$sView`.`oxtitle`, `$sView`.`oxtitle_1`, `$sView`.`oxtitle_2`, `$sView`.`oxtitle_3`, `$sView`.`oxlongdesc`, `$sView`.`oxlongdesc_1`, `$sView`.`oxlongdesc_2`, `$sView`.`oxlongdesc_3`, `$sView`.`oxactive`, `$sView`.`oxactivefrom`, `$sView`.`oxactiveto`, `$sView`.`oxpic`, `$sView`.`oxpic_1`, `$sView`.`oxpic_2`, `$sView`.`oxpic_3`, `$sView`.`oxlink`, `$sView`.`oxlink_1`, `$sView`.`oxlink_2`, `$sView`.`oxlink_3`, `$sView`.`oxsort`, `$sView`.`oxtimestamp` from $sView where 1 and $sView.oxid = 'oxtopstart'", $sSelect);
    }

    /**
     * Test build select string without shop id.
     *
     * @return null
     */
    public function testBuildSelectStringWithoutShopId()
    {
        if ($this->getConfig()->getEdition() === 'EE') {
            $this->markTestSkipped("Test for Community and Professional editions only");
        }

        $oBase = oxNew('oxBase');
        $oBase->init("oxattribute");
        $sSelect = $oBase->buildSelectString(array("oxid" => "111"));
        $sSelect = str_replace("  ", " ", $sSelect);
        $this->assertEquals("select `oxv_oxattribute`.`oxid`, `oxv_oxattribute`.`oxshopid`, `oxv_oxattribute`.`oxtitle`, `oxv_oxattribute`.`oxtitle_1`, `oxv_oxattribute`.`oxtitle_2`, `oxv_oxattribute`.`oxtitle_3`, `oxv_oxattribute`.`oxpos`, `oxv_oxattribute`.`oxtimestamp`, `oxv_oxattribute`.`oxdisplayinbasket` from oxv_oxattribute where 1 and oxid = '111'", $sSelect);
    }

    /**
     * Test build select string without shop id.
     *
     * @return null
     */
    public function testBuildSelectStringWithShopId()
    {
        if ($this->getConfig()->getEdition() === 'EE') {
            $this->markTestSkipped("Test for Community and Professional editions only");
        }

        $oBase = oxNew('oxBase');
        $oBase->init("oxattribute");
        $sSelect = $oBase->buildSelectString(array("oxid" => "111"));
        $sSelect = str_replace("  ", " ", $sSelect);
        $this->assertEquals("select `oxv_oxattribute`.`oxid`, `oxv_oxattribute`.`oxshopid`, `oxv_oxattribute`.`oxtitle`, `oxv_oxattribute`.`oxtitle_1`, `oxv_oxattribute`.`oxtitle_2`, `oxv_oxattribute`.`oxtitle_3`, `oxv_oxattribute`.`oxpos`, `oxv_oxattribute`.`oxtimestamp`, `oxv_oxattribute`.`oxdisplayinbasket` from oxv_oxattribute where 1 and oxid = '111'", $sSelect);
    }

    /**
     * Test build select string without where clause.
     *
     * @return null
     */
    public function testBuildSelectStringWithoutWhere()
    {
        $oBase = new _oxBase();
        $oBase->init('oxuser');
        $sSelect = $oBase->buildSelectString();

        $oDB = oxDb::getDb();

        $rs = $oDB->select($sSelect);
        $expectedCount = $this->getConfig()->getEdition() === 'EE' ? 6 : 1;
        $this->assertEquals($expectedCount, $rs->count());
    }

    /**
     * Test select existing data.
     *
     * @return null
     */
    public function testSelectExistingData()
    {
        $sSelect = "select oxactions.oxid, oxactions.oxtitle from oxactions  where oxactions.oxid = 'oxtopstart'";
        $oBase = new _oxBase();
        $oBase->init('oxactions');
        $this->assertEquals($oBase->assignRecord($sSelect), true);
        $this->assertEquals($oBase->oxactions__oxid->value, 'oxtopstart');
    }

    /**
     * Test select non existing data.
     *
     * @return null
     */
    public function testSelectNonExistingData()
    {
        $sSelect = "select oxactions.oxid,oxactions.oxtitle from oxactions  where oxactions.oxid = 'sss'";
        $oBase = new _oxBase();
        $this->assertFalse($oBase->assignrecord($sSelect));
    }

    /**
     * Test get select fields.
     *
     * @return null
     */
    public function testGetSelectFields()
    {
        $oBase = oxNew('oxBase');
        $oBase->init('oxactions');

        $sView = getViewName('oxactions', -1);
        $this->assertEquals("`$sView`.`oxid`, `$sView`.`oxshopid`, `$sView`.`oxtype`, `$sView`.`oxtitle`, `$sView`.`oxtitle_1`, `$sView`.`oxtitle_2`, `$sView`.`oxtitle_3`, `$sView`.`oxlongdesc`, `$sView`.`oxlongdesc_1`, `$sView`.`oxlongdesc_2`, `$sView`.`oxlongdesc_3`, `$sView`.`oxactive`, `$sView`.`oxactivefrom`, `$sView`.`oxactiveto`, `$sView`.`oxpic`, `$sView`.`oxpic_1`, `$sView`.`oxpic_2`, `$sView`.`oxpic_3`, `$sView`.`oxlink`, `$sView`.`oxlink_1`, `$sView`.`oxlink_2`, `$sView`.`oxlink_3`, `$sView`.`oxsort`, `$sView`.`oxtimestamp`", $oBase->getSelectFields());
    }

    /**
     * Test get select fields without table specified.
     *
     * @return null
     */
    public function testGetSelectFieldsNoFields()
    {
        $oBase = oxNew('oxBase');
        $this->assertEquals($oBase->getSelectFields(), ".`oxid`");
    }

    /**
     *  Test exists, positive.
     *
     * @return null
     */
    public function testExists()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $oBase->setId("oxtopstart");
        $this->assertTrue($oBase->exists());
    }

    /**
     * Test exists, negative.
     *
     * @return null
     */
    public function testExistsNot()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $oBase->setId("oxstartas");
        $this->assertFalse($oBase->exists());
    }

    /**
     * Test exists with id, positive.
     *
     * @return null
     */
    public function testExistsWithId()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $this->assertTrue($oBase->exists("oxtopstart"));
    }

    /**
     * Test exists with id, negative.
     *
     * @return null
     */
    public function testExistsWithIdIfNotExists()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $this->assertFalse($oBase->exists("oxstartas"));
    }

    /**
     * Test exists when not loaded.
     *
     * @return null
     */
    public function testExistsNotLoaded()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $this->assertFalse($oBase->exists());
    }

    /**
     * Test delete.
     *
     * @return null
     */
    public function testDelete()
    {
        $myDB = oxDb::getDb();
        $sInsert = "Insert into oxactions (`OXID`, `OXTITLE`) values ('_test', 'test')";
        $myDB->Execute($sInsert);
        // loading one from a predefined list
        $oBase = new _oxBase();
        $oBase->init("oxactions");

        // now deleting and checking for records in DB
        $sResult = $oBase->delete("_test");
        $this->assertEquals(0, (int) $myDB->getOne('select count(*) from oxactions where oxid = "_test"'));
        $this->assertTrue($sResult);
    }

    /**
     * Test delete with Is Derived.
     *
     * @return null
     */
    public function testDeleteIsDerived()
    {
        $myDB = oxDb::getDb();
        $sInsert = "Insert into oxactions (`OXID`, `OXTITLE`) values ('_test','test')";
        $myDB->Execute($sInsert);
        // loading one from a predefined list
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $oBase->setId("_test");
        $oBase->setIsDerived(true);
        // now deleting and checking for records in DB
        $sResult = $oBase->delete();
        $this->assertEquals(1, (int) $myDB->getOne('select count(*) from oxactions where oxid = "_test"'));
        $this->assertFalse($sResult);
    }

    /**
     * Test delete with set oxid.
     *
     * @return null
     */
    public function testDeleteWithSetOxid()
    {
        $myDB = oxDb::getDb();
        $sInsert = "Insert into oxactions (`OXID`, `OXTITLE`) values ('_test','test')";
        $myDB->Execute($sInsert);
        // loading one from a predefined list
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $oBase->setId("_test");
        // now deleting and checking for records in DB
        $sResult = $oBase->delete();
        $this->assertEquals(0, (int) $myDB->getOne('select count(*) from oxactions where oxid = "_test"'));
        $this->assertTrue($sResult);
    }

    /**
     * Test delete without oxid.
     *
     * @return null
     */
    public function testDeleteWithoutOxid()
    {
        $myDB = oxDb::getDb();
        $sInsert = "Insert into oxactions (`OXID`, `OXTITLE`) values ('_test','test')";
        $myDB->Execute($sInsert);
        // loading one from a predefined list
        $oBase = new _oxBase();
        $oBase->init('oxactions');
        // now deleting and checking for records in DB
        $sResult = $oBase->delete();
        $this->assertEquals(1, (int) $myDB->getOne('select count(*) from oxactions where oxid = "_test"'));
        $this->assertEquals(false, $sResult);
    }

    /**
     * Test delete with oxid, negative.
     *
     * @return null
     */
    public function testDeleteWithNonExistingOxid()
    {
        $myDB = oxDb::getDb();
        $sInsert = "Insert into oxactions (`OXID`, `OXTITLE`) values ('_test','test')";
        $myDB->Execute($sInsert);
        // loading one from a predefined list
        $oBase = new _oxBase();
        $oBase->init('oxactions');
        // now deleting and checking for records in DB
        $sResult = $oBase->delete("ssss");

        $this->assertEquals(1, (int) $myDB->getOne('select count(*) from oxactions where oxid = "_test"'));
        $this->assertEquals(false, $sResult);
    }

    /**
     * Test save if exists.
     *
     * @return null
     */
    public function testSaveIfExists()
    {
        $oBase = new _oxBase();
        $oBase = $this->getMock(\OxidEsales\Eshop\Core\Model\BaseModel::class, array('update'));
        $oBase->expects($this->any())
            ->method('update')
            ->will($this->returnValue(true));
        $oBase->init('oxactions');
        $oBase->setId('oxtopstart');
        $sResult = $oBase->save();
        $this->assertEquals('oxtopstart', $sResult);
    }

    /**
     * Test save if fields not set.
     *
     * @return null
     */
    public function testSaveIfFieldsNotSet()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $oBase->setId("oxtopstart");
        $oBase->setClassVar("_aFieldNames", null);
        $this->assertFalse($oBase->save());
    }

    /**
     * Test save if new.
     *
     * @return null
     */
    public function testSaveIfNew()
    {
        $oBase = $this->getMock(\OxidEsales\Eshop\Core\Model\BaseModel::class, array('_insert'));
        $oBase->expects($this->any())
            ->method('_insert')
            ->will($this->returnValue(true));
        $oBase->init("oxactions");
        $oBase->setId("_test");
        $sResult = $oBase->save();
        $this->assertEquals("_test", $sResult);
    }

    /**
     * Test save if can not insert.
     *
     * @return null
     */
    public function testSaveIfCannotInsert()
    {
        $oBase = $this->getMock(\OxidEsales\Eshop\Core\Model\BaseModel::class, array('_insert'));
        $oBase->expects($this->any())
            ->method('_insert')
            ->will($this->returnValue(false));
        $oBase->init("oxactions");
        $oBase->setId("_test");
        $sResult = $oBase->save();
        $this->assertFalse($sResult);
    }

    /**
     * Test save if is derived.
     *
     * @return null
     */
    public function testSaveIsDerived()
    {
        $oBase = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\_oxBase::class, array('update'));
        $oBase->expects($this->any())
            ->method('update')
            ->will($this->returnValue(true));
        $oBase->init("oxactions");
        $oBase->setId("oxtopstart");
        $oBase->setIsDerived(true);
        $sResult = $oBase->save();
        $this->assertFalse($sResult);
    }

    public function testSaveWithTimeStampTypeColumn(): void
    {
        $myDB = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
        $sInsert = "Insert into oxuserbaskets (`OXID`,`OXUSERID`,`OXTITLE`) values ('_test','test','test')";
        $myDB->Execute($sInsert);
        $oBase = new _oxBase();
        $oBase->init('oxuserbaskets');
        $oBase->load("_test");
        $oBase->oxuserbaskets__oxupdate = new oxField("2007.07.07", oxField::T_RAW);
        $sResult = $oBase->save();

        $this->assertNotNull($sResult);
        $myDB = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
        $res = $myDB->select("select oxupdate from oxuserbaskets where oxid='_test'");
        $this->assertNotEquals("2007-07-07 00:00:00", $res->fields['oxupdate']);
    }

    public function testSaveWithDateTimeTypeColumn(): void
    {
        $oBase = new _oxBase();
        $oBase->init('oxdiscount');
        $oBase->setId('_test');
        $oBase->oxdiscount__oxtitle = new oxField("test", oxField::T_RAW);
        $oBase->oxdiscount__oxactivefrom = new oxField("2007.07.07", oxField::T_RAW);
        $sResult = $oBase->save();

        $this->assertNotNull($sResult);

        $sActivefrom = oxDb::getDb()->getOne("select oxactivefrom from oxdiscount where oxid='_test'");
        $this->assertEquals("2007-07-07 00:00:00", $sActivefrom);
    }

    public function testSaveWithDateTypeColumn(): void
    {
        $table = 'oxarticles';
        $col1 = 'oxtitle';
        $col2 = 'oxdelivery'; /** any DB column with type Date */
        $field1 = "{$table}__{$col1}";
        $field2 = "{$table}__{$col2}";
        $val1 = 'test';
        $val2 = '2022-12-22';
        $base = new _oxBase();
        $base->init($table);
        $base->setId('_test');
        $base->$field1 = new oxField($val1, oxField::T_RAW);
        $base->$field2 = new oxField($val2, oxField::T_RAW);

        $return = $base->save();

        $res = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->select(
            "select `$col2` from `$table` where `$col1` = '$val1'"
        );
        $this->assertNotNull($return);
        $this->assertSame($val2, $res->fields[$col2]);
    }

    /**
     * Test update without oxid.
     *
     * @return null
     */
    public function testUpdateWithoutOXID()
    {
        $oBase = new _oxBase();
        $oBase->init("oxarticles");
        try {
            $oBase->update();
        } catch (Exception $e) {
        }

        $this->assertTrue($e instanceof \OxidEsales\EshopCommunity\Core\Exception\ObjectException);
    }

    /**
     * Test update with oxid.
     *
     * @return null
     */
    public function testUpdateWithOXID()
    {
        $myDB = oxDb::getDb();
        $shopId = $this->getUpdateShopId();
        $sInsert = "Insert into oxarticles (`OXID`,`OXSHOPID`,`OXTITLE`) values ('_test','{$shopId}','test')";
        $myDB->Execute($sInsert);

        $oBase = new _oxBase();
        $oBase->init("oxarticles");
        $oBase->setId("_test");
        $oBase->oxarticles__oxtitle = new oxField('changed title', oxField::T_RAW);
        $sResult = $oBase->update();
        $this->assertNotNull($sResult);
        $myDB = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
        $res = $myDB->select("select oxtitle from oxarticles where oxid='_test'");
        $this->assertEquals($res->fields['oxtitle'], "changed title");
    }

    /**
     * Test update with oxid and is derived.
     *
     * @return null
     */
    public function testUpdateWithOXIDIsDerived()
    {
        $myDB = oxDb::getDb();
        $sInsert = "Insert into oxarticles (`OXID`,`OXTITLE`) values ('_test','test')";
        $myDB->Execute($sInsert);

        $oBase = new _oxBase();
        $oBase->init("oxarticles");
        $oBase->setId("_test");
        $oBase->setIsDerived(true);
        $sResult = $oBase->update();
        $this->assertFalse($sResult);
    }

    /**
     * Test update with oxid, wrong sql.
     */
    public function testUpdateWithOXIDWrongSql()
    {
        $myDB = oxDb::getDb();
        $sInsert = "Insert into oxarticles (`OXID`,`OXTITLE`) values ('_test','test')";
        $myDB->Execute($sInsert);

        $oBase = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\_oxBase::class, array('_getUpdateFields'));
        $oBase->expects($this->any())
            ->method('_getUpdateFields')
            ->will($this->returnValue(''));

        $oBase->init("oxarticles");
        $oBase->setId("_test");

        try {
            $oBase->update();
        } catch (Exception $e) {
            return;
        }

        //TODO: fix this iDebug
        if ($this->getConfig()->getConfigParam('iDebug')) {
            $this->fail('Update exception not caught');
        }
    }

    public function testInsertWithValidDataWillSaveShopId(): void
    {
        $table = 'oxactions';
        $col1 = 'oxtitle';
        $colId = 'oxid';
        $colShopId = 'oxshopid';
        $field1 = "{$table}__{$col1}";
        $fieldId = "{$table}__{$colId}";
        $fieldShopId = "{$table}__{$colShopId}";
        $val1 = 'test';
        $base = oxNew('oxBase');
        $base->init($table);
        $base->$field1 = new oxField($val1, oxField::T_RAW);

        $return = $base->UNITinsert();

        $count = (int) oxDb::getDb()->getOne(
            "select count(*) from `$table` where $col1 = '$val1'"
        );
        $this->assertSame(1, $count);
        $this->assertNotNull($return);
        $this->assertEquals($base->getId(), $base->$fieldId->value);
        $this->assertEquals($this->getShopId(), $base->$fieldShopId->value);
    }

    /**
     * Test insert with set oxid id.
     *
     * @return null
     */
    public function testInsertWithSetOxid()
    {
        $myDB = oxDb::getDb();
        $oBase = new _oxBase();
        $oBase->init('oxactions');
        $oBase->setId("_test");
        $sResult = $oBase->insert();
        $this->assertEquals(1, (int) $myDB->getOne('select count(*) from oxactions where oxid = "_test"'));
        $this->assertNotNull($sResult);
        $this->assertEquals($oBase->getId(), $oBase->oxactions__oxid->value);
    }

    /**
     * Test insert without oxid.
     *
     * @return null
     */
    public function testInsertWithoutOxid()
    {
        $myDB = oxDb::getDb();
        $oBase = new _oxBase();
        $oBase->init('oxactions');
        $oBase->oxactions__oxtitle = new oxField('test1', oxField::T_RAW);
        $sResult = $oBase->insert();

        $this->assertEquals(1, (int) $myDB->getOne('select count(*) from oxactions where oxtitle = "test1"'));
        $this->assertNotNull($sResult);
        $this->assertEquals($oBase->getId(), $oBase->oxactions__oxid->value);
    }

    /**
     * Test get object view name.
     */
    public function testGetObjectViewName()
    {
        if ($this->getConfig()->getEdition() === 'EE') {
            $this->markTestSkipped("Test for Community and Professional editions only");
        }

        $oBase = oxNew('oxBase');
        $sResult = $oBase->UNITgetObjectViewName("oxarticles");
        $this->assertEquals("oxv_oxarticles", $sResult);
    }

    /**
     * Test get object view name, forcing core table usage.
     */
    public function testGetObjectViewNameForceCoreTblUsage()
    {
        if ($this->getConfig()->getEdition() === 'EE') {
            $this->markTestSkipped("Test for Community and Professional editions only");
        }

        $oBase = oxNew('oxBase');
        $sResult = $oBase->UNITgetObjectViewName("oxarticles", "1");
        $this->assertEquals("oxv_oxarticles", $sResult);
    }

    public function testGetObjectViewNameWithNonMultiShopTable(): void
    {
        if ($this->getConfig()->getEdition() === 'EE') {
            $this->markTestSkipped('Test for Community and Professional editions only');
        }
        $shopId = '1';
        $table = 'oxactions';
        $base = oxNew('oxBase');

        $sResult = $base->UNITgetObjectViewName($table, $shopId);

        $this->assertSame("oxv_$table", $sResult);
    }

    /**
     * Test get all fields, full.
     *
     * @return null
     */
    public function testGetAllFieldsFull()
    {
        $oBase = new _oxBase();
        $oBase->init('oxactions');
        $aExpectedFields = array('oxid' => 0, 'oxshopid' => 0, 'oxtype' => 0, 'oxtitle' => 0, 'oxtitle_1' => 0, 'oxtitle_2' => 0, 'oxtitle_3' => 0, 'oxlongdesc' => 0, 'oxlongdesc_1' => 0, 'oxlongdesc_2' => 0, 'oxlongdesc_3' => 0, 'oxactive' => 0, 'oxactivefrom' => 0, 'oxactiveto' => 0, 'oxpic' => 0, 'oxpic_1' => 0, 'oxpic_2' => 0, 'oxpic_3' => 0, 'oxlink' => 0, 'oxlink_1' => 0, 'oxlink_2' => 0, 'oxlink_3' => 0, 'oxsort' => 0, 'oxtimestamp' => 0);

        $this->assertEquals($aExpectedFields, $oBase->UNITgetAllFields(true));
    }

    /**
     * A test for #1831 case
     *
     * @return null
     */
    public function testGetAllFieldEmpty()
    {
        $oBase = new _oxBase();
        //should not throw any error
        $oBase->UNITgetAllFields();
    }

    /**
     * Test add field.
     *
     * @return null
     */
    public function testAddField()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_sCoreTable", "oxtesttable");
        $oBase->UNITaddField('oxtestfield', 1);

        $aFieldNames = $oBase->getClassVar("_aFieldNames");

        $this->assertEquals(array("oxid" => 0, "oxtestfield" => 1), $aFieldNames);
        $this->assertTrue(isset($oBase->oxtesttable__oxtestfield));
    }

    /**
     * Test add field with specified length.
     *
     * @return null
     */
    public function testAddFieldIfLenghtSet()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_sCoreTable", "oxtesttable");
        $oBase->UNITaddField('oxtestfield', 1, null, 20);

        $aFieldNames = $oBase->getClassVar("_aFieldNames");

        $this->assertEquals(array("oxid" => 0, "oxtestfield" => 1), $aFieldNames);
        $this->assertTrue(isset($oBase->oxtesttable__oxtestfield));
        $this->assertEquals(20, $oBase->oxtesttable__oxtestfield->fldmax_length);
        $this->assertFalse($oBase->getClassVar("_blIsSimplyClonable"));
    }

    /**
     * Test add field with specified type.
     *
     * @return null
     */
    public function testAddFieldIfTypeSet()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_sCoreTable", "oxtesttable");
        $oBase->UNITaddField('oxtestfield', 1, 'datetime');

        $aFieldNames = $oBase->getClassVar("_aFieldNames");

        $this->assertEquals(array("oxid" => 0, "oxtestfield" => 1), $aFieldNames);
        $this->assertTrue(isset($oBase->oxtesttable__oxtestfield));
        $this->assertEquals('datetime', $oBase->oxtesttable__oxtestfield->fldtype);
        $this->assertFalse($oBase->getClassVar("_blIsSimplyClonable"));
    }

    /**
     * Testinc active snippet getter.
     *
     * @return null
     */
    public function testGetSqlActiveSnippet()
    {
        $iCurrTime = 1453734000; //some rounded timestamp

        $oUtilsDate = $this->getMock(\OxidEsales\Eshop\Core\UtilsDate::class, array('getRequestTime'));
        $oUtilsDate->expects($this->any())->method('getRequestTime')->will($this->returnValue($iCurrTime));
        /** @var oxUtilsDate $oUtils */
        Registry::set(\OxidEsales\Eshop\Core\UtilsDate::class, $oUtilsDate);

        $aFields = array('oxactive' => 1, 'oxactivefrom' => 1, 'oxactiveto' => 1);
        $sDate = date('Y-m-d H:i:s', $iCurrTime);

        $oBase = $this->getProxyClass('oxbase');
        $oBase->setNonPublicVar('_aFieldNames', $aFields);
        $oBase->setNonPublicVar('_sCoreTable', 'oxbase');

        $sPattern = " (   oxbase.oxactive = 1  or  ( oxbase.oxactivefrom < '$sDate' and oxbase.oxactiveto > '$sDate' ) ) ";

        $this->assertEquals($sPattern, $oBase->getSqlActiveSnippet());
    }

    /**
     * Test get update field value.
     *
     * @return null
     */
    public function test_getUpdateFieldValue()
    {
        $oBase = new _oxBase();
        $oBase->init("oxarticles");
        $oBase->setId('test');
        $this->assertSame("'aaa'", $oBase->getUpdateFieldValue('oxid', new oxField('aaa')));
        $this->assertSame("'aaa\\\"'", $oBase->getUpdateFieldValue('oxid', new oxField('aaa"')));
        $this->assertSame("'aaa\''", $oBase->getUpdateFieldValue('oxid', new oxField('aaa\'')));

        $this->assertSame("''", $oBase->getUpdateFieldValue('oxid', new oxField(null)));
        $this->assertSame('null', $oBase->getUpdateFieldValue('oxvat', new oxField(null)));
    }

    /**
     * Test set field data with bad parameters.
     *
     * @return null
     */
    public function test_setFieldData_withBadParam()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $aFlds = $oBase->getProperty('_aFieldNames');
        $oBase->setFieldData('oxid', 'aaa');
        $this->assertEquals($aFlds, $oBase->getProperty('_aFieldNames'));
        $oBase->setFieldData('lalala', 'aaa');
        $this->assertEquals($aFlds, $oBase->getProperty('_aFieldNames'));
    }

    /**
     * Test lazy loading and multilang field problem.
     *
     * @return null
     */
    public function testTestingLazyLoadAndMultilangFieldProblem()
    {
        $sId = oxDb::getDb()->getOne("select oxid from oxarticles where oxtitle_1 != '' and oxtitle != oxtitle_1");
        $sTitle = oxDb::getDb()->getOne("select oxtitle_1 from oxarticles where oxid='$sId'");
        $oArticle = oxNew('oxArticle');
        $oArticle->loadInLang(1, $sId);

        $this->assertEquals($sTitle, $oArticle->oxarticles__oxtitle->value);
    }

    /**
     * Test non existing field is never registered.
     *
     * @return null
     */
    public function testNonExistantFieldIsNeverRegistered()
    {
        $oTest = new _oxBase();
        $oTest->modifyCacheKey("nonExistantFieldTest", true);
        $oTest->enableLazyLoading();
        $this->cleanTmpDir();
        $oTest->init('oxarticles');
        //trying to access the field
        $sTestValue = $oTest->oxarticles__oxnonexistantfield;

        //checking, should NOT be cached
        $sCacheKey = 'fieldnames_oxarticles_nonExistantFieldTest';
        $aFieldNames = oxRegistry::getUtils()->fromFileCache($sCacheKey);

        $this->assertFalse(isset($aFieldNames['nonexistantfield']));
    }

    /**
     * Test set is derived.
     *
     * @return null
     */
    public function testSetIsDerived()
    {
        $obj = oxNew('oxBase');
        $obj->setIsDerived(true);
        $this->assertTrue($obj->isDerived());
        $obj->setIsDerived(false);
        $this->assertFalse($obj->isDerived());
    }

    /**
     * Test is ox.
     *
     * @return null
     */
    public function testIsOx()
    {
        $obj = oxNew('oxBase');
        $obj->setId('oxtest');
        $this->assertTrue($obj->isOx());
        $obj->setId('test');
        $this->assertFalse($obj->isOx());
    }

    /**
     * Test set get readonly.
     *
     * @return null
     */
    public function testSetGetReadOnly()
    {
        $oVendor = $this->getProxyClass("oxvendor");
        $oVendor->setReadOnly(true);

        $this->assertTrue($oVendor->isReadOnly());
    }

    /**
     * Test set in list.
     *
     * @return null
     */
    public function testSetInList()
    {
        $oSubj = $this->getProxyClass('oxBase');
        $this->assertFalse($oSubj->getNonPublicVar("_blIsInList"));
        $oSubj->setInList();
        $this->assertTrue($oSubj->getNonPublicVar("_blIsInList"));
    }

    /**
     * Test in in list.
     *
     * @return null
     */
    public function testIsInList()
    {
        $oSubj = $this->getProxyClass('oxBase');
        $this->assertFalse($oSubj->UNITisInList());
        $oSubj->setNonPublicVar("_blIsInList", true);
        $this->assertTrue($oSubj->UNITisInList());
    }

    /**
     * Field names getter test
     *
     * @return null
     */
    public function testGetFieldNamesOnBase()
    {
        $oBase = oxNew('oxBase');
        $this->assertEquals(array("oxid"), $oBase->getFieldNames());

        $oBase->init("notExistingTable");
        $this->assertEquals(array("oxid"), $oBase->getFieldNames());
    }

    /**
     * Field names getter test
     *
     * @return null
     */
    public function testGetFieldNamesNoLazyLoading()
    {
        // Content model has NO lazy loading enabled.
        $oBase = oxNew('oxContent');

        $aFieldNames = $oBase->getFieldNames();

        $this->assertTrue(is_array($aFieldNames) && count($aFieldNames) > 0);
        $this->assertTrue(
            in_array("oxtitle", $aFieldNames),
            "oxtitle expected to be in array:  " . serialize($aFieldNames)
        );
    }

    /**
     * Field names getter test
     *
     * @return null
     */
    public function testGetFieldNamesWithLazyLoading()
    {
        // Article model has lazy loading enabled.
        $oBase = oxNew('oxArticle');

        $oBase->init("oxarticles");
        $aFieldNames = $oBase->getFieldNames();

        $this->assertTrue(is_array($aFieldNames) && count($aFieldNames) > 0);
        $this->assertTrue(in_array("oxtitle", $aFieldNames));
    }

    /**
     * Field names getter test
     *
     * @return null
     */
    public function testGetFieldNamesWithLazyLoadingOnAdmin()
    {
        $this->setAdminMode(true);

        // Article model has lazy loading enabled.
        $oBase = oxNew('oxArticle');

        $oBase->init("oxarticles");
        $oBase->setEnableMultilang(false);
        $aFieldNames = $oBase->getFieldNames();

        $this->assertTrue(is_array($aFieldNames) && count($aFieldNames) > 0);
        $this->assertTrue(in_array("oxtitle", $aFieldNames));
    }

    public function testFunctionIsPropertyLoadedReturnsFalseWhenPropertyIsNotLoaded()
    {
        $model = new _oxBase();
        $model->setClassVar("_blUseLazyLoading", true);

        $this->assertFalse($model->isPropertyLoaded('propertyName'));
    }

    public function testFunctionIsPropertyLoadedReturnsTrueWhenPropertyIsLoaded()
    {
        $model = new _oxBase();
        $model->setClassVar("_blUseLazyLoading", true);
        $model->propertyName = 'someValue';

        $this->assertTrue($model->isPropertyLoaded('propertyName'));
    }

    public function testFunctionIsPropertyLoadedReturnsFalseWhenPropertyIsNull()
    {
        $model = new _oxBase();
        $model->setClassVar("_blUseLazyLoading", true);
        $model->propertyName = null;

        $this->assertFalse($model->isPropertyLoaded('propertyName'));
    }

    public function testFunctionIsPropertyLoadedReturnsTrueWhenPropertyIsFalse()
    {
        $model = new _oxBase();
        $model->setClassVar("_blUseLazyLoading", true);
        $model->propertyName = false;

        $this->assertTrue($model->isPropertyLoaded('propertyName'));
    }

    public function testFunctionIsPropertyLoadedReturnsTrueWhenPropertyIsEmptyString()
    {
        $model = new _oxBase();
        $model->setClassVar("_blUseLazyLoading", true);
        $model->propertyName = '';

        $this->assertTrue($model->isPropertyLoaded('propertyName'));
    }


    public function testLazyLoadingMagicIssetReturnsFalseWhenPropertyIsNotLoaded()
    {
        $model = new _oxBase();
        $model->setClassVar("_blUseLazyLoading", true);

        $this->assertFalse(isset($model->propertyName));
    }

    public function testLazyLoadingMagicIssetReturnsFalseWhenPropertyIsNull()
    {
        $model = new _oxBase();
        $model->setClassVar("_blUseLazyLoading", true);
        $model->propertyName = null;

        $this->assertFalse(isset($model->propertyName));
    }

    public function testLazyLoadingMagicIssetReturnsTrueWhenPropertyIsFalse()
    {
        $model = new _oxBase();
        $model->setClassVar("_blUseLazyLoading", true);
        $model->propertyName = false;

        $this->assertTrue(isset($model->propertyName));
    }

    public function testLazyLoadingMagicIssetReturnsTrueWhenPropertyIsEmptyString()
    {
        $model = new _oxBase();
        $model->setClassVar("_blUseLazyLoading", true);
        $model->propertyName = '';

        $this->assertTrue(isset($model->propertyName));
    }

    public function testLazyLoadingMagicIssetReturnsTrueWhenPropertyIsLoaded()
    {
        $model = new _oxBase();
        $model->setClassVar("_blUseLazyLoading", true);
        $model->propertyName = 'someValue';

        $this->assertTrue(isset($model->propertyName));
    }
}
