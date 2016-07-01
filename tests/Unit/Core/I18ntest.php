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

use modDB;
use oxDb;
use oxField;
use OxidEsales\Eshop\Core\Database;
use oxRegistry;
use oxTestModules;

//require_once 'oxbaseTest.php';

class _oxI18n extends \oxI18n
{

    public function getClassVar($sName)
    {
        return $this->$sName;
    }

    public function setClassVar($sName, $sVal)
    {
        return $this->$sName = $sVal;
    }

    public function enableLazyLoading()
    {
        $this->_blUseLazyLoading = true;
    }

}

class I18ntest extends \OxidTestCase
{
    protected function setUp()
    {
        if ($this->getName() == "testMultilangObjectDeletion") {
            $this->_insertTestLanguage();
        }

        parent::setUp();
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        if ($this->getName() == 'testUpdateAndSeoIsOn') {
            $oDB = oxDb::getDb();
            $oDB->execute("delete from oxseo where oxtype != 'static'");
            $oDB->execute("delete from oxarticles where oxid='testa'");
            $oDB->execute("delete from oxartextends where oxid='testa'");
        }

        if ($this->getName() == "testMultilangObjectDeletion") {
            $this->_deleteTestLanguage();
        }

        parent::tearDown();
        modDB::getInstance()->cleanup();
    }

    protected function getSqlShopId()
    {
        $shopId = $this->getConfig()->getEdition() === 'EE' ? '1' : '';
        return $shopId;
    }

    public function testUpdateAndSeoIsOn()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        $oArticle = oxNew('oxArticle');
        $oArticle->setId('testa');
        $oArticle->save();
        $oArticle->getLink();

        $oArticle = oxNew('oxArticle');
        $oArticle->setAdminMode(true);
        $oArticle->load('testa');
        $oArticle->oxarticles__oxtitle = new oxField('new title');
        $oArticle->save();

