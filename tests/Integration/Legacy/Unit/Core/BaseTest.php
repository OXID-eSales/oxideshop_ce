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
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\FieldTestingTrait;
use OxidEsales\Facts\Facts;
use oxRegistry;
use oxUtils;
use stdClass;

require_once TEST_LIBRARY_HELPERS_PATH . 'oxBaseHelper.php';

class _oxBase extends oxBase
{
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
    public function setFieldData($sName, $sValue, $dataType = Field::T_TEXT)
    {
        return parent::setFieldData($sName, $sValue, $dataType);
    }

    /**
     * Force isDerived property.
     *
     * @param bool $blIsDerived is derived
     */
    public function setIsDerived($blIsDerived)
    {
        $this->_blIsDerived = $blIsDerived;
    }

    /**
     * Force enable lazy loading.
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
        return parent::update();
    }

    /**
     * Force insert.
     *
     * @return mixed
     */
    public function insert()
    {
        return parent::insert();
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
        return parent::getObjectViewName($sTable, $shopId);
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
        return parent::initDataStructure($blForceFullStructure);
    }

    /**
     * Force _blUseAccessProp to true thus enabling access property usage.
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
        return parent::getUpdateFieldValue($sFieldName, $oField);
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

class BaseTest extends \OxidTestCase
{
    use FieldTestingTrait;
    use ContainerTrait;

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

        parent::teardown();
    }

    public function getUpdateShopId()
    {
        return 1;
    }

    public function getArticlesViewName()
    {
        return (new Facts())->getEdition() === 'EE' ? 'oxv_oxarticles_1' : 'oxv_oxarticles';
    }

    /**
     * Test is loaded.
     */
    public function testIsLoaded()
    {
        $oBase = $this->getMock(\OxidEsales\Eshop\Core\Model\BaseModel::class, ["addField", "buildSelectString", "assignRecord", "getViewName"]);
        $oBase->expects($this->once())->method('addField')->with($this->equalTo('oxid'), $this->equalTo(0));
        $oBase->expects($this->once())->method('getViewName')->will($this->returnValue("testView"));
        $oBase->expects($this->once())->method('buildSelectString')->with($this->equalto(["testView.oxid" => "xxx"]))->will($this->returnValue("testSql"));
        $oBase->expects($this->once())->method('assignRecord')->with($this->equalTo("testSql"))->will($this->returnValue(true));

        $this->assertFalse($oBase->isLoaded());

        $oBase->load("xxx");
        $this->assertTrue($oBase->isLoaded());
    }

    /**
     * Test is derived when both shop ids are null.
     */
    public function testIsDerivedBothShopIdsAreNull()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getShopId']);
        $oConfig->expects($this->any())->method('getShopId')->will($this->returnValue(null));

        $oBase = $this->getMock(\OxidEsales\Eshop\Core\Model\BaseModel::class, ['getConfig', 'getShopId'], [], '', false);
        Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oBase->expects($this->any())->method('getShopId')->will($this->returnValue(null));

