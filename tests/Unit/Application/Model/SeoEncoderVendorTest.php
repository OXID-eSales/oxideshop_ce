<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use modDB;
use \oxField;
use \oxDb;
use \oxTestModules;

/**
 * Testing oxseoencodervendor class
 */
class SeoEncoderVendorTest extends \OxidTestCase
{
    /**
     * Initialize the fixture.
     */
    protected function setUp()
    {
        parent::setUp();

        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        modDB::getInstance()->cleanup();
        oxDb::getDb()->execute('delete from oxseo where oxtype != "static"');
        oxDb::getDb()->execute('delete from oxobject2seodata');
        oxDb::getDb()->execute('delete from oxseohistory');

        $this->cleanUpTable('oxcategories');

        parent::tearDown();
    }

    /**
     * oxSeoEncoderManufacturer::_getAltUri() test case
     */
    public function testGetAltUriTag()
    {
        oxTestModules::addFunction("oxVendor", "loadInLang", "{ return true; }");

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderVendor::class, array("getVendorUri"));
        $oEncoder->expects($this->once())->method('getVendorUri')->will($this->returnValue("vendorUri"));

        $this->assertEquals("vendorUri", $oEncoder->UNITgetAltUri('1126', 0));
    }

    public function testGetVendorUrlExistingVendor()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $sVndId = $this->getTestConfig()->getShopEdition() == 'EE' ? 'd2e44d9b32fd2c224.65443178' : '77442e37fdf34ccd3.94620745';
        $sUrl = $this->getTestConfig()->getShopEdition() == 'EE' ? 'Nach-Lieferant/Hersteller-2/' : 'Nach-Lieferant/Bush/';

        $oVendor = oxNew('oxVendor');
        $oVendor->load($sVndId);

        $oEncoder = oxNew('oxSeoEncoderVendor');
        $this->assertEquals($this->getConfig()->getShopUrl() . $sUrl, $oEncoder->getVendorUrl($oVendor));
    }

    public function testGetVendorUrlExistingVendorEng()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction('oxVendor', 'resetRootVendor', '{ self::$_aRootVendor = array() ; }');
        $oVendor = oxNew('oxVendor');
        $oVendor->resetRootVendor();

        $sVndId = $this->getTestConfig()->getShopEdition() == 'EE' ? 'd2e44d9b32fd2c224.65443178' : '77442e37fdf34ccd3.94620745';
        $sUrl = $this->getTestConfig()->getShopEdition() == 'EE' ? 'en/By-distributor/Manufacturer-2/' : 'en/By-distributor/Bush/';

        $oVendor = oxNew('oxVendor');
        $oVendor->loadInLang(1, $sVndId);

        $oEncoder = oxNew('oxSeoEncoderVendor');
        $this->assertEquals($this->getConfig()->getShopUrl() . $sUrl, $oEncoder->getVendorUrl($oVendor));
    }

    public function testGetVendorUrlExistingVendorWithLangParam()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $sVndId = $this->getTestConfig()->getShopEdition() == 'EE' ? 'd2e44d9b32fd2c224.65443178' : '77442e37fdf34ccd3.94620745';
        $sUrl = $this->getTestConfig()->getShopEdition() == 'EE' ? 'Nach-Lieferant/Hersteller-2/' : 'Nach-Lieferant/Bush/';

        $oVendor = oxNew('oxVendor');
        $oVendor->loadInLang(1, $sVndId);

        $oEncoder = oxNew('oxSeoEncoderVendor');
        $this->assertEquals($this->getConfig()->getShopUrl() . $sUrl, $oEncoder->getVendorUrl($oVendor, 0));
    }

    public function testGetVendorUrlExistingVendorEngWithLangParam()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction('oxVendor', 'resetRootVendor', '{ self::$_aRootVendor = array() ; }');
        $oVendor = oxNew('oxVendor');
        $oVendor->resetRootVendor();

        $sVndId = $this->getTestConfig()->getShopEdition() == 'EE' ? 'd2e44d9b32fd2c224.65443178' : '77442e37fdf34ccd3.94620745';
        $sUrl = $this->getTestConfig()->getShopEdition() == 'EE' ? 'en/By-distributor/Manufacturer-2/' : 'en/By-distributor/Bush/';

        $oVendor = oxNew('oxVendor');
        $oVendor->loadInLang(0, $sVndId);

        $oEncoder = oxNew('oxSeoEncoderVendor');
        $this->assertEquals($this->getConfig()->getShopUrl() . $sUrl, $oEncoder->getVendorUrl($oVendor, 1));
    }

    /**
     * Testing vendor uri getter
     */
    public function testGetVendorUriExistingVendor()
    {
        $oVendor = oxNew('oxVendor');
        $oVendor->setId('xxx');

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderVendor::class, array('_loadFromDb', '_prepareTitle', '_getUniqueSeoUrl', '_saveToDb'));
        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('oxvendor'), $this->equalTo('xxx'), $this->equalTo($oVendor->getLanguage()))->will($this->returnValue('seourl'));
        $oEncoder->expects($this->never())->method('_prepareTitle');
        $oEncoder->expects($this->never())->method('_getUniqueSeoUrl');
        $oEncoder->expects($this->never())->method('_saveToDb');

        $sUrl = 'seourl';
        $sSeoUrl = $oEncoder->getVendorUri($oVendor);
        $this->assertEquals($sUrl, $sSeoUrl);
    }

    public function testGetVendorUriRootVendor()
    {
        $oVendor = oxNew('oxVendor');
        $oVendor->setId('root');
        $oVendor->oxvendor__oxtitle = new oxField('root', oxField::T_RAW);

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderVendor::class, array('_saveToDb'));
        $oEncoder->expects($this->once())->method('_saveToDb')->with($this->equalTo('oxvendor'), $this->equalTo('root'), $this->equalTo($oVendor->getStdLink()), $this->equalTo('root/'), $this->equalTo($oVendor->getLanguage()));

        $sUrl = 'root/';
        $sSeoUrl = $oEncoder->getVendorUri($oVendor);
        $this->assertEquals($sUrl, $sSeoUrl);
    }

    public function testGetVendorUriRootVendorSecondLanguage()
    {
        $oVendor = oxNew('oxVendor');
        $oVendor->setId('root');
        $oVendor->setLanguage(1);
        $oVendor->oxvendor__oxtitle = new oxField('root', oxField::T_RAW);

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderVendor::class, array('_saveToDb'));
        $oEncoder->expects($this->once())->method('_saveToDb')->with($this->equalTo('oxvendor'), $this->equalTo('root'), $this->equalTo($oVendor->getBaseStdLink(1)), $this->equalTo('en/root/'), $this->equalTo($oVendor->getLanguage()));

        $sUrl = 'en/root/';
        $sSeoUrl = $oEncoder->getVendorUri($oVendor);
        $this->assertEquals($sUrl, $sSeoUrl);
    }

    public function testGetVendorUriNewVendor()
    {
        $oVendor = oxNew('oxVendor');
        $oVendor->setLanguage(1);
        $oVendor->setId('xxx');
        $oVendor->oxvendor__oxtitle = new oxField('xxx', oxField::T_RAW);

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderVendor::class, array('_loadFromDb', '_saveToDb'));
        $oEncoder->expects($this->exactly(2))->method('_loadFromDb')->will($this->returnValue(false));

        $sUrl = 'en/By-distributor/xxx/';
        $sSeoUrl = $oEncoder->getVendorUri($oVendor);

        $this->assertEquals($sUrl, $sSeoUrl);
    }

    /**
     * Testing object url getter
     */
    public function testGetVendorPageUrl()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $sVndId = $this->getTestConfig()->getShopEdition() == 'EE' ? 'd2e44d9b32fd2c224.65443178' : '77442e37fdf34ccd3.94620745';
        $sUrl = $this->getTestConfig()->getShopEdition() == 'EE' ? 'en/By-distributor/Manufacturer-2/?pgNr=100' : 'en/By-distributor/Bush/?pgNr=100';

        $oVendor = oxNew('oxVendor');
        $oVendor->loadInLang(1, $sVndId);

        $oEncoder = oxNew('oxSeoEncoderVendor');
        $this->assertEquals($this->getConfig()->getShopUrl() . $sUrl, $oEncoder->getVendorPageUrl($oVendor, 100));
    }

    public function testGetVendorPageUrlWithLangParam()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $sVndId = $this->getTestConfig()->getShopEdition() == 'EE' ? 'd2e44d9b32fd2c224.65443178' : '77442e37fdf34ccd3.94620745';
        $sUrl = $this->getTestConfig()->getShopEdition() == 'EE' ? 'en/By-distributor/Manufacturer-2/?pgNr=100' : 'en/By-distributor/Bush/?pgNr=100';

        $oVendor = oxNew('oxVendor');
        $oVendor->loadInLang(0, $sVndId);

        $oEncoder = oxNew('oxSeoEncoderVendor');
        $this->assertEquals($this->getConfig()->getShopUrl() . $sUrl, $oEncoder->getVendorPageUrl($oVendor, 100, 1));
    }

    public function testGetVendorUrl()
    {
        $oVendor = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('getLanguage'));
        $oVendor->expects($this->once())->method('getLanguage')->will($this->returnValue(0));

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderVendor::class, array('_getFullUrl', 'getVendorUri'));
        $oEncoder->expects($this->once())->method('_getFullUrl')->will($this->returnValue('seovndurl'));
        $oEncoder->expects($this->once())->method('getVendorUri');

        $this->assertEquals('seovndurl', $oEncoder->getVendorUrl($oVendor));
    }

    public function testGetVendorUriExistingVendorWithLangParam()
    {
        $oVendor = oxNew('oxVendor');
        $oVendor->setLanguage(1);
        $oVendor->setId('xxx');

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderVendor::class, array('_loadFromDb', '_prepareTitle', '_getUniqueSeoUrl', '_saveToDb'));
        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('oxvendor'), $this->equalTo('xxx'), $this->equalTo(0))->will($this->returnValue('seourl'));
        $oEncoder->expects($this->never())->method('_prepareTitle');
        $oEncoder->expects($this->never())->method('_getUniqueSeoUrl');
        $oEncoder->expects($this->never())->method('_saveToDb');

        $sUrl = 'seourl';
        $sSeoUrl = $oEncoder->getVendorUri($oVendor, 0);
        $this->assertEquals($sUrl, $sSeoUrl);
    }

    public function testGetVendorUriRootVendorWithLangParam()
    {
        $oVendor = oxNew('oxVendor');
        $oVendor->setId('root');
        $oVendor->oxvendor__oxtitle = new oxField('root', oxField::T_RAW);

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderVendor::class, array('_saveToDb'));
        $oEncoder->expects($this->once())->method('_saveToDb')->with($this->equalTo('oxvendor'), $this->equalTo('root'), $this->equalTo($oVendor->getBaseStdLink(1)), $this->equalTo('en/By-distributor/'), $this->equalTo(1));

        $sUrl = 'en/By-distributor/';
        $sSeoUrl = $oEncoder->getVendorUri($oVendor, 1);
        $this->assertEquals($sUrl, $sSeoUrl);
    }

    public function testonDeleteVendor()
    {
        $sShopId = $this->getConfig()->getBaseShopId();
        $oDb = oxDb::getDb();
        $sQ = "insert into oxseo
                   ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxfixed, oxexpired, oxparams )
               values
                   ( 'oid', '132', '{$sShopId}', '0', '', '', 'oxvendor', '0', '0', '' )";
        $oDb->execute($sQ);

        $sQ = "insert into oxobject2seodata ( oxobjectid, oxshopid, oxlang ) values ( 'oid', '{$sShopId}', '0' )";
        $oDb->execute($sQ);

        $sQ = "insert into oxseohistory ( oxobjectid, oxident, oxshopid, oxlang ) values ( 'oid', '132', '{$sShopId}', '0' )";
        $oDb->execute($sQ);

        $this->assertTrue((bool) $oDb->getOne("select 1 from oxseo where oxobjectid = 'oid'"));
        $this->assertTrue((bool) $oDb->getOne("select 1 from oxobject2seodata where oxobjectid = 'oid'"));
        $this->assertTrue((bool) $oDb->getOne("select 1 from oxseohistory where oxobjectid = 'oid'"));

        $oObj = oxNew('oxbase');
        $oObj->setId('oid');

        $oEncoder = oxNew('oxSeoEncoderVendor');
        $oEncoder->onDeleteVendor($oObj);

        $this->assertFalse((bool) $oDb->getOne("select 1 from oxseo where oxobjectid = 'oid'"));
        $this->assertFalse((bool) $oDb->getOne("select 1 from oxobject2seodata where oxobjectid = 'oid'"));
        $this->assertFalse((bool) $oDb->getOne("select 1 from oxseohistory where oxobjectid = 'oid'"));
    }
}
