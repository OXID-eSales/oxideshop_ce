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
 * Testing oxSeoEncoderManufacturer class
 */
class SeoEncoderManufacturerTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
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
        oxTestModules::addFunction("oxmanufacturer", "loadInLang", "{ return true; }");

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer::class, ["getManufacturerUri"]);
        $oEncoder->expects($this->once())->method('getManufacturerUri')->willReturn("manufacturerUri");

        $this->assertSame("manufacturerUri", $oEncoder->getAltUri('1126', 0));
    }

    public function testGetManufacturerUrlExistingManufacturer()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $vendorId = $this->getTestConfig()->getShopEdition() == 'EE' ? '2536d76675ebe5cb777411914a2fc8fb' : 'ee4948794e28d488cf1c8101e716a3f4';
        $link = $this->getTestConfig()->getShopEdition() == 'EE' ? 'Nach-Hersteller/Hersteller-2/' : 'Nach-Hersteller/Bush/';

        $manufacturer = oxNew('oxManufacturer');
        $manufacturer->load($vendorId);

        $encoder = oxNew('oxSeoEncoderManufacturer');
        $this->assertSame($this->getConfig()->getShopUrl() . $link, $encoder->getManufacturerUrl($manufacturer));
    }

    public function testGetManufacturerUrlExistingManufacturerEng()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction('oxManufacturer', 'resetRootManufacturer', '{ self::$_aRootManufacturer = array() ; }');

        $vendorId = $this->getTestConfig()->getShopEdition() == 'EE' ? '2536d76675ebe5cb777411914a2fc8fb' : 'ee4948794e28d488cf1c8101e716a3f4';
        $link = $this->getTestConfig()->getShopEdition() == 'EE' ? 'en/By-manufacturer/Manufacturer-2/' : 'en/By-manufacturer/Bush/';

        $manufacturer = oxNew('oxManufacturer');
        $manufacturer->resetRootManufacturer();

        $manufacturer = oxNew('oxManufacturer');
        $manufacturer->loadInLang(1, $vendorId);

        $encoder = oxNew('oxSeoEncoderManufacturer');
        $this->assertSame($this->getConfig()->getShopUrl() . $link, $encoder->getManufacturerUrl($manufacturer));
    }

    public function testGetManufacturerUrlExistingManufacturerWithLangParam()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $vendorId = $this->getTestConfig()->getShopEdition() == 'EE' ? '2536d76675ebe5cb777411914a2fc8fb' : 'ee4948794e28d488cf1c8101e716a3f4';
        $link = $this->getTestConfig()->getShopEdition() == 'EE' ? 'Nach-Hersteller/Hersteller-2/' : 'Nach-Hersteller/Bush/';

        $manufacturer = oxNew('oxManufacturer');
        $manufacturer->loadInLang(1, $vendorId);

        $encoder = oxNew('oxSeoEncoderManufacturer');
        $this->assertSame($this->getConfig()->getShopUrl() . $link, $encoder->getManufacturerUrl($manufacturer, 0));
    }

    public function testGetManufacturerUrlExistingManufacturerEngWithLangParam()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction('oxManufacturer', 'resetRootManufacturer', '{ self::$_aRootManufacturer = array() ; }');

        $vendorId = $this->getTestConfig()->getShopEdition() == 'EE' ? '2536d76675ebe5cb777411914a2fc8fb' : 'ee4948794e28d488cf1c8101e716a3f4';
        $link = $this->getTestConfig()->getShopEdition() == 'EE' ? 'en/By-manufacturer/Manufacturer-2/' : 'en/By-manufacturer/Bush/';

        $manufacturer = oxNew('oxManufacturer');
        $manufacturer->resetRootManufacturer();

        $manufacturer = oxNew('oxManufacturer');
        $manufacturer->loadInLang(0, $vendorId);

        $encoder = oxNew('oxSeoEncoderManufacturer');
        $this->assertSame($this->getConfig()->getShopUrl() . $link, $encoder->getManufacturerUrl($manufacturer, 1));
    }

    /**
     * Testing Manufacturer uri getter
     */
    public function testGetManufacturerUriExistingManufacturer()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId('xxx');

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer::class, ['loadFromDb', 'prepareTitle', 'getUniqueSeoUrl', 'saveToDb']);
        $oEncoder->expects($this->once())->method('loadFromDb')->with('oxmanufacturer', 'xxx', $oManufacturer->getLanguage())->willReturn('seourl');
        $oEncoder->expects($this->never())->method('prepareTitle');
        $oEncoder->expects($this->never())->method('getUniqueSeoUrl');
        $oEncoder->expects($this->never())->method('saveToDb');

        $sUrl = 'seourl';
        $sSeoUrl = $oEncoder->getManufacturerUri($oManufacturer);
        $this->assertSame($sUrl, $sSeoUrl);
    }

    public function testGetManufacturerUriRootManufacturer()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId('root');

        $oManufacturer->oxmanufacturers__oxtitle = new oxField('root', oxField::T_RAW);

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer::class, ['saveToDb']);
        $oEncoder->expects($this->once())->method('saveToDb')->with('oxmanufacturer', 'root', $oManufacturer->getStdLink(), 'root/', $oManufacturer->getLanguage());

        $sUrl = 'root/';
        $sSeoUrl = $oEncoder->getManufacturerUri($oManufacturer);
        $this->assertSame($sUrl, $sSeoUrl);
    }

    public function testGetManufacturerUriRootManufacturerSecondLanguage()
    {
        $manufacturer = oxNew('oxManufacturer');
        $manufacturer->setId('root');
        $manufacturer->setLanguage(1);

        $manufacturer->oxmanufacturers__oxtitle = new oxField('root', oxField::T_RAW);

        $encoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer::class, ['saveToDb']);
        $encoder->expects($this->once())
            ->method('saveToDb')
            ->with(
                'oxmanufacturer',
                'root',
                $manufacturer->getBaseStdLink(1),
                'en/root/',
                $manufacturer->getLanguage()
            );

        $link = 'en/root/';
        $seoUrl = $encoder->getManufacturerUri($manufacturer);
        $this->assertSame($link, $seoUrl);
    }

    public function testGetManufacturerUriNewManufacturer()
    {
        $manufacturer = oxNew('oxManufacturer');
        $manufacturer->setLanguage(1);
        $manufacturer->setId('xxx');

        $manufacturer->oxmanufacturers__oxtitle = new oxField('xxx', oxField::T_RAW);

        $encoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer::class, ['loadFromDb', 'saveToDb']);
        $encoder->expects($this->exactly(2))->method('loadFromDb')->willReturn(false);

        $link = 'en/By-manufacturer/xxx/';
        $seoUrl = $encoder->getManufacturerUri($manufacturer);

        $this->assertSame($link, $seoUrl);
    }

    /**
     * Testing object url getter
     */
    public function testGetManufacturerPageUrl()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $vendorId = $this->getTestConfig()->getShopEdition() == 'EE' ? '2536d76675ebe5cb777411914a2fc8fb' : 'ee4948794e28d488cf1c8101e716a3f4';
        $link = $this->getTestConfig()->getShopEdition() == 'EE' ? 'en/By-manufacturer/Manufacturer-2/?pgNr=100' : 'en/By-manufacturer/Bush/?pgNr=100';

        $manufacturer = oxNew('oxManufacturer');
        $manufacturer->loadInLang(1, $vendorId);

        $encoder = oxNew('oxSeoEncoderManufacturer');
        $this->assertSame($this->getConfig()->getShopUrl() . $link, $encoder->getManufacturerPageUrl($manufacturer, 100));
    }

    public function testGetManufacturerPageUrlWithLangParam()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $vendorId = $this->getTestConfig()->getShopEdition() == 'EE' ? '2536d76675ebe5cb777411914a2fc8fb' : 'ee4948794e28d488cf1c8101e716a3f4';
        $link = $this->getTestConfig()->getShopEdition() == 'EE' ? 'en/By-manufacturer/Manufacturer-2/?pgNr=100' : 'en/By-manufacturer/Bush/?pgNr=100';

        $manufacturer = oxNew('oxManufacturer');
        $manufacturer->loadInLang(0, $vendorId);

        $encoder = oxNew('oxSeoEncoderManufacturer');
        $this->assertSame($this->getConfig()->getShopUrl() . $link, $encoder->getManufacturerPageUrl($manufacturer, 100, 1));
    }

    public function testGetManufacturerUrl()
    {
        $manufacturer = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getLanguage']);
        $manufacturer->expects($this->once())->method('getLanguage')->willReturn(0);

        $encoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer::class, ['getFullUrl', 'getManufacturerUri']);
        $encoder->expects($this->once())->method('getFullUrl')->willReturn('seovndurl');
        $encoder->expects($this->once())->method('getManufacturerUri');

        $this->assertSame('seovndurl', $encoder->getManufacturerUrl($manufacturer));
    }

    public function testGetManufacturerUriExistingManufacturerWithLangParam()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setLanguage(1);
        $oManufacturer->setId('xxx');

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer::class, ['loadFromDb', 'prepareTitle', 'getUniqueSeoUrl', 'saveToDb']);
        $oEncoder->expects($this->once())->method('loadFromDb')->with('oxmanufacturer', 'xxx', 0)->willReturn('seourl');
        $oEncoder->expects($this->never())->method('prepareTitle');
        $oEncoder->expects($this->never())->method('getUniqueSeoUrl');
        $oEncoder->expects($this->never())->method('saveToDb');

        $sUrl = 'seourl';
        $sSeoUrl = $oEncoder->getManufacturerUri($oManufacturer, 0);
        $this->assertSame($sUrl, $sSeoUrl);
    }

    public function testGetManufacturerUriRootManufacturerWithLangParam()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId('root');

        $oManufacturer->oxmanufacturers__oxtitle = new oxField('root', oxField::T_RAW);

        $oEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer::class, ['saveToDb']);
        $oEncoder->expects($this->once())
            ->method('saveToDb')
            ->with(
                'oxmanufacturer',
                'root',
                $oManufacturer->getBaseStdLink(1),
                'en/By-manufacturer/',
                1
            );

        $sUrl = 'en/By-manufacturer/';
        $sSeoUrl = $oEncoder->getManufacturerUri($oManufacturer, 1);
        $this->assertSame($sUrl, $sSeoUrl);
    }

    public function testonDeleteManufacturer()
    {
        $sShopId = $this->getConfig()->getBaseShopId();
        $oDb = oxDb::getDb();
        $sQ = "insert into oxseo
                   ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxfixed, oxexpired, oxparams )
               values
                   ( 'oid', '132', '{$sShopId}', '0', '', '', 'oxmanufacturer', '0', '0', '' )";
        $oDb->execute($sQ);

        $sQ = sprintf("insert into oxobject2seodata ( oxobjectid, oxshopid, oxlang ) values ( 'oid', '%s', '0' )", $sShopId);
        $oDb->execute($sQ);

        $sQ = sprintf("insert into oxseohistory ( oxobjectid, oxident, oxshopid, oxlang ) values ( 'oid', '132', '%s', '0' )", $sShopId);
        $oDb->execute($sQ);

        $this->assertTrue((bool) $oDb->getOne("select 1 from oxseo where oxobjectid = 'oid'"));
        $this->assertTrue((bool) $oDb->getOne("select 1 from oxobject2seodata where oxobjectid = 'oid'"));
        $this->assertTrue((bool) $oDb->getOne("select 1 from oxseohistory where oxobjectid = 'oid'"));

        $oObj = oxNew('oxBase');
        $oObj->setId('oid');

        $oEncoder = oxNew('oxSeoEncoderManufacturer');
        $oEncoder->onDeleteManufacturer($oObj);

        $this->assertFalse((bool) $oDb->getOne("select 1 from oxseo where oxobjectid = 'oid'"));
        $this->assertFalse((bool) $oDb->getOne("select 1 from oxobject2seodata where oxobjectid = 'oid'"));
        $this->assertFalse((bool) $oDb->getOne("select 1 from oxseohistory where oxobjectid = 'oid'"));
    }
}
