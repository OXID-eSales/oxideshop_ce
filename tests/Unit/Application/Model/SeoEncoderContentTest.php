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
 * Testing oxseoencodercontent class
 */
class SeoEncoderContentTest extends \OxidTestCase
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
     * oxSeoEncoderContent::_getAltUri() test case
     */
    public function testGetAltUriTag()
    {
        oxTestModules::addFunction("oxcontent", "loadInLang", "{ return true; }");

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderContent::class, array("getContentUri"));
        $oEncoder->expects($this->once())->method('getContentUri')->will($this->returnValue("contentUri"));

        $this->assertEquals("contentUri", $oEncoder->UNITgetAltUri('1126', 0));
    }

    /**
     * Content url getter tests
     */
    public function testGetContentUrlExisting()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $sStdUrl = 'cl=content';
        $sSeoUrl = 'content-title/';

        $oContent = oxNew('oxContent');
        $oContent->setLanguage(1);
        $oContent->setId('contentid');
        $oContent->oxcontents__oxtitle = new oxField('content title');


        $sShopId = $this->getConfig()->getBaseShopId();
        $iLang = 1;
        $sObjectId = $oContent->getId();
        $sIdent = md5(strtolower($sSeoUrl));
        $sType = 'oxcontent';

        $sQ = "insert into oxseo (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype)
               values ('$sObjectId', '$sIdent', '$sShopId', '$iLang', '$sStdUrl', '$sSeoUrl', '$sType')";
        oxDb::getDb()->execute($sQ);

        $oEncoder = oxNew('oxSeoEncoderContent');

        $sUrl = $this->getConfig()->getShopUrl() . $sSeoUrl;
        $sSeoUrl = $oEncoder->getContentUrl($oContent);

        $this->assertEquals($sUrl, $sSeoUrl);
    }

    public function testGetContentUrlExistingWithLangParam()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $sStdUrl = 'cl=content';
        $sSeoUrl = 'content-title/';

        $oContent = oxNew('oxContent');
        $oContent->setLanguage(0);
        $oContent->setId('contentid');
        $oContent->oxcontents__oxtitle = new oxField('content title');


        $sShopId = $this->getConfig()->getBaseShopId();
        $iLang = 1;
        $sObjectId = $oContent->getId();
        $sIdent = md5(strtolower($sSeoUrl));
        $sType = 'oxcontent';

        $sQ = "insert into oxseo (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype)
               values ('$sObjectId', '$sIdent', '$sShopId', '$iLang', '$sStdUrl', '$sSeoUrl', '$sType')";
        oxDb::getDb()->execute($sQ);

        $oEncoder = oxNew('oxSeoEncoderContent');

        $sUrl = $this->getConfig()->getShopUrl() . $sSeoUrl;
        $sSeoUrl = $oEncoder->getContentUrl($oContent, 1);

        $this->assertEquals($sUrl, $sSeoUrl);
    }

    /**
     * Test case for getting content for not existing url
     */
    public function testGetContentUrlNotExisting()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $content = oxNew('oxContent');
        $content->setId('contentid');
        $content->setTitle('content title');
        $content->setType(2);

        $categoryId = $this->getTestConfig()->getShopEdition() == 'EE' ? '30e44ab85808a1f05.26160932' : '8a142c3e49b5a80c1.23676990';
        $link = $this->getTestConfig()->getShopEdition() == 'EE' ? 'Wohnen/content-title/' : 'Geschenke/content-title/';

        $content->setCategoryId($categoryId);

        $encoder = oxNew('oxSeoEncoderContent');
        $seoUrl = $encoder->getContentUrl($content);

        $this->assertEquals($this->getConfig()->getShopUrl() . $link, $seoUrl);
    }

    // code call seq. check
    public function testGetContentUriExistingSeqCheck()
    {
        $oContent = $this->getMock(\OxidEsales\Eshop\Application\Model\Content::class, array('getLanguage', 'getId', '_prepareTitle', '_getUniqueSeoUrl', '_saveToDb'));
        $oContent->expects($this->once())->method('getLanguage')->will($this->returnValue(1));
        $oContent->expects($this->once())->method('getId')->will($this->returnValue('contentid'));

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderContent::class, array('_loadFromDb'));
        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('oxContent'), $this->equalTo('contentid'), $this->equalTo(1))->will($this->returnValue('seocontenturl'));
        $oContent->expects($this->never())->method('_prepareTitle');
        $oContent->expects($this->never())->method('_getUniqueSeoUrl');
        $oContent->expects($this->never())->method('_saveToDb');

        $this->assertEquals('seocontenturl', $oEncoder->getContentUri($oContent));
    }

    public function testGetContentUriExistingSeqCheckWithLangParam()
    {
        $oContent = $this->getMock(\OxidEsales\Eshop\Application\Model\Content::class, array('getLanguage', 'getId', '_prepareTitle', '_getUniqueSeoUrl', '_saveToDb'));
        $oContent->expects($this->never())->method('getLanguage')->will($this->returnValue(1));
        $oContent->expects($this->once())->method('getId')->will($this->returnValue('contentid'));

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderContent::class, array('_loadFromDb'));
        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('oxContent'), $this->equalTo('contentid'), $this->equalTo(0))->will($this->returnValue('seocontenturl'));
        $oContent->expects($this->never())->method('_prepareTitle');
        $oContent->expects($this->never())->method('_getUniqueSeoUrl');
        $oContent->expects($this->never())->method('_saveToDb');

        $this->assertEquals('seocontenturl', $oEncoder->getContentUri($oContent, 0));
    }

    public function testGetContentUriNotExistingSeqCheck()
    {
        $oContent = $this->getMock(\OxidEsales\Eshop\Application\Model\Content::class, array('getLanguage', 'getId', 'getBaseStdLink', 'loadInLang'));
        $oContent->oxcontents__oxcatid = new oxField('xxx', oxField::T_RAW);
        $oContent->oxcontents__oxtitle = new oxField('content title', oxField::T_RAW);
        $oContent->expects($this->atLeastOnce())->method('getLanguage')->will($this->returnValue(0));
        $oContent->expects($this->exactly(3))->method('getId')->will($this->returnValue('contentid'));
        $oContent->expects($this->once())->method('getBaseStdLink')->will($this->returnValue('stdlink'));

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderContent::class, array('_loadFromDb', '_prepareTitle', '_processSeoUrl', '_saveToDb'));
        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('oxContent'), $this->equalTo('contentid'), $this->equalTo(0))->will($this->returnValue(false));
        $oEncoder->expects($this->once())->method('_prepareTitle')->with($this->equalTo('content title'))->will($this->returnValue('content-title'));
        $oEncoder->expects($this->once())->method('_processSeoUrl')->with($this->equalTo('content-title/'), $this->equalTo('contentid'), $this->equalTo(0))->will($this->returnValue('content-title/'));
        $oEncoder->expects($this->once())->method('_saveToDb')->with($this->equalTo('oxcontent'), $this->equalTo('contentid'), $this->equalTo('stdlink'), $this->equalTo('content-title/'), $this->equalTo(0));

        $this->assertEquals('content-title/', $oEncoder->getContentUri($oContent, 0));
    }

    public function testGetContentUriNotExistingSeqCheckChangeLang()
    {
        $oContent = $this->getMock(\OxidEsales\Eshop\Application\Model\Content::class, array('getLanguage', 'getId', 'getBaseStdLink', 'loadInLang'));
        $oContent->oxcontents__oxcatid = new oxField('xxx', oxField::T_RAW);
        $oContent->oxcontents__oxtitle = new oxField('content title', oxField::T_RAW);
        $oContent->expects($this->once())->method('getLanguage')->will($this->returnValue(1));
        $oContent->expects($this->exactly(2))->method('getId')->will($this->returnValue('contentid'));
        $oContent->expects($this->never())->method('getBaseStdLink')->will($this->returnValue('stdlink'));

        oxTestModules::addFunction('oxcontent', 'getBaseStdLink( $iLang, $blAddId = true, $blFull = true )', '{return "stdlink";}');
        oxTestModules::addFunction('oxcontent', 'loadInLang($iLanguage, $sOxid)', '{$this->oxcontents__oxtitle = new oxField("content title - new");$this->oxcontents__oxcatid = new oxField("xxx");}');
        oxTestModules::addFunction('oxcontent', 'getId', '{return "contentid";}');

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderContent::class, array('_loadFromDb', '_prepareTitle', '_processSeoUrl', '_saveToDb'));
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
        $oEncoder = oxNew('oxSeoEncoderContent');

        $oContent = oxNew('oxContent');
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
        $sShopId = $this->getConfig()->getBaseShopId();
        $oDb = oxDb::getDb();
        $sQ = "insert into oxseo
                   ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxfixed, oxexpired, oxparams )
               values
                   ( 'oid', '132', '{$sShopId}', '0', '', '', 'oxcontent', '0', '0', '' )";
        $oDb->execute($sQ);

        $sQ = "insert into oxobject2seodata ( oxobjectid, oxshopid, oxlang ) values ( 'oid', '{$sShopId}', '0' )";
        $oDb->execute($sQ);

        $sQ = "insert into oxseohistory ( oxobjectid, oxident, oxshopid, oxlang ) values ( 'oid', '132', '{$sShopId}', '0' )";
        $oDb->execute($sQ);

        $this->assertTrue((bool) $oDb->getOne("select 1 from oxseo where oxobjectid = 'oid'"));
        $this->assertTrue((bool) $oDb->getOne("select 1 from oxobject2seodata where oxobjectid = 'oid'"));
        $this->assertTrue((bool) $oDb->getOne("select 1 from oxseohistory where oxobjectid = 'oid'"));

        $oEncoder = oxNew('oxSeoEncoderContent');
        $oEncoder->onDeleteContent('oid');

        $this->assertFalse((bool) $oDb->getOne("select 1 from oxseo where oxobjectid = 'oid'"));
        $this->assertFalse((bool) $oDb->getOne("select 1 from oxobject2seodata where oxobjectid = 'oid'"));
        $this->assertFalse((bool) $oDb->getOne("select 1 from oxseohistory where oxobjectid = 'oid'"));
    }
}
