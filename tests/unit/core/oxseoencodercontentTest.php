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

/**
 * Testing oxseoencodercontent class
 */
class Unit_Core_oxSeoEncoderContentTest extends OxidTestCase
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
     * oxSeoEncoderContent::_getAltUri() test case
     *
     * @return null
     */
    public function testGetAltUriTag()
    {
        oxTestModules::addFunction("oxcontent", "loadInLang", "{ return true; }");

        $oEncoder = $this->getMock("oxSeoEncoderContent", array("getContentUri"));
        $oEncoder->expects($this->once())->method('getContentUri')->will($this->returnValue("contentUri"));

        $this->assertEquals("contentUri", $oEncoder->UNITgetAltUri('1126', 0));
    }

    /**
     * Content url getter tests
     */
    public function testGetContentUrlExisting()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $sStdUrl = 'cl=content';
        $sSeoUrl = 'content-title/';

        $oContent = new oxContent();
        $oContent->setLanguage(1);
        $oContent->setId('contentid');
        $oContent->oxcontents__oxtitle = new oxField('content title');


        $sShopId = oxRegistry::getConfig()->getBaseShopId();
        $iLang = 1;
        $sObjectId = $oContent->getId();
        $sIdent = md5(strtolower($sSeoUrl));;
        $sType = 'oxcontent';

        $sQ = "insert into oxseo (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype)
               values ('$sObjectId', '$sIdent', '$sShopId', '$iLang', '$sStdUrl', '$sSeoUrl', '$sType')";
        oxDb::getDb()->execute($sQ);

        $oEncoder = new oxSeoEncoderContent();

        $sUrl = oxRegistry::getConfig()->getShopUrl() . $sSeoUrl;
        $sSeoUrl = $oEncoder->getContentUrl($oContent);

        $this->assertEquals($sUrl, $sSeoUrl);
    }

    public function testGetContentUrlExistingWithLangParam()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $sStdUrl = 'cl=content';
        $sSeoUrl = 'content-title/';

        $oContent = new oxContent();
        $oContent->setLanguage(0);
        $oContent->setId('contentid');
        $oContent->oxcontents__oxtitle = new oxField('content title');


        $sShopId = oxRegistry::getConfig()->getBaseShopId();
        $iLang = 1;
        $sObjectId = $oContent->getId();
        $sIdent = md5(strtolower($sSeoUrl));;
        $sType = 'oxcontent';

        $sQ = "insert into oxseo (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype)
               values ('$sObjectId', '$sIdent', '$sShopId', '$iLang', '$sStdUrl', '$sSeoUrl', '$sType')";
        oxDb::getDb()->execute($sQ);

        $oEncoder = new oxSeoEncoderContent();

        $sUrl = oxRegistry::getConfig()->getShopUrl() . $sSeoUrl;
        $sSeoUrl = $oEncoder->getContentUrl($oContent, 1);

        $this->assertEquals($sUrl, $sSeoUrl);
    }

    /**
     * Test case for getting content for not existing url
     */
    public function testGetContentUrlNotExisting()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $oContent = new oxContent();
        $oContent->setId('contentid');
        $oContent->setTitle('content title');
        $oContent->setType(2);

        $oContent->setCategoryId('8a142c3e49b5a80c1.23676990 ');
        $sUrl = $this->getConfig()->getShopUrl() . 'Geschenke/content-title/';

        $oEncoder = new oxSeoEncoderContent();
        $sSeoUrl = $oEncoder->getContentUrl($oContent);

        $this->assertEquals($sUrl, $sSeoUrl);
    }

    // code call seq. check
    public function testGetContentUriExistingSeqCheck()
    {
        $oContent = $this->getMock('oxContent', array('getLanguage', 'getId'));
        $oContent->expects($this->once())->method('getLanguage')->will($this->returnValue(1));
        $oContent->expects($this->once())->method('getId')->will($this->returnValue('contentid'));

        $oEncoder = $this->getMock('oxSeoEncoderContent', array('_loadFromDb', '_prepareTitle', '_getUniqueSeoUrl', '_saveToDb'));
        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('oxContent'), $this->equalTo('contentid'), $this->equalTo(1))->will($this->returnValue('seocontenturl'));
        $oContent->expects($this->never())->method('_prepareTitle');
        $oContent->expects($this->never())->method('_getUniqueSeoUrl');
        $oContent->expects($this->never())->method('_saveToDb');

        $this->assertEquals('seocontenturl', $oEncoder->getContentUri($oContent));
    }

    public function testGetContentUriExistingSeqCheckWithLangParam()
    {
        $oContent = $this->getMock('oxContent', array('getLanguage', 'getId'));
        $oContent->expects($this->never())->method('getLanguage')->will($this->returnValue(1));
        $oContent->expects($this->once())->method('getId')->will($this->returnValue('contentid'));

        $oEncoder = $this->getMock('oxSeoEncoderContent', array('_loadFromDb', '_prepareTitle', '_getUniqueSeoUrl', '_saveToDb'));
        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('oxContent'), $this->equalTo('contentid'), $this->equalTo(0))->will($this->returnValue('seocontenturl'));
        $oContent->expects($this->never())->method('_prepareTitle');
        $oContent->expects($this->never())->method('_getUniqueSeoUrl');
        $oContent->expects($this->never())->method('_saveToDb');

        $this->assertEquals('seocontenturl', $oEncoder->getContentUri($oContent, 0));
    }

    public function testGetContentUriNotExistingSeqCheck()
    {
        $oContent = $this->getMock('oxContent', array('getLanguage', 'getId', 'getBaseStdLink', 'loadInLang'));
        $oContent->oxcontents__oxcatid = new oxField('xxx', oxField::T_RAW);
        $oContent->oxcontents__oxtitle = new oxField('content title', oxField::T_RAW);
        $oContent->expects($this->atLeastOnce())->method('getLanguage')->will($this->returnValue(0));
        $oContent->expects($this->exactly(3))->method('getId')->will($this->returnValue('contentid'));
        $oContent->expects($this->once())->method('getBaseStdLink')->will($this->returnValue('stdlink'));

        $oEncoder = $this->getMock('oxSeoEncoderContent', array('_loadFromDb', '_prepareTitle', '_processSeoUrl', '_saveToDb'));
        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('oxContent'), $this->equalTo('contentid'), $this->equalTo(0))->will($this->returnValue(false));
        $oEncoder->expects($this->once())->method('_prepareTitle')->with($this->equalTo('content title'))->will($this->returnValue('content-title'));
        $oEncoder->expects($this->once())->method('_processSeoUrl')->with($this->equalTo('content-title/'), $this->equalTo('contentid'), $this->equalTo(0))->will($this->returnValue('content-title/'));
        $oEncoder->expects($this->once())->method('_saveToDb')->with($this->equalTo('oxcontent'), $this->equalTo('contentid'), $this->equalTo('stdlink'), $this->equalTo('content-title/'), $this->equalTo(0));

        $this->assertEquals('content-title/', $oEncoder->getContentUri($oContent, 0));
    }

    public function testGetContentUriNotExistingSeqCheckChangeLang()
    {
        $oContent = $this->getMock('oxContent', array('getLanguage', 'getId', 'getBaseStdLink', 'loadInLang'));
        $oContent->oxcontents__oxcatid = new oxField('xxx', oxField::T_RAW);
        $oContent->oxcontents__oxtitle = new oxField('content title', oxField::T_RAW);
        $oContent->expects($this->once())->method('getLanguage')->will($this->returnValue(1));
        $oContent->expects($this->exactly(2))->method('getId')->will($this->returnValue('contentid'));
        $oContent->expects($this->never())->method('getBaseStdLink')->will($this->returnValue('stdlink'));

        oxTestModules::addFunction('oxcontent', 'getBaseStdLink( $iLang, $blAddId = true, $blFull = true )', '{return "stdlink";}');
        oxTestModules::addFunction('oxcontent', 'loadInLang($iLanguage, $sOxid)', '{$this->oxcontents__oxtitle = new oxField("content title - new");$this->oxcontents__oxcatid = new oxField("xxx");}');
        oxTestModules::addFunction('oxcontent', 'getId', '{return "contentid";}');

        $oEncoder = $this->getMock('oxSeoEncoderContent', array('_loadFromDb', '_prepareTitle', '_processSeoUrl', '_saveToDb'));
        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('oxContent'), $this->equalTo('contentid'), $this->equalTo(0))->will($this->returnValue(false));
        $oEncoder->expects($this->once())->method('_prepareTitle')->with($this->equalTo('content title - new'))->will($this->returnValue('content-title-new'));
        $oEncoder->expects($this->once())->method('_processSeoUrl')->with($this->equalTo('content-title-new/'), $this->equalTo('contentid'), $this->equalTo(0))->will($this->returnValue('content-title-new/'));
        $oEncoder->expects($this->once())->method('_saveToDb')->with($this->equalTo('oxcontent'), $this->equalTo('contentid'), $this->equalTo('stdlink'), $this->equalTo('content-title-new/'), $this->equalTo(0));

        $this->assertEquals('content-title-new/', $oEncoder->getContentUri($oContent, 0));
    }

    /**
     * Test case for method getContentUri when switching oxContent type from category to snippet to return correct URL
     */
    public function testGetContentUriSwitchingTypeFromCategoryToSnippetUrlIsCorrect()
    {
        $oEncoder = new oxSeoEncoderContent();

        $oContent = new oxContent();
        $oContent->setId('testcontent_cat_to_snippet');
        $oContent->setCategoryId('943202124f58e02e84bb228a9a2a9f1e');
        $oContent->setType(2);
        $oContent->setTitle('test_title');
        $oContent->save();

        $this->assertEquals('Eco-Fashion/test-title/', $oEncoder->getContentUri($oContent, 0, true));

        $oContent->setType(0);
        $oContent->save();

        $this->assertEquals('test-title/', $oEncoder->getContentUri($oContent, 0, true));
    }

    public function testonDeleteContent()
    {
        $sShopId = oxRegistry::getConfig()->getBaseShopId();
        $oDb = oxDb::getDb();
        $sQ = "insert into oxseo
                   ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxfixed, oxexpired, oxparams )
               values
                   ( 'oid', '132', '{$sShopId}', '0', '', '', 'oxcontent', '0', '0', '' )";
        $oDb->execute($sQ);

        $sQ = "insert into oxobject2seodata ( oxobjectid, oxshopid, oxlang ) values ( 'oid', '{$sShopId}', '0' )";
        $oDb->execute($sQ);

        $this->assertTrue((bool) $oDb->getOne("select 1 from oxseo where oxobjectid = 'oid'"));
        $this->assertTrue((bool) $oDb->getOne("select 1 from oxobject2seodata where oxobjectid = 'oid'"));

        $oEncoder = new oxSeoEncoderContent();
        $oEncoder->onDeleteContent('oid');

        $this->assertFalse((bool) $oDb->getOne("select 1 from oxseo where oxobjectid = 'oid'"));
        $this->assertFalse((bool) $oDb->getOne("select 1 from oxobject2seodata where oxobjectid = 'oid'"));
    }

}