        $this->assertNull($oBase->isDerived());
    }

    /**
     * Test is derived when shop ids match.
     */
    public function testIsDerivedShopIdsMatch()
    {
        $expected = (new Facts())->getEdition() === 'EE' ? false : null;

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getShopId']);
        $oConfig->expects($this->any())->method('getShopId')->will($this->returnValue('xxx'));

        $oBase = $this->getMock(\OxidEsales\Eshop\Core\Model\BaseModel::class, ['getConfig', 'getShopId'], [], '', false);
        Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oBase->expects($this->any())->method('getShopId')->will($this->returnValue('xxx'));

        $this->assertSame($expected, $oBase->isDerived());
    }

    /**
     * Test is derived when shop ids does not match.
     */
    public function testIsDerivedShopIdsDeosNotMatch()
    {
        $expected = (new Facts())->getEdition() === 'EE' ? true : null;

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getShopId']);
        $oConfig->expects($this->any())->method('getShopId')->will($this->returnValue('xxx'));

        $oBase = $this->getMock(\OxidEsales\Eshop\Core\Model\BaseModel::class, ['getConfig', 'getShopId'], [], '', false);
        Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oBase->expects($this->any())->method('getShopId')->will($this->returnValue('yyy'));

        $this->assertSame($expected, $oBase->isDerived());
    }

    /**
     * Test allow derived update.
     */
    public function testAllowDerivedUpdate()
    {
        $oBase = $this->getMock(\OxidEsales\Eshop\Core\Model\BaseModel::class, ['isDerived']);
        $oBase->expects($this->once())->method('isDerived')->will($this->returnValue(false));
        $this->assertTrue($oBase->allowDerivedUpdate());
    }

    /**
     * Test allow derived delete.
     */
    public function testAllowDerivedDelete()
    {
        $oBase = $this->getMock(\OxidEsales\Eshop\Core\Model\BaseModel::class, ['isDerived']);
        $oBase->expects($this->once())->method('isDerived')->will($this->returnValue(false));
        $this->assertTrue($oBase->allowDerivedDelete());
    }

    /**
     * Test converting fields.
     */
    public function testConvertingFields()
    {
        $oBase = $this->getMock(\OxidEsales\Eshop\Core\Model\BaseModel::class, ['initDataStructure', 'isAdmin', 'exists', 'isDerived', 'update', 'insert', 'onChange', 'getId']);
        $oBase->expects($this->once())->method('initDataStructure');
        $oBase->expects($this->once())->method('isAdmin')->will($this->returnValue(true));
        $oBase->expects($this->once())->method('exists')->will($this->returnValue(false));
        $oBase->expects($this->never())->method('isDerived');
        $oBase->expects($this->never())->method('update');
        $oBase->expects($this->once())->method('insert')->will($this->returnValue(true));
        $oBase->expects($this->once())->method('onChange')->with($this->equalTo(ACTION_INSERT));
        $oBase->expects($this->once())->method('getId')->will($this->returnValue('ggg'));

        // initing ..
        $oBase->init('oxsomething');

        // adding some test fields ..
        $oBase->addField('oxfield1', 1, 'datetime');
        $oBase->addField('oxfield2', 1, 'timestamp');
        $oBase->addField('oxfield3', 1, 'date');

        $this->assertEquals('ggg', $oBase->save());

        $this->assertEquals("0000-00-00 00:00:00", $oBase->oxsomething__oxfield1->value);
        $this->assertEquals("00000000000000", $oBase->oxsomething__oxfield2->value);
        $this->assertEquals("0000-00-00", $oBase->oxsomething__oxfield3->value);
    }

    /**
     * Constructor test
     */
    public function testOxBase()
    {
        $oBase = oxNew('oxBase');

        $this->assertEquals($this->getConfig()->getShopId(), $oBase->getShopId());
        $this->assertNotEquals(0, $this->getConfig()->getShopID());
    }

    /**
     * Testing object cloning (actually properties copying) function.
     */
    public function testOxClone()
    {
        $o = oxNew('oxBase');
        $u = oxNew('oxBase');
        $u->aa = 'a';
        $handle = new stdClass();
        $u->ab = $handle;
        $handle->z = 'z';
        $o->oxClone($u);
        $handle->z = 'x';
        $this->assertEquals('a', $o->aa);
        $this->assertEquals('z', $o->ab->z);
    }

    /**
     * Testing blIsDerived magic getter.
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
     */
    public function testIsReadOnly()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_blReadOnly", true);
        $this->assertFalse(property_exists($oBase, 'blReadOnly') && $oBase->blReadOnly !== null);
        $this->assertTrue($oBase->blReadOnly);
    }

    /**
     * Testing simple lazy loading .
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
     */
    public function testMagicGetLazyLoadingNoObjectFound()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_blUseLazyLoading", true);
        $oBase->init("oxarticles");
        $oBase->setId("test");
        $this->assertNull($oBase->oxarticles__oxtitle);
    }

    /**
     * Simple lazy loading test, if loaded non existing field with debug.
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
        $oBase->oxarticles__oxtitle->value;
        $oBase->oxarticles__oxshortdesc->value;

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
     */
    public function testInitDataFullStructure()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_sCoreTable", "oxarticles");
        $oBase->setClassVar("_blUseLazyLoading", "true");
        $oBase->initDataStructure(true);
        $this->assertTrue(property_exists($oBase, 'oxarticles__oxtitle') && $oBase->oxarticles__oxtitle !== null);
        $aFieldNames = $oBase->getClassVar("_aFieldNames");
        $this->assertTrue(isset($aFieldNames['oxtitle']));
    }

    /**
     * Test get field longname.
     */
    public function testGetFieldLongName()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_sCoreTable", "oxtesttable");
        $this->assertEquals("oxtesttable__oxtestfield", $oBase->getFieldLongName('oxtestfield'));
    }

    /**
     * Test get class name.
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
        $this->assertStringStartsWith($expectedOxid, $base->getUpdateFields());
    }

    public function testGetUpdateFieldsWithUseSkipSaveFieldsOff()
    {
        $base = new _oxBase();
        $base->init('oxactions');

        $base->oxactions__oxid = new oxField('test1', oxField::T_RAW);
        $base->oxactions__oxtitle = new oxField('title1', oxField::T_RAW);
        $base->setClassVar('_aSkipSaveFields', ['oxtitle']);

        $shopId = $this->getUpdateShopId();

        $expectedOxid = sprintf('oxid = \'test1\',oxshopid = \'%s\',oxtype = \'\',oxtitle = \'title1\'', $shopId);
        $this->assertStringStartsWith($expectedOxid, $base->getUpdateFields(false));
    }

    public function testGetUpdateFieldsWithUseSkipSaveFieldsOn()
    {
        $base = new _oxBase();
        $base->init('oxactions');

        $base->oxactions__oxid = new oxField('test1', oxField::T_RAW);
        $base->oxactions__oxtitle = new oxField('title1', oxField::T_RAW);

        $shopId = $this->getUpdateShopId();

        $base->setClassVar('_aSkipSaveFields', ['oxtitle']);
        $expectedOxid = sprintf('oxid = \'test1\',oxshopid = \'%s\',oxtype = \'\',oxtitle_1 = \'\'', $shopId);
        $this->assertStringStartsWith($expectedOxid, $base->getUpdateFields());
    }

    /**
     * Test get core table name.
     */
    public function testGetCoreTableName()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_sCoreTable", "table1");
        $this->assertEquals("table1", $oBase->getCoreTableName());
    }

    /**
     * Test get seo id.
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
     */
    public function testGetId()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_sOXID", "testId");
        $this->assertEquals("testId", $oBase->getId());
    }

    /**
     * Test set shop id with numeric value.
     */
    public function testSetShopIdNumeric()
    {
        $oBase = new _oxBase();
        $oBase->setShopId(5);
        $this->assertEquals(5, $oBase->getClassVar("_iShopId"));
    }

    /**
     * Test get shop id.
     */
    public function testGetShopId()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_iShopId", "testShopId");
        $this->assertEquals("testShopId", $oBase->getShopId());
    }

    /**
     * Test get view name.
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
     */
    public function testIsMultilang()
    {
        $oBase = new _oxBase();
        $this->assertFalse($oBase->isMultilang());
    }

    /**
     * Test set is derived.
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
     */
    public function testSetFieldData()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");

        $rs = ["oxid" => "oxtopstart", "oxtitle" => "Startseite unten"];
        foreach ($rs as $name => $value) {
            $oBase->setFieldData($name, $value);
        }

        $this->assertEquals($oBase->oxactions__oxid->value, "oxtopstart");
        $this->assertEquals($oBase->oxactions__oxtitle->value, "Startseite unten");
    }

    /**
     * Test set field data long name.
     */
    public function testSetFieldDataLongName()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");

        $rs = ["oxid" => "oxtopstart", "oxactions__oxtitle" => "Startseite unten"];
        foreach ($rs as $name => $value) {
            $oBase->setFieldData($name, $value);
        }

        $this->assertEquals($oBase->oxactions__oxid->value, "oxtopstart");
        $this->assertEquals($oBase->oxactions__oxtitle->value, "Startseite unten");
    }

    /**
     * Test set field data for non existing field with lazy loading enabled.
     */
    public function testSetFieldDataNonExistingLazyLoading()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_blUseLazyLoading", true);
        $oBase->init("oxactions");

        $rs = ["oxid" => "oxtopstart", "oxactions__oxtestval" => "Startseite unten", "oxtestval2" => "TestVal2"];
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
     */
    public function testSetFieldDataNonExistingNonLazyLoading()
    {
        $oBase = $this->getProxyClass("oxbase");
        $oBase->setNonPublicVar("_blUseLazyLoading", false);
        $oBase->setNonPublicVar("_sCoreTable", "oxactions");

        $aFieldNames = $oBase->getNonPublicVar('_aFieldNames');
        $this->assertFalse(isset($aFieldNames['oxtitle']));
        $rs = ["oxid" => "oxtopstart", "oxtitle" => "Startseite unten"];
        foreach ($rs as $name => $value) {
            $oBase->setFieldData($name, $value);
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
     * @dataProvider getFieldDataDataProvider
     */
    public function testGetFieldData($value, $type, $expected)
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");

        $oBase->oxactions__oxid = new Field($value, $type);
        $this->assertEquals($expected, $oBase->getFieldData("oxid"));
    }

    public function getFieldDataDataProvider(): array
    {
        $stringWithSpecialChars = 'special<>chars';
        return [
            ['oxstart', null, "oxstart"],
            ['oxstart', Field::T_RAW, "oxstart"],
            [$stringWithSpecialChars, null, $this->encode($stringWithSpecialChars)],
            [$stringWithSpecialChars, Field::T_RAW, $stringWithSpecialChars],
        ];
    }

    /**
     * Test get empty field data.
     */
    public function testGetFieldDataEmpty()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $this->assertNull($oBase->getFieldData("oxid"));
    }

    /**
     * Test get raw field data.
     *
     * @dataProvider getRawFieldDataDataProvider
     */
    public function testGetRawFieldData($value, $type, $expected)
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");

        $oBase->oxactions__oxid = new Field($value, $type);
        $this->assertEquals($expected, $oBase->getRawFieldData("oxid"));
    }

    public function getRawFieldDataDataProvider(): array
    {
        return [
            ['oxstart', null, "oxstart"],
            ['oxstart', Field::T_RAW, "oxstart"],
            ['special<>chars', null, "special<>chars"],
            ['special<>chars', Field::T_RAW, "special<>chars"],
        ];
    }

    /**
     * Test get empty field data.
     */
    public function testGetRawFieldDataEmpty()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $this->assertNull($oBase->getRawFieldData("oxid"));
    }

    /**
     * Test load.
     */
    public function testLoad()
    {
        $oBase = new _oxBase();
        $oBase->modifyCacheKey(null, true);
        $oBase->init("oxactions");
        $oBase->load("oxtopstart");
        $this->assertEquals($oBase->getId(), "oxtopstart");
        $this->assertTrue(property_exists($oBase, 'oxactions__oxtitle') && $oBase->oxactions__oxtitle !== null);
    }

    /**
     * Test load in language 1.
     */
    public function testLoadInLang0()
    {
        $oObj = new _oxBase();
        $oObj->init("oxarticles");
        $oObj->modifyCacheKey(null, true);

        if ((new Facts())->getEdition() === 'EE') {
            $expectedTranslation = sprintf('Champagne Pliers %s Bottle Opener PROFI', $this->encode('&'));
        } else {
            $expectedTranslation = sprintf('Champagne Pliers %s Bottle Opener', $this->encode('&'));
        }

        $oObj->load(2080);
        $this->assertEquals(2080, $oObj->getId());
        //load in 2 languages anyway
        $this->assertEquals("Barzange PROFI", $oObj->oxarticles__oxtitle->value);
        $this->assertEquals($expectedTranslation, $oObj->oxarticles__oxtitle_1->value);
    }

    /**
     * Test load with lazy loading 1.
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
     */
    public function testLoadLazyNonExistingField()
    {
        $oBase = new _oxBase();
        $oBase->enableLazyLoading();
        $oBase->init("oxarticles");
        $oBase->load("2000");

        $this->assertEquals("2000", $oBase->getId());
        $aFieldList = $oBase->getClassVar("_aFieldNames");
        $this->assertFalse(isset($aFieldList["oxnonexistingfield"]));
    }

    /**
     * Test build select string.
     */
    public function testBuildSelectString()
    {
        $oBase = oxNew('oxBase');
        $oBase->init("oxactions");

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sView = $tableViewNameGenerator->getViewName("oxactions", -1);
        $sSelect = $oBase->buildSelectString([$sView . '.oxid' => "oxtopstart"]);
        $sSelect = str_replace("  ", " ", $sSelect);

        $this->assertEquals(sprintf('select `%s`.`oxid`, `%s`.`oxshopid`, `%s`.`oxtype`, `%s`.`oxtitle`, `%s`.`oxtitle_1`, `%s`.`oxtitle_2`, `%s`.`oxtitle_3`, `%s`.`oxlongdesc`, `%s`.`oxlongdesc_1`, `%s`.`oxlongdesc_2`, `%s`.`oxlongdesc_3`, `%s`.`oxactive`, `%s`.`oxactivefrom`, `%s`.`oxactiveto`, `%s`.`oxpic`, `%s`.`oxpic_1`, `%s`.`oxpic_2`, `%s`.`oxpic_3`, `%s`.`oxlink`, `%s`.`oxlink_1`, `%s`.`oxlink_2`, `%s`.`oxlink_3`, `%s`.`oxsort`, `%s`.`oxtimestamp` from %s where 1 and %s.oxid = \'oxtopstart\'', $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView), $sSelect);
    }

    /**
     * Test build select string without shop id.
     */
    public function testBuildSelectStringWithoutShopId()
    {
        if ((new Facts())->getEdition() === 'EE') {
            $this->markTestSkipped("Test for Community and Professional editions only");
        }

        $oBase = oxNew('oxBase');
        $oBase->init("oxattribute");

        $sSelect = $oBase->buildSelectString(["oxid" => "111"]);
        $sSelect = str_replace("  ", " ", $sSelect);
        $this->assertEquals("select `oxv_oxattribute`.`oxid`, `oxv_oxattribute`.`oxshopid`, `oxv_oxattribute`.`oxtitle`, `oxv_oxattribute`.`oxtitle_1`, `oxv_oxattribute`.`oxtitle_2`, `oxv_oxattribute`.`oxtitle_3`, `oxv_oxattribute`.`oxpos`, `oxv_oxattribute`.`oxtimestamp`, `oxv_oxattribute`.`oxdisplayinbasket` from oxv_oxattribute where 1 and oxid = '111'", $sSelect);
    }

    /**
     * Test build select string without shop id.
     */
    public function testBuildSelectStringWithShopId()
    {
        if ((new Facts())->getEdition() === 'EE') {
            $this->markTestSkipped("Test for Community and Professional editions only");
        }

        $oBase = oxNew('oxBase');
        $oBase->init("oxattribute");

        $sSelect = $oBase->buildSelectString(["oxid" => "111"]);
        $sSelect = str_replace("  ", " ", $sSelect);
        $this->assertEquals("select `oxv_oxattribute`.`oxid`, `oxv_oxattribute`.`oxshopid`, `oxv_oxattribute`.`oxtitle`, `oxv_oxattribute`.`oxtitle_1`, `oxv_oxattribute`.`oxtitle_2`, `oxv_oxattribute`.`oxtitle_3`, `oxv_oxattribute`.`oxpos`, `oxv_oxattribute`.`oxtimestamp`, `oxv_oxattribute`.`oxdisplayinbasket` from oxv_oxattribute where 1 and oxid = '111'", $sSelect);
    }

    /**
     * Test build select string without where clause.
     */
    public function testBuildSelectStringWithoutWhere()
    {
        $oBase = new _oxBase();
        $oBase->init('oxuser');

        $sSelect = $oBase->buildSelectString();

        $oDB = oxDb::getDb();

        $rs = $oDB->select($sSelect);
        $expectedCount = (new Facts())->getEdition() === 'EE' ? 6 : 1;
        $this->assertEquals($expectedCount, $rs->count());
    }

    /**
     * Test select existing data.
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
     */
    public function testSelectNonExistingData()
    {
        $sSelect = "select oxactions.oxid,oxactions.oxtitle from oxactions  where oxactions.oxid = 'sss'";
        $oBase = new _oxBase();
        $this->assertFalse($oBase->assignrecord($sSelect));
    }

    /**
     * Test get select fields.
     */
    public function testGetSelectFields()
    {
        $oBase = oxNew('oxBase');
        $oBase->init('oxactions');

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sView = $tableViewNameGenerator->getViewName('oxactions', -1);
        $this->assertEquals(sprintf('`%s`.`oxid`, `%s`.`oxshopid`, `%s`.`oxtype`, `%s`.`oxtitle`, `%s`.`oxtitle_1`, `%s`.`oxtitle_2`, `%s`.`oxtitle_3`, `%s`.`oxlongdesc`, `%s`.`oxlongdesc_1`, `%s`.`oxlongdesc_2`, `%s`.`oxlongdesc_3`, `%s`.`oxactive`, `%s`.`oxactivefrom`, `%s`.`oxactiveto`, `%s`.`oxpic`, `%s`.`oxpic_1`, `%s`.`oxpic_2`, `%s`.`oxpic_3`, `%s`.`oxlink`, `%s`.`oxlink_1`, `%s`.`oxlink_2`, `%s`.`oxlink_3`, `%s`.`oxsort`, `%s`.`oxtimestamp`', $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView, $sView), $oBase->getSelectFields());
    }

    /**
     * Test get select fields without table specified.
     */
    public function testGetSelectFieldsNoFields()
    {
        $oBase = oxNew('oxBase');
        $this->assertEquals($oBase->getSelectFields(), ".`oxid`");
    }

    /**
     *  Test exists, positive.
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
     */
    public function testExistsWithId()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $this->assertTrue($oBase->exists("oxtopstart"));
    }

    /**
     * Test exists with id, negative.
     */
    public function testExistsWithIdIfNotExists()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $this->assertFalse($oBase->exists("oxstartas"));
    }

    /**
     * Test exists when not loaded.
     */
    public function testExistsNotLoaded()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $this->assertFalse($oBase->exists());
    }

    /**
     * Test delete.
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
     */
    public function testSaveIfExists()
    {
        $oBase = new _oxBase();
        $oBase = $this->getMock(\OxidEsales\Eshop\Core\Model\BaseModel::class, ['update']);
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
     */
    public function testSaveIfNew()
    {
        $oBase = $this->getMock(\OxidEsales\Eshop\Core\Model\BaseModel::class, ['insert']);
        $oBase->expects($this->any())
            ->method('insert')
            ->will($this->returnValue(true));
        $oBase->init("oxactions");
        $oBase->setId("_test");

        $sResult = $oBase->save();
        $this->assertEquals("_test", $sResult);
    }

    /**
     * Test save if can not insert.
     */
    public function testSaveIfCannotInsert()
    {
        $oBase = $this->getMock(\OxidEsales\Eshop\Core\Model\BaseModel::class, ['insert']);
        $oBase->expects($this->any())
            ->method('insert')
            ->will($this->returnValue(false));
        $oBase->init("oxactions");
        $oBase->setId("_test");

        $sResult = $oBase->save();
        $this->assertFalse($sResult);
    }

    /**
     * Test save if is derived.
     */
    public function testSaveIsDerived()
    {
        $oBase = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\_oxBase::class, ['update']);
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
        $field1 = sprintf('%s__%s', $table, $col1);
        $field2 = sprintf('%s__%s', $table, $col2);
        $val1 = 'test';
        $val2 = '2022-12-22';
        $base = new _oxBase();
        $base->init($table);
        $base->setId('_test');
        $base->$field1 = new oxField($val1, oxField::T_RAW);
        $base->$field2 = new oxField($val2, oxField::T_RAW);

        $return = $base->save();

        $res = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->select(
            sprintf('select `%s` from `%s` where `%s` = \'%s\'', $col2, $table, $col1, $val1)
        );
        $this->assertNotNull($return);
        $this->assertSame($val2, $res->fields[$col2]);
    }

    /**
     * Test update without oxid.
     */
    public function testUpdateWithoutOXID()
    {
        $oBase = new _oxBase();
        $oBase->init("oxarticles");
        try {
            $oBase->update();
        } catch (Exception $exception) {
        }

        $this->assertTrue($exception instanceof \OxidEsales\EshopCommunity\Core\Exception\ObjectException);
    }

    /**
     * Test update with oxid.
     */
    public function testUpdateWithOXID()
    {
        $myDB = oxDb::getDb();
        $shopId = $this->getUpdateShopId();
        $sInsert = sprintf('Insert into oxarticles (`OXID`,`OXSHOPID`,`OXTITLE`) values (\'_test\',\'%s\',\'test\')', $shopId);
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

        $oBase = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Core\_oxBase::class, ['getUpdateFields']);
        $oBase->expects($this->any())
            ->method('getUpdateFields')
            ->will($this->returnValue(''));

        $oBase->init("oxarticles");
        $oBase->setId("_test");

        try {
            $oBase->update();
        } catch (Exception) {
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
        $field1 = sprintf('%s__%s', $table, $col1);
        $fieldId = sprintf('%s__%s', $table, $colId);
        $fieldShopId = sprintf('%s__%s', $table, $colShopId);
        $val1 = 'test';
        $base = oxNew('oxBase');
        $base->init($table);
        $base->$field1 = new oxField($val1, oxField::T_RAW);

        $return = $base->insert();

        $count = (int) oxDb::getDb()->getOne(
            sprintf('select count(*) from `%s` where %s = \'%s\'', $table, $col1, $val1)
        );
        $this->assertSame(1, $count);
        $this->assertNotNull($return);
        $this->assertEquals($base->getId(), $base->$fieldId->value);
        $this->assertEquals($this->getShopId(), $base->$fieldShopId->value);
    }

    /**
     * Test insert with set oxid id.
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
        if ((new Facts())->getEdition() === 'EE') {
            $this->markTestSkipped("Test for Community and Professional editions only");
        }

        $oBase = oxNew('oxBase');
        $sResult = $oBase->getObjectViewName("oxarticles");
        $this->assertEquals("oxv_oxarticles", $sResult);
    }

    /**
     * Test get object view name, forcing core table usage.
     */
    public function testGetObjectViewNameForceCoreTblUsage()
    {
        if ((new Facts())->getEdition() === 'EE') {
            $this->markTestSkipped("Test for Community and Professional editions only");
        }

        $oBase = oxNew('oxBase');
        $sResult = $oBase->getObjectViewName("oxarticles", "1");
        $this->assertEquals("oxv_oxarticles", $sResult);
    }

    public function testGetObjectViewNameWithNonMultiShopTable(): void
    {
        if ((new Facts())->getEdition() === 'EE') {
            $this->markTestSkipped('Test for Community and Professional editions only');
        }

        $shopId = '1';
        $table = 'oxactions';
        $base = oxNew('oxBase');

        $sResult = $base->getObjectViewName($table, $shopId);

        $this->assertSame('oxv_' . $table, $sResult);
    }

    /**
     * Test get all fields, full.
     */
    public function testGetAllFieldsFull()
    {
        $oBase = new _oxBase();
        $oBase->init('oxactions');

        $aExpectedFields = ['oxid' => 0, 'oxshopid' => 0, 'oxtype' => 0, 'oxtitle' => 0, 'oxtitle_1' => 0, 'oxtitle_2' => 0, 'oxtitle_3' => 0, 'oxlongdesc' => 0, 'oxlongdesc_1' => 0, 'oxlongdesc_2' => 0, 'oxlongdesc_3' => 0, 'oxactive' => 0, 'oxactivefrom' => 0, 'oxactiveto' => 0, 'oxpic' => 0, 'oxpic_1' => 0, 'oxpic_2' => 0, 'oxpic_3' => 0, 'oxlink' => 0, 'oxlink_1' => 0, 'oxlink_2' => 0, 'oxlink_3' => 0, 'oxsort' => 0, 'oxtimestamp' => 0];

        $this->assertEquals($aExpectedFields, $oBase->getAllFields(true));
    }

    /**
     * A test for #1831 case
     */
    public function testGetAllFieldEmpty()
    {
        $oBase = new _oxBase();
        //should not throw any error
        $oBase->getAllFields();
    }

    /**
     * Test add field.
     */
    public function testAddField()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_sCoreTable", "oxtesttable");
        $oBase->addField('oxtestfield', 1);

        $aFieldNames = $oBase->getClassVar("_aFieldNames");

        $this->assertEquals(["oxid" => 0, "oxtestfield" => 1], $aFieldNames);
        $this->assertTrue(property_exists($oBase, 'oxtesttable__oxtestfield') && $oBase->oxtesttable__oxtestfield !== null);
    }

    /**
     * Test add field with specified length.
     */
    public function testAddFieldIfLenghtSet()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_sCoreTable", "oxtesttable");
        $oBase->addField('oxtestfield', 1, null, 20);

        $aFieldNames = $oBase->getClassVar("_aFieldNames");

        $this->assertEquals(["oxid" => 0, "oxtestfield" => 1], $aFieldNames);
        $this->assertTrue(property_exists($oBase, 'oxtesttable__oxtestfield') && $oBase->oxtesttable__oxtestfield !== null);
        $this->assertEquals(20, $oBase->oxtesttable__oxtestfield->fldmax_length);
        $this->assertFalse($oBase->getClassVar("_blIsSimplyClonable"));
    }

    /**
     * Test add field with specified type.
     */
    public function testAddFieldIfTypeSet()
    {
        $oBase = new _oxBase();
        $oBase->setClassVar("_sCoreTable", "oxtesttable");
        $oBase->addField('oxtestfield', 1, 'datetime');

        $aFieldNames = $oBase->getClassVar("_aFieldNames");

        $this->assertEquals(["oxid" => 0, "oxtestfield" => 1], $aFieldNames);
        $this->assertTrue(property_exists($oBase, 'oxtesttable__oxtestfield') && $oBase->oxtesttable__oxtestfield !== null);
        $this->assertEquals('datetime', $oBase->oxtesttable__oxtestfield->fldtype);
        $this->assertFalse($oBase->getClassVar("_blIsSimplyClonable"));
    }

    /**
     * Testinc active snippet getter.
     */
    public function testGetSqlActiveSnippet()
    {
        $iCurrTime = 1453734000; //some rounded timestamp

        $oUtilsDate = $this->getMock(\OxidEsales\Eshop\Core\UtilsDate::class, ['getRequestTime']);
        $oUtilsDate->expects($this->any())->method('getRequestTime')->will($this->returnValue($iCurrTime));
        Registry::set(\OxidEsales\Eshop\Core\UtilsDate::class, $oUtilsDate);

        $aFields = ['oxactive' => 1, 'oxactivefrom' => 1, 'oxactiveto' => 1];
        $sDate = date('Y-m-d H:i:s', $iCurrTime);

        $oBase = $this->getProxyClass('oxbase');
        $oBase->setNonPublicVar('_aFieldNames', $aFields);
        $oBase->setNonPublicVar('_sCoreTable', 'oxbase');

        $sPattern = sprintf(' (   oxbase.oxactive = 1  or  ( oxbase.oxactivefrom < \'%s\' and oxbase.oxactiveto > \'%s\' ) ) ', $sDate, $sDate);

        $this->assertEquals($sPattern, $oBase->getSqlActiveSnippet());
    }

    /**
     * Test get update field value.
     */
    public function test_getUpdateFieldValue()
    {
        $oBase = new _oxBase();
        $oBase->init("oxarticles");
        $oBase->setId('test');
        $this->assertSame("'aaa'", $oBase->getUpdateFieldValue('oxid', new oxField('aaa')));
        $this->assertSame("'aaa\\\"'", $oBase->getUpdateFieldValue('oxid', new oxField('aaa"')));
        $this->assertSame("'aaa\''", $oBase->getUpdateFieldValue('oxid', new oxField("aaa'")));

        $this->assertSame("''", $oBase->getUpdateFieldValue('oxid', new oxField(null)));
        $this->assertSame('null', $oBase->getUpdateFieldValue('oxvat', new oxField(null)));
    }

    /**
     * Test set field data with bad parameters.
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
     */
    public function testTestingLazyLoadAndMultilangFieldProblem()
    {
        $sId = oxDb::getDb()->getOne("select oxid from oxarticles where oxtitle_1 != '' and oxtitle != oxtitle_1");
        $sTitle = oxDb::getDb()->getOne(sprintf('select oxtitle_1 from oxarticles where oxid=\'%s\'', $sId));
        $oArticle = oxNew('oxArticle');
        $oArticle->loadInLang(1, $sId);

        $this->assertEquals($sTitle, $oArticle->oxarticles__oxtitle->value);
    }

    /**
     * Test non existing field is never registered.
     */
    public function testNonExistantFieldIsNeverRegistered()
    {
        $oTest = new _oxBase();
        $oTest->modifyCacheKey("nonExistantFieldTest", true);
        $oTest->enableLazyLoading();
        $this->cleanTmpDir();
        $oTest->init('oxarticles');

        //checking, should NOT be cached
        $sCacheKey = 'fieldnames_oxarticles_nonExistantFieldTest';
        $aFieldNames = oxRegistry::getUtils()->fromFileCache($sCacheKey);

        $this->assertFalse(isset($aFieldNames['nonexistantfield']));
    }

    /**
     * Test set is derived.
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
     */
    public function testSetGetReadOnly()
    {
        $oVendor = $this->getProxyClass("oxvendor");
        $oVendor->setReadOnly(true);

        $this->assertTrue($oVendor->isReadOnly());
    }

    /**
     * Test set in list.
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
     */
    public function testIsInList()
    {
        $oSubj = $this->getProxyClass('oxBase');
        $this->assertFalse($oSubj->isInList());
        $oSubj->setNonPublicVar("_blIsInList", true);
        $this->assertTrue($oSubj->isInList());
    }

    /**
     * Field names getter test
     */
    public function testGetFieldNamesOnBase()
    {
        $oBase = oxNew('oxBase');
        $this->assertEquals(["oxid"], $oBase->getFieldNames());

        $oBase->init("notExistingTable");
        $this->assertEquals(["oxid"], $oBase->getFieldNames());
    }

    /**
     * Field names getter test
     */
    public function testGetFieldNamesNoLazyLoading()
    {
        // Content model has NO lazy loading enabled.
        $oBase = oxNew('oxContent');

        $aFieldNames = $oBase->getFieldNames();

        $this->assertTrue(is_array($aFieldNames) && $aFieldNames !== []);
        $this->assertTrue(
            in_array("oxtitle", $aFieldNames),
            "oxtitle expected to be in array:  " . serialize($aFieldNames)
        );
    }

    /**
     * Field names getter test
     */
    public function testGetFieldNamesWithLazyLoading()
    {
        // Article model has lazy loading enabled.
        $oBase = oxNew('oxArticle');

        $oBase->init("oxarticles");

        $aFieldNames = $oBase->getFieldNames();

        $this->assertTrue(is_array($aFieldNames) && $aFieldNames !== []);
        $this->assertTrue(in_array("oxtitle", $aFieldNames));
    }

    /**
     * Field names getter test
     */
    public function testGetFieldNamesWithLazyLoadingOnAdmin()
    {
        $this->setAdminMode(true);

        // Article model has lazy loading enabled.
        $oBase = oxNew('oxArticle');

        $oBase->init("oxarticles");
        $oBase->setEnableMultilang(false);

        $aFieldNames = $oBase->getFieldNames();

        $this->assertTrue(is_array($aFieldNames) && $aFieldNames !== []);
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

        $this->assertFalse(property_exists($model, 'propertyName') && $model->propertyName !== null);
    }

    public function testLazyLoadingMagicIssetReturnsFalseWhenPropertyIsNull()
    {
        $model = new _oxBase();
        $model->setClassVar("_blUseLazyLoading", true);

        $model->propertyName = null;

        $this->assertFalse(property_exists($model, 'propertyName') && $model->propertyName !== null);
    }

    public function testLazyLoadingMagicIssetReturnsTrueWhenPropertyIsFalse()
    {
        $model = new _oxBase();
        $model->setClassVar("_blUseLazyLoading", true);

        $model->propertyName = false;

        $this->assertTrue(property_exists($model, 'propertyName') && $model->propertyName !== null);
    }

    public function testLazyLoadingMagicIssetReturnsTrueWhenPropertyIsEmptyString()
    {
        $model = new _oxBase();
        $model->setClassVar("_blUseLazyLoading", true);

        $model->propertyName = '';

        $this->assertTrue(property_exists($model, 'propertyName') && $model->propertyName !== null);
    }

    public function testLazyLoadingMagicIssetReturnsTrueWhenPropertyIsLoaded()
    {
        $model = new _oxBase();
        $model->setClassVar("_blUseLazyLoading", true);

        $model->propertyName = 'someValue';

        $this->assertTrue(property_exists($model, 'propertyName') && $model->propertyName !== null);
    }
}
