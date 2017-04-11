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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

class modOxManufacturerForoxSeoEncoderManufacturerTest extends oxManufacturer
{

    public static function reSetRootManufacturer()
    {
        self::$_aRootManufacturer = array();
    }
}

/**
 * Testing oxseoencodermanufacturer class
 */
class Unit_Core_oxSeoEncoderManufacturerTest extends OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");
        //echo $this->getName()."\n";
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        modDB::getInstance()->cleanup();
        // deleting seo entries
        oxDb::getDb()->execute('delete from oxseo where oxtype != "static"');
        oxDb::getDb()->execute('delete from oxobject2seodata');
        oxDb::getDb()->execute('delete from oxseohistory');

        $this->cleanUpTable('oxcategories');

        parent::tearDown();
    }

    public function __SaveToDbCreatesGoodMd5Callback($sSQL)
    {
        $this->aSQL[] = $sSQL;
        if ($this->aRET && isset($this->aRET[count($this->aSQL) - 1])) {
            return $this->aRET[count($this->aSQL) - 1];
        }
    }

    /**
     * oxSeoEncoderManufacturer::_getAltUri() test case
     *
     * @return null
     */
    public function testGetAltUriTag()
    {
        oxTestModules::addFunction("oxmanufacturer", "loadInLang", "{ return true; }");

        $oEncoder = $this->getMock("oxSeoEncoderManufacturer", array("getManufacturerUri"));
        $oEncoder->expects($this->once())->method('getManufacturerUri')->will($this->returnValue("manufacturerUri"));

        $this->assertEquals("manufacturerUri", $oEncoder->UNITgetAltUri('1126', 0));
    }

    public function testGetManufacturerUrlExistingManufacturer()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        $sVndId = 'ee4948794e28d488cf1c8101e716a3f4';
        $sUrl = oxRegistry::getConfig()->getShopUrl() . 'Nach-Hersteller/Bush/';

        $oManufacturer = new oxManufacturer();
        $oManufacturer->load($sVndId);

        $oEncoder = new oxSeoEncoderManufacturer();
        $this->assertEquals($sUrl, $oEncoder->getManufacturerUrl($oManufacturer));
    }

    public function testGetManufacturerUrlExistingManufacturerEng()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction('oxManufacturer', 'resetRootManufacturer', '{ self::$_aRootManufacturer = array() ; }');
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->resetRootManufacturer();

        $sVndId = 'ee4948794e28d488cf1c8101e716a3f4';
        $sUrl = oxRegistry::getConfig()->getShopUrl() . 'en/By-Manufacturer/Bush/';

        $oManufacturer = new oxManufacturer();
        $oManufacturer->loadInLang(1, $sVndId);

        $oEncoder = new oxSeoEncoderManufacturer();
        $this->assertEquals($sUrl, $oEncoder->getManufacturerUrl($oManufacturer));
    }

    public function testGetManufacturerUrlExistingManufacturerWithLangParam()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        $sVndId = 'ee4948794e28d488cf1c8101e716a3f4';
        $sUrl = oxRegistry::getConfig()->getShopUrl() . 'Nach-Hersteller/Bush/';

        $oManufacturer = new oxManufacturer();
        $oManufacturer->loadInLang(1, $sVndId);

        $oEncoder = new oxSeoEncoderManufacturer();
        $this->assertEquals($sUrl, $oEncoder->getManufacturerUrl($oManufacturer, 0));
    }

    public function testGetManufacturerUrlExistingManufacturerEngWithLangParam()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction('oxManufacturer', 'resetRootManufacturer', '{ self::$_aRootManufacturer = array() ; }');
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->resetRootManufacturer();

        $sVndId = 'ee4948794e28d488cf1c8101e716a3f4';
        $sUrl = oxRegistry::getConfig()->getShopUrl() . 'en/By-Manufacturer/Bush/';

        $oManufacturer = new oxManufacturer();
        $oManufacturer->loadInLang(0, $sVndId);

        $oEncoder = new oxSeoEncoderManufacturer();
        $this->assertEquals($sUrl, $oEncoder->getManufacturerUrl($oManufacturer, 1));
    }

    /**
     * Testing Manufacturer uri getter
     */
    public function testGetManufacturerUriExistingManufacturer()
    {
        $oManufacturer = new oxManufacturer();
        $oManufacturer->setId('xxx');

        $oEncoder = $this->getMock('oxSeoEncoderManufacturer', array('_loadFromDb', '_prepareTitle'));
        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('oxmanufacturer'), $this->equalTo('xxx'), $this->equalTo($oManufacturer->getLanguage()))->will($this->returnValue('seourl'));
        $oEncoder->expects($this->never())->method('_prepareTitle');
        $oEncoder->expects($this->never())->method('_getUniqueSeoUrl');
        $oEncoder->expects($this->never())->method('_saveToDb');

        $sUrl = 'seourl';
        $sSeoUrl = $oEncoder->getManufacturerUri($oManufacturer);
        $this->assertEquals($sUrl, $sSeoUrl);
    }

    public function testGetManufacturerUriRootManufacturer()
    {
        $oManufacturer = new oxManufacturer();
        $oManufacturer->setId('root');
        $oManufacturer->oxmanufacturers__oxtitle = new oxField('root', oxField::T_RAW);

        $oEncoder = $this->getMock('oxSeoEncoderManufacturer', array('_saveToDb'));
        $oEncoder->expects($this->once())->method('_saveToDb')->with($this->equalTo('oxmanufacturer'), $this->equalTo('root'), $this->equalTo($oManufacturer->getStdLink()), $this->equalTo('root/'), $this->equalTo($oManufacturer->getLanguage()));

        $sUrl = 'root/';
        $sSeoUrl = $oEncoder->getManufacturerUri($oManufacturer);
        $this->assertEquals($sUrl, $sSeoUrl);
    }

    public function testGetManufacturerUriRootManufacturerSecondLanguage()
    {
        $oManufacturer = new oxManufacturer();
        $oManufacturer->setId('root');
        $oManufacturer->setLanguage(1);
        $oManufacturer->oxmanufacturers__oxtitle = new oxField('root', oxField::T_RAW);

        $oEncoder = $this->getMock('oxSeoEncoderManufacturer', array('_saveToDb'));
        $oEncoder->expects($this->once())
            ->method('_saveToDb')
            ->with(
                $this->equalTo('oxmanufacturer'),
                $this->equalTo('root'),
                $this->equalTo($oManufacturer->getBaseStdLink(1)),
                $this->equalTo('en/root/'),
                $this->equalTo($oManufacturer->getLanguage())
            );

        $sUrl = 'en/root/';
        $sSeoUrl = $oEncoder->getManufacturerUri($oManufacturer);
        $this->assertEquals($sUrl, $sSeoUrl);
    }

    public function testGetManufacturerUriNewManufacturer()
    {
        modOxManufacturerForoxSeoEncoderManufacturerTest::reSetRootManufacturer();

        $oManufacturer = new oxManufacturer();
        $oManufacturer->setLanguage(1);
        $oManufacturer->setId('xxx');
        $oManufacturer->oxmanufacturers__oxtitle = new oxField('xxx', oxField::T_RAW);

        $oEncoder = $this->getMock('oxSeoEncoderManufacturer', array('_loadFromDb', '_saveToDb'));
        $oEncoder->expects($this->exactly(2))->method('_loadFromDb')->will($this->returnValue(false));

        $sUrl = 'en/By-Manufacturer/xxx/';
        $sSeoUrl = $oEncoder->getManufacturerUri($oManufacturer);

        $this->assertEquals($sUrl, $sSeoUrl);
    }

    /**
     * Testing object url getter
     */
    public function testGetManufacturerPageUrl()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $sUrl = oxRegistry::getConfig()->getShopUrl() . 'en/By-Manufacturer/Bush/101/';
        $sVndId = 'ee4948794e28d488cf1c8101e716a3f4';

        $oManufacturer = new oxManufacturer();
        $oManufacturer->loadInLang(1, $sVndId);

        $oEncoder = new oxSeoEncoderManufacturer();
        $this->assertEquals($sUrl, $oEncoder->getManufacturerPageUrl($oManufacturer, 100));
    }

    public function testGetManufacturerPageUrlWithLangParam()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $sUrl = oxRegistry::getConfig()->getShopUrl() . 'en/By-Manufacturer/Bush/101/';
        $sVndId = 'ee4948794e28d488cf1c8101e716a3f4';

        $oManufacturer = new oxManufacturer();
        $oManufacturer->loadInLang(0, $sVndId);

        $oEncoder = new oxSeoEncoderManufacturer();
        $this->assertEquals($sUrl, $oEncoder->getManufacturerPageUrl($oManufacturer, 100, 1));
    }

    public function testGetManufacturerUrl()
    {
        $oManufacturer = $this->getMock('oxcategory', array('getLanguage'));
        $oManufacturer->expects($this->once())->method('getLanguage')->will($this->returnValue(0));

        $oEncoder = $this->getMock('oxSeoEncoderManufacturer', array('_getFullUrl', 'getManufacturerUri'));
        $oEncoder->expects($this->once())->method('_getFullUrl')->will($this->returnValue('seovndurl'));
        $oEncoder->expects($this->once())->method('getManufacturerUri');

        $this->assertEquals('seovndurl', $oEncoder->getManufacturerUrl($oManufacturer));
    }

    public function testGetManufacturerUriExistingManufacturerWithLangParam()
    {
        $oManufacturer = new oxManufacturer();
        $oManufacturer->setLanguage(1);
        $oManufacturer->setId('xxx');

        $oEncoder = $this->getMock('oxSeoEncoderManufacturer', array('_loadFromDb', '_prepareTitle'));
        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('oxmanufacturer'), $this->equalTo('xxx'), $this->equalTo(0))->will($this->returnValue('seourl'));
        $oEncoder->expects($this->never())->method('_prepareTitle');
        $oEncoder->expects($this->never())->method('_getUniqueSeoUrl');
        $oEncoder->expects($this->never())->method('_saveToDb');

        $sUrl = 'seourl';
        $sSeoUrl = $oEncoder->getManufacturerUri($oManufacturer, 0);
        $this->assertEquals($sUrl, $sSeoUrl);
    }

    public function testGetManufacturerUriRootManufacturerWithLangParam()
    {
        $oManufacturer = new oxManufacturer();
        $oManufacturer->setId('root');
        $oManufacturer->oxmanufacturers__oxtitle = new oxField('root', oxField::T_RAW);

        $oEncoder = $this->getMock('oxSeoEncoderManufacturer', array('_saveToDb'));
        $oEncoder->expects($this->once())
            ->method('_saveToDb')
            ->with(
                $this->equalTo('oxmanufacturer'),
                $this->equalTo('root'),
                $this->equalTo($oManufacturer->getBaseStdLink(1)),
                $this->equalTo('en/By-Manufacturer/'),
                $this->equalTo(1)
            );

        $sUrl = 'en/By-Manufacturer/';
        $sSeoUrl = $oEncoder->getManufacturerUri($oManufacturer, 1);
        $this->assertEquals($sUrl, $sSeoUrl);
    }

    public function testonDeleteManufacturer()
    {
        $sShopId = oxRegistry::getConfig()->getBaseShopId();
        $oDb = oxDb::getDb();
        $sQ = "insert into oxseo
                   ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxfixed, oxexpired, oxparams )
               values
                   ( 'oid', '132', '{$sShopId}', '0', '', '', 'oxmanufacturer', '0', '0', '' )";
        $oDb->execute($sQ);

        $sQ = "insert into oxobject2seodata ( oxobjectid, oxshopid, oxlang ) values ( 'oid', '{$sShopId}', '0' )";
        $oDb->execute($sQ);

        $this->assertTrue((bool) $oDb->getOne("select 1 from oxseo where oxobjectid = 'oid'"));
        $this->assertTrue((bool) $oDb->getOne("select 1 from oxobject2seodata where oxobjectid = 'oid'"));

        $oObj = new oxbase();
        $oObj->setId('oid');

        $oEncoder = new oxSeoEncoderManufacturer();
        $oEncoder->onDeleteManufacturer($oObj);

        $this->assertFalse((bool) $oDb->getOne("select 1 from oxseo where oxobjectid = 'oid'"));
        $this->assertFalse((bool) $oDb->getOne("select 1 from oxobject2seodata where oxobjectid = 'oid'"));
    }

}