        $this->assertTrue('1' == oxDb::getDb()->getOne('select oxexpired from oxseo where oxobjectid = "testa"'));
    }

    public function testUpdateAndSeoIsOnMock()
    {

        $oSeo = $this->getMock('oxseoencoder', array('markAsExpired'));
        $oSeo->expects($this->once())->method('markAsExpired')->with(
            $this->equalTo('testa'),
            $this->equalTo(null),
            $this->equalTo(1),
            $this->equalTo(0)
        )->will($this->returnValue(null));
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        $oArticle = oxNew('oxArticle');
        $oArticle->setId('testa');
        $oArticle->save();
        $oArticle->getLink();

        oxTestModules::addModuleObject('oxSeoEncoder', $oSeo);

        $oArticle = oxNew('oxArticle');
        $oArticle->setAdminMode(true);
        $oArticle->load('testa');
        $oArticle->oxarticles__oxtitle = new oxField('new title');
        $oArticle->save();
    }

    public function testSetLanguage()
    {
        $oObj = new _oxI18n();
        $oObj->setLanguage(0); // defaults to 0 in demodata
        $this->assertEquals(0, $oObj->getClassVar("_iLanguage"));
        $oObj->setLanguage(1);
        $this->assertEquals(1, $oObj->getClassVar("_iLanguage"));
    }

    public function testSetEnableMultilang()
    {
        $oObj = new _oxI18n();
        $oObj->setEnableMultilang(false);
        $this->assertFalse($oObj->getClassVar("_blEmployMultilanguage"));
        $oObj->setEnableMultilang(true);
        $this->assertTrue($oObj->getClassVar("_blEmployMultilanguage"));
    }

    public function testSetEnableMultiLangReloadsAFieldNames()
    {
        $oi18 = new _oxI18n();
        $oi18->init("oxartextends");
        $this->assertEquals(array('oxid' => 0, 'oxlongdesc' => 1,'oxtimestamp' => 0), $oi18->getClassVar('_aFieldNames'));

        $oi18 = new _oxI18n();
        $oi18->init("oxartextends");
        $oi18->setEnableMultilang(false);
        $this->assertEquals(array('oxid' => 0, 'oxlongdesc' => 0, 'oxlongdesc_1' => 0, 'oxlongdesc_2' => 0, 'oxlongdesc_3' => 0, 'oxtimestamp' => 0), $oi18->getClassVar('_aFieldNames'));
    }

    public function testSetEnableMultilanguageCacheTest()
    {
        $oI18n = $this->getMock('oxI18n', array('modifyCacheKey'));
        $oI18n->expects($this->once())->method('modifyCacheKey')->with("_nonml");
        $oI18n->setEnableMultilang(false);
    }

    public function testIsMultilingualField()
    {
        $oObj = new _oxI18n();
        $oObj->init("oxarticles");

        $this->assertTrue($oObj->IsMultilingualField('oxtitle'));
        $this->assertTrue($oObj->IsMultilingualField('oxvarselect'));
        $this->assertFalse($oObj->IsMultilingualField('oxid'));
        $this->assertFalse($oObj->IsMultilingualField('non existing'));
        $this->assertFalse($oObj->IsMultilingualField('oxtime'));
    }

    public function testIsMultilingualFieldLazyLoad()
    {
        $this->cleanTmpDir();
        $oObj = new _oxI18n();
        $oObj->enableLazyLoading();
        $oObj->init("oxarticles");

        $this->assertTrue($oObj->IsMultilingualField('oxtitle'));
    }

    public function testLoadInLang0()
    {
        $oObj = new _oxI18n();
        $oObj->init("oxarticles");
        $oObj->loadInLang(0, 1127);

        $this->assertEquals("Blinkende Eiswürfel FLASH", $oObj->oxarticles__oxtitle->value);
        $this->assertEquals(1127, $oObj->getId());
        $this->assertFalse(isset($oObj->oxarticles__oxtitle_1->value));
    }

    public function testLoadInLang1()
    {
        $oObj = new _oxI18n();
        $oObj->init("oxarticles");
        $oObj->loadInLang(1, 1127);
        $this->assertEquals("Ice Cubes FLASH", $oObj->oxarticles__oxtitle->value);
        $this->assertEquals(1127, $oObj->getId());
        $this->assertFalse(isset($oObj->oxarticles__oxtitle_1->value));
    }

    public function testLoadInLang0DisableMultilang()
    {
        $oObj = new _oxI18n();
        $oObj->setEnableMultilang(false);
        $oObj->init("oxarticles");
        $oObj->loadInLang(0, 1127);
        $this->assertEquals(1127, $oObj->getId());
        $this->assertEquals("Blinkende Eiswürfel FLASH", $oObj->oxarticles__oxtitle->value);
        $this->assertEquals("Ice Cubes FLASH", $oObj->oxarticles__oxtitle_1->value);
    }

    public function testLoadInLang1DisableMultilang()
    {
        $oObj = new _oxI18n();
        $oObj->setEnableMultilang(false);
        $oObj->init("oxarticles");
        $oObj->loadInLang(1, 1127);
        $this->assertEquals("Blinkende Eiswürfel FLASH", $oObj->oxarticles__oxtitle->value);
        $this->assertEquals("Ice Cubes FLASH", $oObj->oxarticles__oxtitle_1->value);
    }


    public function testLazyLoadInLang0()
    {
        $this->cleanTmpDir();
        oxRegistry::getLang()->setBaseLanguage(0);

        $oBase = new _oxI18n();
        $oBase->enableLazyLoading();
        $oBase->init("oxarticles");
        $oBase->load("2000");
        $this->assertEquals("Wanduhr ROBOT", $oBase->oxarticles__oxtitle->value);
    }

    public function testLazyLoadInLang1()
    {
        $this->cleanTmpDir();
        oxRegistry::getLang()->setBaseLanguage(1);

        $oBase = new _oxI18n();
        $oBase->enableLazyLoading();
        $oBase->init("oxarticles");
        $oBase->load("2000");
        $this->assertEquals("Wall Clock ROBOT", $oBase->oxarticles__oxtitle->value);
    }

    public function testLoad()
    {
        oxRegistry::getLang()->setBaseLanguage(1);

        $oObj = new _oxI18n();
        $oObj->init("oxarticles");
        $oObj->load(1127);

        $this->assertEquals("Ice Cubes FLASH", $oObj->oxarticles__oxtitle->value);
    }

    public function testGetAvailableInLangs()
    {
        $aLang = array('de' => "Deutsch", 'en' => "English", 'lt' => "Lithuanian", 'zb' => "ZuluBumBum");
        $aLangParams['de']['baseId'] = 0;
        $aLangParams['de']['abbr'] = 'de';
        $aLangParams['en']['baseId'] = 1;
        $aLangParams['en']['abbr'] = 'en';
        $aLangParams['lt']['baseId'] = 2;
        $aLangParams['lt']['abbr'] = 'lt';
        $aLangParams['zb']['baseId'] = 3;
        $aLangParams['zb']['abbr'] = 'zb';

        $this->getConfig()->setConfigParam('aLanguageParams', $aLangParams);
        $this->getConfig()->setConfigParam('aLanguages', $aLang);

        $oObj = new _oxI18n();
        $oObj->init("oxwrapping");
        $oObj->load('a6840cc0ec80b3991.74884864');

        $aRes = $oObj->getAvailableInLangs();
        $this->assertEquals(array(0 => "Deutsch", 1 => "English"), $aRes);
    }

    public function testGetAvailableInLangsWithNotLoadedObject()
    {
        $aLang = array(0 => "Deutsch", 1 => "English", 2 => "Lithuanian", 3 => "ZuluBumBum");
        $this->getConfig()->setConfigParam('aLanguages', $aLang);

        $oObj = new _oxI18n();
        $oObj->init("oxwrapping");

        $aRes = $oObj->getAvailableInLangs();
        $this->assertEquals(array(), $aRes);

        $oObj->setId('noSuchId');
        $aRes = $oObj->getAvailableInLangs();
        $this->assertEquals(array(), $aRes);
    }

    public function testGetAvailableInLangsObjectWithoutMultilangFields()
    {
        $aRezLang = array(0 => "Deutsch", 1 => "English", 2 => "Lithuanian", 3 => "ZuluBumBum");
        $aLang = array('de' => "Deutsch", 'en' => "English", 'lt' => "Lithuanian", 'zb' => "ZuluBumBum");
        $aLangParams['de']['baseId'] = 0;
        $aLangParams['de']['abbr'] = 'de';
        $aLangParams['en']['baseId'] = 1;
        $aLangParams['en']['abbr'] = 'en';
        $aLangParams['lt']['baseId'] = 2;
        $aLangParams['lt']['abbr'] = 'lt';
        $aLangParams['zb']['baseId'] = 3;
        $aLangParams['zb']['abbr'] = 'zb';

        $this->getConfig()->setConfigParam('aLanguageParams', $aLangParams);
        $this->getConfig()->setConfigParam('aLanguages', $aLang);
        $this->getConfig()->setConfigParam('aLanguages', $aLang);

        $oObj = new _oxI18n();
        $oObj->init("oxvouchers");

        $aRes = $oObj->getAvailableInLangs();
        $this->assertEquals($aRezLang, $aRes);
    }

    public function testGetFieldLang()
    {
        $oObj = new _oxI18n();
        $this->assertEquals("12", $oObj->UNITgetFieldLang('oxtitle_12'));
        $this->assertEquals("1", $oObj->UNITgetFieldLang('oxtitle_1'));
        $this->assertEquals("0", $oObj->UNITgetFieldLang('oxtitle'));
        $this->assertEquals("0", $oObj->UNITgetFieldLang('oxtitle_555'));
    }

    public function testAddFieldNormal()
    {
        $oObj = new _oxI18n();
        $oObj->setClassVar("_sCoreTable", "oxtesttable");
        $oObj->UNITaddField('oxtestField', 1);

        $aFieldNames = $oObj->getClassVar("_aFieldNames");

        $this->assertEquals(array("oxid" => 0, "oxtestfield" => 1), $aFieldNames);
        $this->assertTrue(isset($oObj->oxtesttable__oxtestfield));
    }

    public function testAddFieldMulitlanguage()
    {
        $oObj = new _oxI18n();
        $oObj->setClassVar("_sCoreTable", "oxtesttable");
        $oObj->UNITaddField('oxtestField_1', 1);

        $aFieldNames = $oObj->getClassVar("_aFieldNames");

        $this->assertEquals(array('oxid' => 0), $aFieldNames);
        $this->assertFalse(isset($oObj->oxtesttable__oxtestfield));
        $this->assertFalse(isset($oObj->oxtesttable__oxtestfield_1));
    }

    public function testAddFieldMulitlanguageDisableMultilang()
    {
        $oObj = new _oxI18n();
        $oObj->setClassVar("_sCoreTable", "oxtesttable");
        $oObj->setEnableMultilang(false);
        $oObj->UNITaddField('oxtestField_1', 1);

        $aFieldNames = $oObj->getClassVar("_aFieldNames");

        $this->assertEquals(array('oxid' => 0, 'oxtestfield_1' => 1), $aFieldNames);
        $this->assertFalse(isset($oObj->oxtesttable__oxtestfield));
        $this->assertTrue(isset($oObj->oxtesttable__oxtestfield_1));
    }

    //tests from oxBase public functions, but having different public functionality in oxi18n
    public function testGetUpdateFieldsLang0()
    {
        $oObj = new _oxI18n();
        $oObj->init('oxattribute');
        $oObj->setLanguage(0);

        $shopId = $this->getSqlShopId();
        $sExpRes = "oxid = '',oxshopid = '" . $shopId . "',oxtitle = '',oxpos = '9999',oxdisplayinbasket = '0'";

        $this->assertEquals($sExpRes, $oObj->UNITgetUpdateFields());

    }

    public function testGetSelectFieldsLang0()
    {
        $oObj = new _oxI18n();
        $oObj->init('oxattribute');
        $oObj->setLanguage(0);
        $sTable = $oObj->getViewName();

        $additional = $this->getConfig()->getEdition() === 'EE' ? "`$sTable`.`oxmapid`, " : "";
        $sExpRes = "`$sTable`.`oxid`, $additional`$sTable`.`oxshopid`, `$sTable`.`oxtitle`, `$sTable`.`oxpos`, `$sTable`.`oxtimestamp`, `$sTable`.`oxdisplayinbasket`";

        $this->assertEquals($sExpRes, $oObj->getSelectFields());
    }

    public function testGetUpdateFieldsLang1()
    {
        $oObj = new _oxI18n();
        $oObj->init('oxattribute');
        $oObj->setLanguage(1);

        $shopId = $this->getSqlShopId();
        $sExpRes = "oxid = '',oxshopid = '$shopId',oxtitle_1 = '',oxpos = '9999',oxdisplayinbasket = '0'";

        $this->assertEquals($sExpRes, $oObj->UNITgetUpdateFields());
    }

    public function testGetSelectFieldsLang1()
    {
        $oObj = new _oxI18n();
        $oObj->init('oxattribute');
        $oObj->setLanguage(1);
        $sTable = $oObj->getViewName();

        $additional = $this->getConfig()->getEdition() === 'EE' ? "`$sTable`.`oxmapid`, " : "";
        $sExpRes = "`$sTable`.`oxid`, $additional`$sTable`.`oxshopid`, `$sTable`.`oxtitle`, `$sTable`.`oxpos`, `$sTable`.`oxtimestamp`, `$sTable`.`oxdisplayinbasket`";

        $this->assertEquals($sExpRes, $oObj->getSelectFields());
    }

    public function testGetUpdateFieldsLang1DisableMultilang()
    {
        $oObj = new _oxI18n();
        $oObj->setEnableMultilang(false);
        $oObj->init('oxattribute');
        $oObj->setLanguage(1);

        $shopId = $this->getSqlShopId();
        $sExpRes = "oxid = '',oxshopid = '$shopId',oxtitle = '',oxtitle_1 = '',oxtitle_2 = '',oxtitle_3 = '',oxpos = '9999',oxdisplayinbasket = '0'";

        $this->assertEquals($sExpRes, $oObj->UNITgetUpdateFields());
    }

    public function testGetSelectFieldsLang1DisableMultilang()
    {
        $oObj = new _oxI18n();
        $oObj->setEnableMultilang(false);
        $oObj->init('oxattribute');
        $oObj->setLanguage(1);
        $sTable = $oObj->getViewName();

        $additional = $this->getConfig()->getEdition() === 'EE' ? "`$sTable`.`oxmapid`, " : "";
        $sExpRes = "`$sTable`.`oxid`, $additional`$sTable`.`oxshopid`, `$sTable`.`oxtitle`, `$sTable`.`oxtitle_1`, `$sTable`.`oxtitle_2`, `$sTable`.`oxtitle_3`, `$sTable`.`oxpos`, `$sTable`.`oxtimestamp`, `$sTable`.`oxdisplayinbasket`";

        $this->assertEquals($sExpRes, $oObj->getSelectFields());
    }

    public function testGetSqlActiveSnippetForceCoreActiveMultilang()
    {
        $iCurrTime = 1453734000; //some rounded timestamp

        $oUtilsDate = $this->getMock('oxUtilsDate', array('getRequestTime'));
        $oUtilsDate->expects($this->any())->method('getRequestTime')->will($this->returnValue($iCurrTime));
        /** @var oxUtilsDate $oUtils */
        oxRegistry::set('oxUtilsDate', $oUtilsDate);

        $oI18n = $this->getMock('oxI18n', array('getViewName'));
        $oI18n->expects($this->once())->method('getViewName')->with($this->equalTo(null))->will($this->returnValue('oxi18n'));

        $oI18n->UNITaddField('oxactive', 0);
        $oI18n->UNITaddField('oxactivefrom', 0);
        $oI18n->UNITaddField('oxactiveto', 0);

        if ($this->getConfig()->getEdition() === 'EE') {
            $oI18n->setForceCoreTableUsage(true);
        }

        $sDate = date('Y-m-d H:i:s', $iCurrTime);
        $sTable = 'oxi18n';
        $sTemplate = " (   $sTable.oxactive = 1  or  ( $sTable.oxactivefrom < '$sDate' and $sTable.oxactiveto > '$sDate' ) ) ";

        $sQ = $oI18n->getSqlActiveSnippet();
        $this->assertEquals($sTemplate, $sQ);
    }

    public function testGetSqlActiveSnippet()
    {
        $iCurrTime = 1453734000; //some rounded timestamp

        $oUtilsDate = $this->getMock('oxUtilsDate', array('getRequestTime'));
        $oUtilsDate->expects($this->any())->method('getRequestTime')->will($this->returnValue($iCurrTime));
        /** @var oxUtilsDate $oUtils */
        oxRegistry::set('oxUtilsDate', $oUtilsDate);

        $sTable = 'oxi18n';

        /** @var oxI18n|PHPUnit_Framework_MockObject_MockObject $oI18n */
        $oI18n = $this->getMock('oxI18n', array('getCoreTableName', 'getViewName', 'isMultilingualField', 'getLanguage'));
        $oI18n->expects($this->any())->method('getCoreTableName')->will($this->returnValue($sTable));
        $oI18n->expects($this->once())->method('getViewName')->will($this->returnValue('oxi18n'));
        $oI18n->expects($this->never())->method('getLanguage');

        $oI18n->UNITaddField('oxactive', 0);
        $oI18n->UNITaddField('oxactivefrom', 0);
        $oI18n->UNITaddField('oxactiveto', 0);

        if ($this->getConfig()->getEdition() === 'EE') {
            $oI18n->setForceCoreTableUsage(false);
        }

        $sDate = date('Y-m-d H:i:s', $iCurrTime);
        $sTemplate = " (   $sTable.oxactive = 1  or  ( $sTable.oxactivefrom < '$sDate' and $sTable.oxactiveto > '$sDate' ) ) ";

        $sQ = $oI18n->getSqlActiveSnippet();
        $this->assertEquals($sTemplate, $sQ);
    }

    /*
     * Testing if object is treated as multilanguage
     */
    public function testIsMultilang()
    {
        $oObj = oxNew('oxi18n');
        $this->assertTrue($oObj->isMultilang());
    }

    /*
     * Testing cache hay modifier
     */
    public function testModifyCacheKey()
    {
        $oObj = $this->getProxyClass('oxi18n');
        $oObj->modifyCacheKey(null);
        $this->assertNull($oObj->getNonPublicVar("_sCacheKey"));
        $oObj->modifyCacheKey("_nonml");
        $this->assertEquals("_nonml", $oObj->getNonPublicVar("_sCacheKey"));
        $oObj->modifyCacheKey("_nonml");
        $this->assertEquals("_nonml_nonml", $oObj->getNonPublicVar("_sCacheKey"));
        $oObj->modifyCacheKey("_nonml", true);
        $this->assertEquals("_nonml|i18n", $oObj->getNonPublicVar("_sCacheKey"));
    }

    /**
     * base test
     */
    public function testGetUpdateSqlFieldNameMLfield()
    {
        $oObj = $this->getMock('oxi18n', array('isMultilingualField'));
        $oObj->expects($this->exactly(2))->method('isMultilingualField')
            ->with($this->equalTo('field'))
            ->will($this->returnValue(true));

        $oObj->setLanguage(0);
        $this->assertEquals('field', $oObj->getUpdateSqlFieldName('field'));
        $oObj->setLanguage(1);
        $this->assertEquals('field_1', $oObj->getUpdateSqlFieldName('field'));
        $oObj->setLanguage(200);
        $this->assertEquals('field_200', $oObj->getUpdateSqlFieldName('field'));
    }

    /**
     * base test
     */
    public function testGetUpdateSqlFieldNameNonMLfield()
    {
        $oObj = $this->getMock('oxi18n', array('isMultilingualField'));
        $oObj->expects($this->exactly(2))->method('isMultilingualField')
            ->with($this->equalTo('field'))
            ->will($this->returnValue(false));

        $oObj->setLanguage(0);
        $this->assertEquals('field', $oObj->getUpdateSqlFieldName('field'));
        $oObj->setLanguage(1);
        $this->assertEquals('field', $oObj->getUpdateSqlFieldName('field'));
        $oObj->setLanguage(200);
        $this->assertEquals('field', $oObj->getUpdateSqlFieldName('field'));
    }

    /**
     * base test
     */
    public function testGetUpdateFieldsForTable()
    {
        $oObj = oxNew('oxi18n');
        $oObj->init('oxstates');
        $oObj->setId('test_a');
        $oObj->oxstates__oxtitle = new oxField('titletest');

        $oObj->setLanguage(0);
        $this->assertEquals("oxid = 'test_a',oxcountryid = '',oxtitle = 'titletest',oxisoalpha2 = ''", $oObj->UNITgetUpdateFieldsForTable('oxstates'));
        $this->assertEquals("oxid = 'test_a'", $oObj->UNITgetUpdateFieldsForTable(getLangTableName('oxstates', 90)));
        $this->assertEquals("oxid = 'test_a'", $oObj->UNITgetUpdateFieldsForTable(getLangTableName('oxstates', 100)));

        $oObj->setLanguage(90);
        $this->assertEquals("oxid = 'test_a',oxcountryid = '',oxisoalpha2 = ''", $oObj->UNITgetUpdateFieldsForTable('oxstates'));
        $this->assertEquals("oxid = 'test_a',oxtitle_90 = 'titletest'", $oObj->UNITgetUpdateFieldsForTable(getLangTableName('oxstates', 90)));
        $this->assertEquals("oxid = 'test_a'", $oObj->UNITgetUpdateFieldsForTable(getLangTableName('oxstates', 100)));
    }

    /**
     * base test
     */
    public function testGetUpdateFieldsForTableNonMlObject()
    {
        $cl = oxTestModules::addFunction(
            oxTestModules::addFunction(
                'oxi18n',
                '__setFieldNames($fn)',
                '{$this->_aFieldNames = $fn;}'
            ),
            '__getFieldNames',
            '{return $this->_aFieldNames;}'
        );
        $oObj = new $cl();
        $oObj->setEnableMultilang(false);
        $oObj->init('oxstates');
        $oObj->__setFieldNames(array_merge($oObj->__getFieldNames(), array('oxtitle_90' => 0)));
        $oObj->setId('test_a');
        $oObj->oxstates__oxtitle_90 = new oxField('titletest');

        $oObj->setLanguage(0);
        $this->assertEquals("oxid = 'test_a',oxcountryid = '',oxtitle = '',oxisoalpha2 = '',oxtitle_1 = '',oxtitle_2 = '',oxtitle_3 = ''", $oObj->UNITgetUpdateFieldsForTable('oxstates'));
        $this->assertEquals("oxid = 'test_a',oxtitle_90 = 'titletest'", $oObj->UNITgetUpdateFieldsForTable(getLangTableName('oxstates', 90)));
        $this->assertEquals("oxid = 'test_a'", $oObj->UNITgetUpdateFieldsForTable(getLangTableName('oxstates', 100)));

        $oObj->setLanguage(90);
        $this->assertEquals("oxid = 'test_a',oxcountryid = '',oxtitle = '',oxisoalpha2 = '',oxtitle_1 = '',oxtitle_2 = '',oxtitle_3 = ''", $oObj->UNITgetUpdateFieldsForTable('oxstates'));
        $this->assertEquals("oxid = 'test_a',oxtitle_90 = 'titletest'", $oObj->UNITgetUpdateFieldsForTable(getLangTableName('oxstates', 90)));
        $this->assertEquals("oxid = 'test_a'", $oObj->UNITgetUpdateFieldsForTable(getLangTableName('oxstates', 100)));
    }

    /**
     * base test
     */
    public function testGetUpdateFields()
    {
        $oObj = $this->getMock('oxi18n', array('_getUpdateFieldsForTable', 'getCoreTableName'));
        $oObj->expects($this->exactly(1))->method('_getUpdateFieldsForTable')
            ->with($this->equalTo('coretable'), $this->equalTo('useskipsavefields'))
            ->will($this->returnValue('returned val'));
        $oObj->expects($this->exactly(1))->method('getCoreTableName')
            ->will($this->returnValue('coretable'));

        $this->assertEquals('returned val', $oObj->UNITgetUpdateFields('useskipsavefields'));
    }

    public static $aLoggedSqls = array();

    /**
     * base test
     */
    public function testUpdate()
    {
        $oObj = oxNew('oxi18n');
        $oObj->init('oxstates');


        $oObj->setId("test_update");
        $oObj->oxstates__oxtitle = new oxField('test_x');

        $dbMock = $this->getDbObjectMock();
        $dbMock->expects($this->any())->method('select')->will($this->returnValue(false));
        $dbMock->expects($this->any())->method('execute')->will($this->evalFunction('{\Unit\Core\I18nTest::$aLoggedSqls[] = $args[0];return true;}'));
        $this->setProtectedClassProperty(Database::getInstance(), 'db' , $dbMock); 

        $oObj->setLanguage(0);
        I18nTest::$aLoggedSqls = array();
        $oObj->UNITupdate();
        $this->assertEquals(
            array("update oxstates set oxid = 'test_update',oxcountryid = '',oxtitle = 'test_x',oxisoalpha2 = '' where oxstates.oxid = 'test_update'"),
            array_map('trim', I18ntest::$aLoggedSqls)
        );

        $oObj->setLanguage(90);
        I18nTest::$aLoggedSqls = array();
        $oObj->UNITupdate();
        $this->assertEquals(
            array(
                 "update oxstates set oxid = 'test_update',oxcountryid = '',oxisoalpha2 = '' where oxstates.oxid = 'test_update'",
                 "insert into oxstates_set11 set oxid = 'test_update',oxtitle_90 = 'test_x' on duplicate key update oxid = 'test_update',oxtitle_90 = 'test_x'",
            ),
            array_map('trim', I18ntest::$aLoggedSqls)
        );
    }

    /**
     * base test
     */
    public function testUpdate_MLangDisabled()
    {
        $cl = oxTestModules::addFunction(
            oxTestModules::addFunction(
                'oxi18n',
                '__setFieldNames($fn)',
                '{$this->_aFieldNames = $fn;}'
            ),
            '__getFieldNames',
            '{return $this->_aFieldNames;}'
        );
        $oObj = $this->getMock($cl, array('_getLanguageSetTables'));
        $oObj->expects($this->any())->method('_getLanguageSetTables')->will($this->returnValue(array('oxstates_set11')));
        $oObj->setEnableMultilang(false);
        $oObj->init('oxstates');
        $oObj->__setFieldNames(array_merge($oObj->__getFieldNames(), array('oxtitle_90' => 0)));

        $oObj->setId("test_update");
        $oObj->oxstates__oxtitle = new oxField('test_x');
        $oObj->oxstates__oxtitle_90 = new oxField('test_y');

        $dbMock = $this->getDbObjectMock();
        $dbMock->expects($this->any())->method('select')->will($this->returnValue(false));
        $dbMock->expects($this->any())->method('execute')->will($this->evalFunction('{\Unit\Core\I18nTest::$aLoggedSqls[] = $args[0];return true;}'));
        $this->setProtectedClassProperty(Database::getInstance(), 'db' , $dbMock); 

        $oObj->setLanguage(0);
        I18ntest::$aLoggedSqls = array();
        $oObj->UNITupdate();
        $this->assertEquals(
            array(
                 "update oxstates set oxid = 'test_update',oxcountryid = '',oxtitle = 'test_x',oxisoalpha2 = '',oxtitle_1 = '',oxtitle_2 = '',oxtitle_3 = '' where oxstates.oxid = 'test_update'",
                 "insert into oxstates_set11 set oxid = 'test_update',oxtitle_90 = 'test_y' on duplicate key update oxid = 'test_update',oxtitle_90 = 'test_y'",
            ),
            array_map('trim', I18ntest::$aLoggedSqls)
        );

        $oObj->setLanguage(90);
        I18nTest::$aLoggedSqls = array();
        $oObj->UNITupdate();
        $this->assertEquals(
            array(
                 "update oxstates set oxid = 'test_update',oxcountryid = '',oxtitle = 'test_x',oxisoalpha2 = '',oxtitle_1 = '',oxtitle_2 = '',oxtitle_3 = '' where oxstates.oxid = 'test_update'",
                 "insert into oxstates_set11 set oxid = 'test_update',oxtitle_90 = 'test_y' on duplicate key update oxid = 'test_update',oxtitle_90 = 'test_y'",
            ),
            array_map('trim', I18ntest::$aLoggedSqls)
        );
    }

    public function testGetLanguageSetTables()
    {
        $oObj = oxNew('oxi18n');
        $oObj->init('oxstates');

        $this->assertEquals(
            array(),
            $oObj->UNITgetLanguageSetTables()
        );

        $oLang = $this->getMock('oxLang', array('getLanguageIds'));
        $oLang->expects($this->any())->method('getLanguageIds')->will($this->returnValue(array(0 => 'de', 1 => 'en', 90 => 'lt')));

        oxTestModules::addModuleObject('oxLang', $oLang);

        $this->assertEquals(
            array(
                 'oxstates_set11'
            ),
            $oObj->UNITgetLanguageSetTables()
        );
    }

    /**
     * base test
     */
    public function testInsert()
    {
        $oObj = $this->getMock('oxi18n', array('_getLanguageSetTables'));
        $oObj->expects($this->any())->method('_getLanguageSetTables')->will($this->returnValue(array('oxstates_set11')));
        $oObj->init('oxstates');


        $oObj->setId("test_insert");
        $oObj->oxstates__oxtitle = new oxField('test_x');


        $dbMock = $this->getDbObjectMock();
        $dbMock->expects($this->any())->method('select')->will($this->returnValue(false));
        $dbMock->expects($this->any())->method('execute')->will($this->evalFunction('{\Unit\Core\I18nTest::$aLoggedSqls[] = $args[0];return true;}'));
        $this->setProtectedClassProperty(Database::getInstance(), 'db' , $dbMock); 

        $oObj->setLanguage(0);
        I18nTest::$aLoggedSqls = array();
        $oObj->UNITinsert();
        $this->assertEquals(
            array(
                "Insert into oxstates set oxid = 'test_insert',oxcountryid = '',oxtitle = 'test_x',oxisoalpha2 = ''",
                "insert into oxstates_set11 set oxid = 'test_insert'",
            ),
            array_map('trim', I18nTest::$aLoggedSqls)
        );

        $oObj->setLanguage(90);
        I18nTest::$aLoggedSqls = array();
        $oObj->UNITinsert();
        $this->assertEquals(
            array(
                "Insert into oxstates set oxid = 'test_insert',oxcountryid = '',oxisoalpha2 = ''",
                "insert into oxstates_set11 set oxid = 'test_insert',oxtitle_90 = 'test_x'",
            ),
            array_map('trim', I18nTest::$aLoggedSqls)
        );
    }


    /**
     * base test
     */
    public function testGetViewName()
    {
        $oObj = oxNew('oxi18n');
        $oObj->init('oxarticles');

        $this->assertEquals(getViewName('oxarticles', 0, 1), $oObj->getViewName());
        $this->assertEquals(getViewName('oxarticles', 0, -1), $oObj->getViewName(1));
        $this->assertEquals(getViewName('oxarticles', 0, 1), $oObj->getViewName(0));
        $this->assertEquals(getViewName('oxarticles', 0, 1), $oObj->getViewName());

        $oObj->setLanguage(1);
        $this->assertEquals(getViewName('oxarticles', 1, 1), $oObj->getViewName());
        $this->assertEquals(getViewName('oxarticles', 1, -1), $oObj->getViewName(1));
        $this->assertEquals(getViewName('oxarticles', 1, 1), $oObj->getViewName(0));
        $this->assertEquals(getViewName('oxarticles', 1, 1), $oObj->getViewName());

        $oObj->setEnableMultilang(false);
        $this->assertEquals(getViewName('oxarticles', -1, 1), $oObj->getViewName());
        $this->assertEquals(getViewName('oxarticles', -1, 1), $oObj->getViewName(0));
        $this->assertEquals(getViewName('oxarticles', -1, -1), $oObj->getViewName(1));
        $this->assertEquals(getViewName('oxarticles', -1, 1), $oObj->getViewName());
    }


    /**
     * base test
     */
    public function testGetAllFields()
    {
        $oObj = $this->getMock('oxi18n', array('_getTableFields', 'getViewName'));
        $oObj->expects($this->exactly(1))->method('_getTableFields')
            ->with($this->equalTo('view'), $this->equalTo('simeple?'))
            ->will($this->returnValue('returned val'));
        $oObj->expects($this->exactly(1))->method('getViewName')
            ->will($this->returnValue('view'));
        $oObj->setEnableMultilang(false);

        $this->assertEquals('returned val', $oObj->UNITGetAllFields('simeple?'));

        $oObj = $this->getMock('oxi18n', array('getViewName'));
        $oObj->expects($this->exactly(1))->method('getViewName')
            ->will($this->returnValue(''));
        $oObj->setEnableMultilang(false);

        $this->assertEquals(array(), $oObj->UNITGetAllFields('simeple?'));

    }

    /**
     * Test get update field value.
     */
    public function test_getUpdateFieldValue()
    {
        $oObj = oxNew('oxI18n');
        $oObj->init("oxarticles");
        $oObj->setId('test');
        $this->assertSame("'aaa'", $oObj->UNITgetUpdateFieldValue('oxid', new oxField('aaa')));
        $this->assertSame("'aaa\\\"'", $oObj->UNITgetUpdateFieldValue('oxid', new oxField('aaa"')));
        $this->assertSame("'aaa\''", $oObj->UNITgetUpdateFieldValue('oxid', new oxField('aaa\'')));

        $this->assertSame("''", $oObj->UNITgetUpdateFieldValue('oxid', new oxField(null)));
        $this->assertSame('null', $oObj->UNITgetUpdateFieldValue('oxvat', new oxField(null)));

        $this->assertSame("''", $oObj->UNITgetUpdateFieldValue('oxid_10', new oxField(null)));
        $this->assertSame('null', $oObj->UNITgetUpdateFieldValue('oxvat_10', new oxField(null)));
    }

    /**
     * Test for #0003138: Multilanguage fields having different
     * character case are not always detected as multilanguage
     */
    public function testIsMultilingualFieldFor0003138()
    {
        $oArticle = oxNew('oxArticle');
        $this->assertTrue($oArticle->isMultilingualField("oxtitle"));
        $this->assertTrue($oArticle->isMultilingualField("OXTITLE"));
        $this->assertTrue($oArticle->isMultilingualField("oXtItLe"));
    }

    protected $_aLangTables = array();

    /**
     * Inserts new test language tables
     */
    protected function _insertTestLanguage()
    {
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $this->_aLangTables["oxactions"] = "oxactions";
        $this->_aLangTables["oxcategory"] = "oxcategories";
        $this->_aLangTables["oxcontent"] = "oxcontents";
        $this->_aLangTables["oxcountry"] = "oxcountry";
        $this->_aLangTables["oxdelivery"] = "oxdelivery";
        $this->_aLangTables["oxdiscount"] = "oxdiscount";
        $this->_aLangTables["oxgroups"] = "oxgroups";
        $this->_aLangTables["oxlinks"] = "oxlinks";
        $this->_aLangTables["oxmediaurl"] = "oxmediaurls";
        $this->_aLangTables["oxnews"] = "oxnews";
        $this->_aLangTables["oxpayment"] = "oxpayments";
        $this->_aLangTables["oxreview"] = "oxreviews";
        $this->_aLangTables["oxstate"] = "oxstates";
        $this->_aLangTables["oxvendor"] = "oxvendor";
        $this->_aLangTables["oxwrapping"] = "oxwrapping";
        $this->_aLangTables["oxattribute"] = "oxattribute";
        $this->_aLangTables["oxselectlist"] = "oxselectlist";
        $this->_aLangTables["oxdeliveryset"] = "oxdeliveryset";
        $this->_aLangTables["oxmanufacturer"] = "oxmanufacturers";

        // creating language set tables and inserting by one test record
        foreach ($this->_aLangTables as $iPos => $sTable) {
            $sQ = "show create table {$sTable}";
            $rs = $oDb->select($sQ);

            // creating table
            $sQ = end($rs->fields);
            if ((stripos($sTable, "oxartextends") === false && stripos($sTable, "oxshops") === false) &&
                !preg_match("/oxshopid/i", $sQ)
            ) {
                unset($this->_aLangTables[$iPos]);
                continue;
            }


            $sQ = str_replace($sTable, $sTable . "_set1", $sQ);
            $oDb->execute($sQ);
        }

        $sShopId = $this->_sOXID;

        // inserting test records
        foreach ($this->_aLangTables as $sTable) {
            // do not insert data into shops table..
            if (stripos($sTable, "oxshops") !== false) {
                continue;
            }

            $sQVal = "";
            $sQ = "show columns from {$sTable}";
            $rs = $oDb->select($sQ);
            if ($rs != false && $rs->count() > 0) {
                while (!$rs->EOF) {
                    $sValue = $rs->fields["Default"];
                    $sType = $rs->fields["Type"];
                    $sField = $rs->fields["Field"];

                    // overwriting default values
                    if (stripos($sField, "oxshopid") !== false) {
                        $sValue = $sShopId;
                    }
                    if (stripos($sField, "oxid") !== false) {
                        $sValue = "_testRecordForTest";
                    }


                    if ($sQVal) {
                        $sQVal .= ", ";
                    }
                    $sQVal .= "'$sValue'";
                    $rs->moveNext();
                }
            }

            $oDb->execute("insert into {$sTable} values ({$sQVal})");
            $oDb->execute("insert into {$sTable}_set1 values ({$sQVal})");
        }
    }

    /**
     * Removes test language tables
     */
    protected function _deleteTestLanguage()
    {
        // dropping language set tables
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
        foreach ($this->_aLangTables as $sTable) {
            $oDb->execute("drop table {$sTable}_set1");
            $oDb->execute("delete from {$sTable} where oxid like '_test%'");
        }
    }

    /**
     * Testing how multilanguage objects are deleted..
     */
    public function testMultilangObjectDeletion()
    {
        $sId = "_testRecordForTest";
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $this->getConfig()->setConfigParam("iLangPerTable", 4);
        oxTestModules::addFunction("oxLang", "getLanguageIds", "{return array('0' => 'de', '1' => 'de', '2' => 'lt', '3' => 'ru', '4' => 'pl', '5' => 'cz');}");

        foreach ($this->_aLangTables as $sObjectType => $sTableName) {
            $this->assertTrue((bool) $oDb->getOne("select 1 from {$sTableName} where oxid = '{$sId}'"), "Missing data for table {$sTableName} table");
            $this->assertTrue((bool) $oDb->getOne("select 1 from {$sTableName}_set1 where oxid = '{$sId}'"), "Missing data for table {$sTableName}_set1 table");

            $oObject = oxNew($sObjectType);
            $oObject->setId($sId);

            // some fine tuning..
            if ($sObjectType == "oxcategory") {
                $oObject->oxcategories__oxright = new oxField(11);
                $oObject->oxcategories__oxleft = new oxField(10);
            }

            $this->assertTrue($oObject->delete($sId), "Unable to delete $sObjectType type object");

            $this->assertFalse((bool) $oDb->getOne("select 1 from {$sTableName} where oxid = '{$sId}'"), "Not cleaned {$sTableName} table");
            $this->assertFalse((bool) $oDb->getOne("select 1 from {$sTableName}_set1 where oxid = '{$sId}'"), "Not cleaned {$sTableName}_set1 table");
        }
    }
}
