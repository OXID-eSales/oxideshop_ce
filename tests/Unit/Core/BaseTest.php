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

use \oxObjectException;

use Exception;
use \oxBase;
use oxBaseHelper;
use \oxUtils;
use \stdClass;
use \oxField;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

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
        //$this->_sCacheKey = (rand(0, pow(10,10)));
        parent::__construct();
        $this->_sCacheKey = null;
    }

    /**
     * Sets the names to main and view tables, loads metadata of each table.
     *
     * @param string $sTableName       Name of DB object table
     * @param bool   $blForceAllFields Forces initialisation of all fields overriding lazy loading functionality
     *
     * @return null
     */
    public function init($sTableName = null, $blForceAllFields = false)
    {
        //$this->_sCacheKey = null;
        return parent::init($sTableName, $blForceAllFields);
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
    public function setFieldData($sName, $sValue)
    {
        return parent::_setFieldData($sName, $sValue);
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
    function getObjectViewName($sTable)
    {
        return parent::_getObjectViewName($sTable);
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

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setup()
    {
        self::$count++;

        parent::setUp();

        $this->cleanUpTable('oxactions');
        $this->cleanUpTable('oxattribute');
        $this->cleanUpTable('oxarticles');
        $this->cleanUpTable('oxcategories');
        $this->cleanUpTable('oxdiscount');
        $this->cleanUpTable('oxnews');
        $this->cleanUpTable('oxorder');

        $this->getConfig();
        $this->getSession();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxactions');
        $this->cleanUpTable('oxattribute');
        $this->cleanUpTable('oxarticles');
        $this->cleanUpTable('oxcategories');
        $this->cleanUpTable('oxdiscount');
        $this->cleanUpTable('oxnews');
        $this->cleanUpTable('oxorder');

        //clean it
        oxDB::getDb()->execute('delete from oxactions where oxtitle like "test%"');
        oxDB::getDb()->execute('delete from oxnews where oxshortdesc like "oxbasetest%"');

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
        $oBase = $this->getMock("oxbase", array("_addField", "buildSelectString", "assignRecord", "getViewName"));
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
        $oConfig = $this->getMock('oxconfig', array('getShopId'));
        $oConfig->expects($this->any())->method('getShopId')->will($this->returnValue(null));

        $oBase = $this->getMock('oxbase', array('getConfig', 'getShopId'), array(), '', false);
        $oBase->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
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

        $oConfig = $this->getMock('oxconfig', array('getShopId'));
        $oConfig->expects($this->any())->method('getShopId')->will($this->returnValue('xxx'));

        $oBase = $this->getMock('oxbase', array('getConfig', 'getShopId'), array(), '', false);
        $oBase->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
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

        $oConfig = $this->getMock('oxconfig', array('getShopId'));
        $oConfig->expects($this->any())->method('getShopId')->will($this->returnValue('xxx'));

        $oBase = $this->getMock('oxbase', array('getConfig', 'getShopId'), array(), '', false);
        $oBase->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
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
        $oBase = $this->getMock('oxbase', array('isDerived'));
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
        $oBase = $this->getMock('oxbase', array('isDerived'));
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
        $oBase = $this->getMock('oxbase', array('_initDataStructure', 'isAdmin', 'exists', 'isDerived', '_update', '_insert', 'onChange', 'getId'));
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
        $this->assertFalse(isset($oBase->blIsDerived));
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
        $this->assertFalse(isset($oBase->sOXID));
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
        $oBase->getConfig()->setConfigParam('iDebug', -1);
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
        $oBase->getConfig()->setConfigParam('iDebug', 0);
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

        $this->assertEquals(array('oxid' => 0), $oBase->getClassVar("_aFieldNames"));
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

        $this->assertEquals(array('oxid' => 0), $oBase->getClassVar("_aFieldNames"));

        //making sure 2 fields are used
        $sVal = $oBase->oxarticles__oxtitle->value;
        $sVal = $oBase->oxarticles__oxshortdesc->value;

        //testing initial load
        $aFieldNames = array("oxid" => 0, "oxtitle" => 0, "oxshortdesc" => 0);
        $this->assertEquals($aFieldNames, $oBase->getClassVar("_aFieldNames"));

        $oBase = new _oxBase();
        $oBase->setClassVar("_sCoreTable", "oxarticles");
        $oBase->setClassVar("_blUseLazyLoading", true);
        $oBase->modifyCacheKey("lazyloadingtest", true);
        $oBase->init();
        $oBase->load(2000);

        //test final load
        $aFieldNames = array("oxid" => 0);
        $this->assertEquals($aFieldNames, $oBase->getClassVar("_aFieldNames"));
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

        $this->assertEquals(array('oxid' => 0), $oBase->getClassVar("_aFieldNames"));

        //making sure 2 fields are used
        $sVal = $oBase->oxarticles__oxtitle->value;
        $sVal = $oBase->oxarticles__oxshortdesc->value;

        //testing initial load
        $aFieldNames = array("oxid" => 0, "oxtitle" => 0, "oxshortdesc" => 0);
        $this->assertEquals($aFieldNames, $oBase->getClassVar("_aFieldNames"));

        oxBaseHelper::cleanup();
        $oBase = new _oxBase();
        $oBase->setClassVar("_sCoreTable", "oxarticles");
        $oBase->setClassVar("_blUseLazyLoading", true);
        $oBase->modifyCacheKey("lazyloadingtest1", true);
        $oBase->init();
        $oBase->load(2000);
        //test final load
        $this->assertEquals($aFieldNames, $oBase->getClassVar("_aFieldNames"));
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
        $this->assertFalse(isset($oBase->oxarticles__oxtitle));
        $aFieldNames = $oBase->getClassVar("_aFieldNames");
        $this->assertFalse(isset($aFieldNames['oxtitle']));;
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

    /**
     * Test get update fields.
     *
     * @return null
     */
    public function testGetUpdateFields()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $oBase->oxactions__oxid = new oxField("test1", oxField::T_RAW);
        $oBase->oxactions__oxtitle = new oxField("title1", oxField::T_RAW);

        $shopId = $this->getUpdateShopId();
        $sGetUpdateFields = "oxid = 'test1',oxshopid = '{$shopId}',oxtype = '',oxtitle = 'title1',oxtitle_1 = '',oxtitle_2 = '',oxtitle_3 = '',oxlongdesc = '',oxlongdesc_1 = '',oxlongdesc_2 = '',oxlongdesc_3 = '',oxactive = '1',oxactivefrom = '0000-00-00 00:00:00',oxactiveto = '0000-00-00 00:00:00',oxpic = '',oxpic_1 = '',oxpic_2 = '',oxpic_3 = '',oxlink = '',oxlink_1 = '',oxlink_2 = '',oxlink_3 = '',oxsort = '0'";
        $this->assertEquals($sGetUpdateFields, $oBase->UNITgetUpdateFields());
    }

    /**
     * Test get update fields with use skip save fields of.
     *
     * @return null
     */
    public function testGetUpdateFieldsWithUseSkipSaveFieldsOff()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $oBase->oxactions__oxid = new oxField("test1", oxField::T_RAW);
        $oBase->oxactions__oxtitle = new oxField("title1", oxField::T_RAW);

        $oBase->setClassVar('_aSkipSaveFields', array('oxtitle'));

        $shopId = $this->getUpdateShopId();
        $sGetUpdateFields = "oxid = 'test1',oxshopid = '{$shopId}',oxtype = '',oxtitle = 'title1',oxtitle_1 = '',oxtitle_2 = '',oxtitle_3 = '',oxlongdesc = '',oxlongdesc_1 = '',oxlongdesc_2 = '',oxlongdesc_3 = '',oxactive = '1',oxactivefrom = '0000-00-00 00:00:00',oxactiveto = '0000-00-00 00:00:00',oxpic = '',oxpic_1 = '',oxpic_2 = '',oxpic_3 = '',oxlink = '',oxlink_1 = '',oxlink_2 = '',oxlink_3 = '',oxsort = '0',oxtimestamp = 'CURRENT_TIMESTAMP'";
        $this->assertEquals($sGetUpdateFields, $oBase->UNITgetUpdateFields(false));
    }

    /**
     * Test get update fields with use skip save fields on.
     *
     * @return null
     */
    public function testGetUpdateFieldsWithUseSkipSaveFieldsOn()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $oBase->oxactions__oxid = new oxField("test1", oxField::T_RAW);
        $oBase->oxactions__oxtitle = new oxField("title1", oxField::T_RAW);

        $oBase->setClassVar('_aSkipSaveFields', array('oxtitle'));

        $shopId = $this->getUpdateShopId();
        $sGetUpdateFields = "oxid = 'test1',oxshopid = '{$shopId}',oxtype = '',oxtitle_1 = '',oxtitle_2 = '',oxtitle_3 = '',oxlongdesc = '',oxlongdesc_1 = '',oxlongdesc_2 = '',oxlongdesc_3 = '',oxactive = '1',oxactivefrom = '0000-00-00 00:00:00',oxactiveto = '0000-00-00 00:00:00',oxpic = '',oxpic_1 = '',oxpic_2 = '',oxpic_3 = '',oxlink = '',oxlink_1 = '',oxlink_2 = '',oxlink_3 = '',oxsort = '0',oxtimestamp = 'CURRENT_TIMESTAMP'";

        $this->assertEquals($sGetUpdateFields, $oBase->UNITgetUpdateFields());
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
        $this->assertFalse(isset($oBase->oxactions__oxtitle));

        $oBase->DisableLazyLoading();
        $this->assertFalse($oBase->getClassVar("_blUseLazyLoading"));
        $this->assertTrue(isset($oBase->oxactions__oxtitle));
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
        $select = "select * from oxactions where oxid = 'oxstart'";
        $oDB = oxDb::getDB(oxDB::FETCH_MODE_ASSOC);
        $rs = $oDB->execute($select);
        $oBase->assign($rs->fields);
        $this->assertEquals("oxstart", $oBase->getId());
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
        $oBase->oxactions__oxid = new oxField("oxstart", oxField::T_RAW);
        $select = "select * from oxactions where oxid = 'oxstart'";
        $rs = $oDB->Execute($select);
        $oBase->assign($rs->fields);
        $this->assertEquals($oBase->getId(), "oxstart");
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
        $oBase->oxactions__oxid = new oxField("oxstart", oxField::T_RAW);
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
        $rs = array("oxid" => "oxstart", "oxtitle" => "Startseite unten");
        while (list($name, $value) = each($rs)) {
            $oBase->setFieldData($name, $value);
        }
        $this->assertEquals($oBase->oxactions__oxid->value, "oxstart");
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
        $rs = array("oxid" => "oxstart", "oxactions__oxtitle" => "Startseite unten");
        while (list($name, $value) = each($rs)) {
            $oBase->setFieldData($name, $value);
        }
        $this->assertEquals($oBase->oxactions__oxid->value, "oxstart");
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
        $rs = array("oxid" => "oxstart", "oxactions__oxtestval" => "Startseite unten", "oxtestval2" => "TestVal2");
        while (list($name, $value) = each($rs)) {
            $oBase->setFieldData($name, $value);
        }
        //standard field
        $this->assertEquals($oBase->oxactions__oxid->value, "oxstart");
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
        $rs = array("oxid" => "oxstart", "oxtitle" => "Startseite unten");
        while (list($name, $value) = each($rs)) {
            $oBase->UNITsetFieldData($name, $value);
        }
        //standard field
        $this->assertEquals($oBase->oxactions__oxid->value, "oxstart");
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
        $oBase->oxactions__oxid = new oxField("oxstart", oxField::T_RAW);
        $this->assertEquals("oxstart", $oBase->getFieldData("oxid"));
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
        $oBase->load("oxstart");
        $this->assertEquals($oBase->getId(), "oxstart");
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
        $oBase->load("oxstart");
        $this->assertEquals("oxstart", $oBase->getId());
        $this->assertFalse(isset($oBase->oxactions__oxtitle));
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
        $this->assertFalse(isset($oBase->oxarticles__oxprice));
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
        $sSelect = $oBase->buildSelectString(array("$sView.oxid" => "oxstart"));
        $sSelect = str_replace("  ", " ", $sSelect);

        $this->assertEquals("select `$sView`.`oxid`, `$sView`.`oxshopid`, `$sView`.`oxtype`, `$sView`.`oxtitle`, `$sView`.`oxtitle_1`, `$sView`.`oxtitle_2`, `$sView`.`oxtitle_3`, `$sView`.`oxlongdesc`, `$sView`.`oxlongdesc_1`, `$sView`.`oxlongdesc_2`, `$sView`.`oxlongdesc_3`, `$sView`.`oxactive`, `$sView`.`oxactivefrom`, `$sView`.`oxactiveto`, `$sView`.`oxpic`, `$sView`.`oxpic_1`, `$sView`.`oxpic_2`, `$sView`.`oxpic_3`, `$sView`.`oxlink`, `$sView`.`oxlink_1`, `$sView`.`oxlink_2`, `$sView`.`oxlink_3`, `$sView`.`oxsort`, `$sView`.`oxtimestamp` from $sView where 1 and $sView.oxid = 'oxstart'", $sSelect);
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
    public function  testBuildSelectStringWithShopId()
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
    public function  testBuildSelectStringWithoutWhere()
    {
        $oBase = new _oxBase();
        $oBase->init('oxuser');
        $sSelect = $oBase->buildSelectString();

        $oDB = oxDb::getDb();

        $rs = $oDB->Execute($sSelect);
        $expectedCount = $this->getConfig()->getEdition() === 'EE' ? 6 : 1;
        $this->assertEquals($expectedCount, $rs->RecordCount());
    }

    /**
     * Test select existing data.
     *
     * @return null
     */
    public function  testSelectExistingData()
    {
        $sSelect = "select oxactions.oxid, oxactions.oxtitle from oxactions  where oxactions.oxid = 'oxstart'";
        $oBase = new _oxBase();
        $oBase->init('oxactions');
        $this->assertEquals($oBase->assignRecord($sSelect), true);
        $this->assertEquals($oBase->oxactions__oxid->value, 'oxstart');
    }

    /**
     * Test select non existing data.
     *
     * @return null
     */
    public function  testSelectNonExistingData()
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
    public function  testGetSelectFields()
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
    public function  testGetSelectFieldsNoFields()
    {
        $oBase = oxNew('oxBase');
        $this->assertEquals($oBase->getSelectFields(), ".`oxid`");
    }

    /**
     *  Test exists, positive.
     *
     * @return null
     */
    public function  testExists()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $oBase->setId("oxstart");
        $this->assertTrue($oBase->exists());
    }

    /**
     * Test exists, negative.
     *
     * @return null
     */
    public function  testExistsNot()
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
    public function  testExistsWithId()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $this->assertTrue($oBase->exists("oxstart"));
    }

    /**
     * Test exists with id, negative.
     *
     * @return null
     */
    public function  testExistsWithIdIfNotExists()
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
    public function  testExistsNotLoaded()
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
    public function  testDelete()
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
    public function  testDeleteIsDerived()
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
    public function  testDeleteWithSetOxid()
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
    public function  testDeleteWithoutOxid()
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
    public function  testDeleteWithNonExistingOxid()
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
    public function  testSaveIfExists()
    {
        $oBase = new _oxBase();
        $oBase = $this->getMock('oxbase', array('update'));
        $oBase->expects($this->any())
            ->method('update')
            ->will($this->returnValue(true));
        $oBase->init('oxactions');
        $oBase->setId('oxstart');
        $sResult = $oBase->save();
        $this->assertEquals('oxstart', $sResult);
    }

    /**
     * Test save if fields not set.
     *
     * @return null
     */
    public function  testSaveIfFieldsNotSet()
    {
        $oBase = new _oxBase();
        $oBase->init("oxactions");
        $oBase->setId("oxstart");
        $oBase->setClassVar("_aFieldNames", null);
        $this->assertFalse($oBase->save());
    }

    /**
     * Test save if new.
     *
     * @return null
     */
    public function  testSaveIfNew()
    {
        $oBase = $this->getMock('oxbase', array('_insert'));
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
    public function  testSaveIfCannotInsert()
    {
        $oBase = $this->getMock('oxbase', array('_insert'));
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
    public function  testSaveIsDerived()
    {
        $oBase = $this->getMock('Unit\Core\_oxBase', array('update'));
        $oBase->expects($this->any())
            ->method('update')
            ->will($this->returnValue(true));
        $oBase->init("oxactions");
        $oBase->setId("oxstart");
        $oBase->setIsDerived(true);
        $sResult = $oBase->save();
        $this->assertFalse($sResult);
    }

    /**
     * Test save if timestamp updated.
     *
     * @return null
     */
    public function  testSaveIfExistsInAdminTimeStamp()
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
        $res = $myDB->Execute("select oxupdate from oxuserbaskets where oxid='_test'");
        $this->assertNotEquals("2007-07-07 00:00:00", $res->fields['oxupdate']);
    }

    /**
     * Test save if new in admin mode with date time.
     *
     * @return null
     */
    public function  testSaveIfNewInAdminDateTime()
    {
        //$this->getConfig()->blAdmin = true;
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

    /**
     * Test save if new in admin mode with date.
     *
     * @return null
     */
    public function  testSaveIfNewInAdminDate()
    {
        $myDB = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
        //$this->getConfig()->blAdmin = true;
        $oBase = new _oxBase();
        $oBase->init('oxnews');
        $oBase->setId('_test');
        $oBase->oxnews__oxshortdesc = new oxField("oxbasetest", oxField::T_RAW);
        $oBase->oxnews__oxdate = new oxField("2007.07.07", oxField::T_RAW);
        $sResult = $oBase->save();
        //$this->getConfig()->blAdmin = false;
        $this->assertNotNull($sResult);
        $myDB = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
        $res = $myDB->Execute("select oxdate from oxnews where oxshortdesc='oxbasetest'");
        $this->assertEquals($res->fields['oxdate'], "2007-07-07");
    }

    /**
     * Test update without oxid.
     *
     * @return null
     */
    public function  testUpdateWithoutOXID()
    {
        $oBase = new _oxBase();
        $oBase->init("oxarticles");
        try {
            $oBase->update();
        } catch (Exception $e) {
        }

        $this->assertTrue($e instanceof oxObjectException);
    }

    /**
     * Test update with oxid.
     *
     * @return null
     */
    public function  testUpdateWithOXID()
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
        $res = $myDB->Execute("select oxtitle from oxarticles where oxid='_test'");
        $this->assertEquals($res->fields['oxtitle'], "changed title");
    }

    /**
     * Test update with oxid and is derived.
     *
     * @return null
     */
    public function  testUpdateWithOXIDIsDerived()
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
    public function  testUpdateWithOXIDWrongSql()
    {
        $myDB = oxDb::getDb();
        $sInsert = "Insert into oxarticles (`OXID`,`OXTITLE`) values ('_test','test')";
        $myDB->Execute($sInsert);

        $oBase = $this->getMock('Unit\Core\_oxBase', array('_getUpdateFields'));
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

    /**
     * Test insert with shop id.
     *
     * @return null
     */
    public function  testInsertWithShopId()
    {
        $myDB = oxDb::getDb();
        $oBase = oxNew('oxBase');
        $oBase->init('oxnews');
        $oBase->oxnews__oxshortdesc = new oxField("oxbasetest", oxField::T_RAW);
        $sResult = $oBase->UNITinsert();
        $this->assertEquals(1, (int) $myDB->getOne('select count(*) from oxnews where oxshortdesc = "oxbasetest"'));
        $this->assertNotNull($sResult);
        $this->assertEquals($oBase->getId(), $oBase->oxnews__oxid->value);
        $this->assertEquals($this->getShopId(), $oBase->oxnews__oxshopid->value);
    }

    /**
     * Test insert with set oxid id.
     *
     * @return null
     */
    public function  testInsertWithSetOxid()
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
    public function  testInsertWithoutOxid()
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
    public function  testGetObjectViewName()
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
    public function  testGetObjectViewNameForceCoreTblUsage()
    {
        if ($this->getConfig()->getEdition() === 'EE') {
            $this->markTestSkipped("Test for Community and Professional editions only");
        }

        $oBase = oxNew('oxBase');
        $sResult = $oBase->UNITgetObjectViewName("oxarticles", "1");
        $this->assertEquals("oxv_oxarticles", $sResult);
    }

    /**
     * Test get object view name for multishop table.
     */
    public function  testGetObjectViewNameNotMullShopTable()
    {
        if ($this->getConfig()->getEdition() === 'EE') {
            $this->markTestSkipped("Test for Community and Professional editions only");
        }

        $oBase = oxNew('oxBase');
        $sResult = $oBase->UNITgetObjectViewName("oxnews", "1");
        $this->assertEquals("oxv_oxnews", $sResult);
    }

    /**
     * Test get all fields.
     *
     * @return null
     */
    public function  testGetAllFields()
    {
        oxTestModules::addFunction('oxUtils', 'fromFileCache', '{return false;}');
        oxTestModules::addFunction('oxUtils', 'fromStaticCache', '{return false;}');
        $oBase = $this->getMock('Unit\Core\_oxBase', array('isAdmin'));
        $oBase->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oBase->init('oxactions');

        $oField1 = new stdClass();
        $oField1->name = 'OXID';
        $oField1->max_length = '32';
        $oField1->type = 'char';
        $oField1->scale = null;
        $oField1->not_null = true;
        $oField1->primary_key = true;
        $oField1->auto_increment = false;
        $oField1->binary = false;
        $oField1->unsigned = false;
        $oField1->has_default = false;
        $oField1->comment = 'Action id';
        $oField1->characterSet = 'latin1';
        $oField1->collation = 'latin1_general_ci';

        $oField2 = new stdClass();
        $oField2->name = 'OXSHOPID';
        $oField2->max_length = '11';
        $oField2->type = 'int';
        $oField2->scale = null;
        $oField2->not_null = true;
        $oField2->primary_key = false;
        $oField2->auto_increment = false;
        $oField2->binary = false;
        $oField2->unsigned = false;
        $oField2->has_default = true;
        $oField2->default_value = 1;

        $oField3 = new stdClass();
        $oField3->name = 'OXTYPE';
        $oField3->max_length = '1';
        $oField3->type = 'tinyint';
        $oField3->scale = null;
        $oField3->not_null = true;
        $oField3->primary_key = false;
        $oField3->auto_increment = false;
        $oField3->binary = false;
        $oField3->unsigned = false;
        $oField3->has_default = false;
        $oField3->comment = 'Action type: 0 or 1 - action, 2 - promotion, 3 - banner';
        $oField3->characterSet = null;
        $oField3->collation = '';

        $oField4 = new stdClass();
        $oField4->name = 'OXTITLE';
        $oField4->max_length = '128';
        $oField4->type = 'char';
        $oField4->scale = null;
        $oField4->not_null = true;
        $oField4->primary_key = false;
        $oField4->auto_increment = false;
        $oField4->binary = false;
        $oField4->unsigned = false;
        $oField4->has_default = false;
        $oField4->comment = 'Title (multilanguage)';
        $oField4->characterSet = 'utf8';
        $oField4->collation = 'utf8_general_ci';

        $oField41 = clone $oField4;
        $oField41->name = 'OXTITLE_1';
        $oField41->comment = '';

        $oField42 = clone $oField4;
        $oField42->name = 'OXTITLE_2';
        $oField42->comment = '';

        $oField43 = clone $oField4;
        $oField43->name = 'OXTITLE_3';
        $oField43->comment = '';

        $oField5 = new stdClass();
        $oField5->name = 'OXLONGDESC';
        $oField5->max_length = '10';
        $oField5->type = 'text';
        $oField5->scale = null;
        $oField5->not_null = true;
        $oField5->primary_key = false;
        $oField5->auto_increment = false;
        $oField5->binary = false;
        $oField5->unsigned = false;
        $oField5->has_default = false;
        $oField5->comment = 'Long description, used for promotion (multilanguage)';
        $oField5->characterSet = 'utf8';
        $oField5->collation = 'utf8_general_ci';

        $oField51 = clone $oField5;
        $oField51->name = 'OXLONGDESC_1';
        $oField51->comment = '';

        $oField52 = clone $oField5;
        $oField52->name = 'OXLONGDESC_2';
        $oField52->comment = '';

        $oField53 = clone $oField5;
        $oField53->name = 'OXLONGDESC_3';
        $oField53->comment = '';

        $oField6 = new stdClass();
        $oField6->name = 'OXACTIVE';
        $oField6->max_length = '1';
        $oField6->type = 'tinyint';
        $oField6->scale = null;
        $oField6->not_null = true;
        $oField6->primary_key = null;
        $oField6->auto_increment = false;
        $oField6->binary = false;
        $oField6->unsigned = false;
        $oField6->has_default = true;
        $oField6->default_value = '1';
        $oField6->comment = 'Active';
        $oField6->characterSet = null;
        $oField6->collation = null;

        $oField7 = new stdClass();
        $oField7->name = 'OXACTIVEFROM';
        $oField7->max_length = 20;
        $oField7->type = 'datetime';
        $oField7->scale = null;
        $oField7->not_null = true;
        $oField7->primary_key = false;
        $oField7->auto_increment = false;
        $oField7->binary = false;
        $oField7->unsigned = false;
        $oField7->has_default = true;
        $oField7->default_value = '0000-00-00 00:00:00';
        $oField7->comment = 'Active from specified date';
        $oField7->characterSet = null;
        $oField7->collation = null;

        $oField8 = new stdClass();
        $oField8->name = 'OXACTIVETO';
        $oField8->max_length = 20;
        $oField8->type = 'datetime';
        $oField8->scale = null;
        $oField8->not_null = true;
        $oField8->primary_key = false;
        $oField8->auto_increment = false;
        $oField8->binary = false;
        $oField8->unsigned = false;
        $oField8->has_default = true;
        $oField8->default_value = '0000-00-00 00:00:00';
        $oField8->comment = 'Active to specified date';
        $oField8->characterSet = null;
        $oField8->collation = null;

        $oField9 = new stdClass();
        $oField9->name = 'OXPIC';
        $oField9->max_length = '128';
        $oField9->type = 'varchar';
        $oField9->scale = null;
        $oField9->not_null = true;
        $oField9->primary_key = false;
        $oField9->auto_increment = false;
        $oField9->binary = false;
        $oField9->unsigned = false;
        $oField9->has_default = false;
        $oField9->comment = 'Picture filename, used for banner (multilanguage)';
        $oField9->characterSet = 'utf8';
        $oField9->collation = 'utf8_general_ci';

        $oField91 = clone $oField9;
        $oField91->name = 'OXPIC_1';
        $oField91->comment = '';

        $oField92 = clone $oField9;
        $oField92->name = 'OXPIC_2';
        $oField92->comment = '';

        $oField93 = clone $oField9;
        $oField93->name = 'OXPIC_3';
        $oField93->comment = '';

        $oField10 = new stdClass();
        $oField10->name = 'OXLINK';
        $oField10->max_length = '128';
        $oField10->type = 'varchar';
        $oField10->scale = null;
        $oField10->not_null = true;
        $oField10->primary_key = false;
        $oField10->auto_increment = false;
        $oField10->binary = false;
        $oField10->unsigned = false;
        $oField10->has_default = false;
        $oField10->comment = 'Link, used on banner (multilanguage)';
        $oField10->characterSet = 'utf8';
        $oField10->collation = 'utf8_general_ci';

        $oField101 = clone $oField10;
        $oField101->name = 'OXLINK_1';
        $oField101->comment = '';

        $oField102 = clone $oField10;
        $oField102->name = 'OXLINK_2';
        $oField102->comment = '';

        $oField103 = clone $oField10;
        $oField103->name = 'OXLINK_3';
        $oField103->comment = '';

        $oField11 = new stdClass();
        $oField11->name = 'OXSORT';
        $oField11->max_length = '5';
        $oField11->type = 'int';
        $oField11->scale = null;
        $oField11->not_null = true;
        $oField11->primary_key = false;
        $oField11->auto_increment = false;
        $oField11->binary = false;
        $oField11->unsigned = false;
        $oField11->has_default = true;
        $oField11->default_value = '0';
        $oField11->comment = 'Sorting';
        $oField11->characterSet = null;
        $oField11->collation = null;

        $oField12 = new stdClass();
        $oField12->name = 'OXTIMESTAMP';
        $oField12->max_length = '10';
        $oField12->type = 'timestamp';
        $oField12->scale = null;
        $oField12->not_null = true;
        $oField12->primary_key = false;
        $oField12->auto_increment = false;
        $oField12->binary = false;
        $oField12->unsigned = false;
        $oField12->has_default = true;
        $oField12->default_value = 'CURRENT_TIMESTAMP';
        $oField12->comment = 'Timestamp';
        $oField12->characterSet = null;
        $oField12->collation = null;

        $expectedFields = array(
            $oField1,
            $oField2,
            $oField3,
            $oField4,
            $oField41,
            $oField42,
            $oField43,
            $oField5,
            $oField51,
            $oField52,
            $oField53,
            $oField6,
            $oField7,
            $oField8,
            $oField9,
            $oField91,
            $oField92,
            $oField93,
            $oField10,
            $oField101,
            $oField102,
            $oField103,
            $oField11,
            $oField12
        );

        $this->assertEquals($expectedFields, $oBase->UNITgetAllFields());
    }

    /**
     * Test get all fields, full.
     *
     * @return null
     */
    public function  testGetAllFieldsFull()
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
    public function  testAddField()
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
    public function  testAddFieldIfLenghtSet()
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
    public function  testAddFieldIfTypeSet()
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
    public function  testGetSqlActiveSnippet()
    {
        $iCurrTime = 1453734000; //some rounded timestamp

        $oUtilsDate = $this->getMock('oxUtilsDate', array('getRequestTime'));
        $oUtilsDate->expects($this->any())->method('getRequestTime')->will($this->returnValue($iCurrTime));
        /** @var oxUtilsDate $oUtils */
        oxRegistry::set('oxUtilsDate', $oUtilsDate);

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
    public function testGetFieldNames()
    {
        $oBase = oxNew('oxBase');
        $this->assertEquals(array("oxid"), $oBase->getFieldNames());

        $oBase->init("oxarticles");
        $aFieldNames = $oBase->getFieldNames();

        $this->assertTrue(is_array($aFieldNames) && count($aFieldNames) > 0);
        $this->assertTrue(in_array("oxtitle", $aFieldNames));
    }
}
