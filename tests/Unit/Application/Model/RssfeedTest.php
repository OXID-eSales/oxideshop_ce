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
namespace Unit\Application\Model;

use \oxField;
use oxRssFeed;
use \stdClass;
use \oxList;
use \oxRegistry;
use \oxTestModules;

class RssfeedTest extends \OxidTestCase
{

    public function testGetChannel()
    {
        oxTestModules::addFunction('oxrssfeed', 'setChannel', '{$this->_aChannel = $aA[0];}');

        $o = oxNew('oxRssFeed');
        $o->setChannel('asd');
        $this->assertEquals('asd', $o->getChannel());
    }

    public function testLoadBaseChannel()
    {
        oxTestModules::addFunction('oxutilsurl', 'prepareUrlForNoSession', '{return $aA[0]."extra";}');
        oxTestModules::addFunction('oxlang', 'getBaseLanguage', '{return 1;}');
        oxTestModules::addFunction('oxlang', 'getLanguageIds', '{return array("aa", "bb");}');
        oxTestModules::publicize('oxrssfeed', '_loadBaseChannel');
        $oRss = oxNew('oxRssFeed');

        $oCfg = $this->getMock('oxconfig', array('getActiveShop', 'getShopUrl', 'getImageUrl'));
        $oShop = oxNew('oxShop');
        $oShop->oxshops__oxname = new oxField('name');
        $oShop->oxshops__oxversion = new oxField('oxversion');
        $oShop->oxshops__oxfname = new oxField('John');
        $oShop->oxshops__oxlname = new oxField('Doe');
        $oCfg->expects($this->any())->method('getActiveShop')->will($this->returnValue($oShop));
        $oCfg->expects($this->any())->method('getShopUrl')->will($this->returnValue("http://homeurl/"));
        $oCfg->expects($this->any())->method('getImageUrl')->will($this->returnValue("http://homeurl/lalala/"));

        $oRss->setConfig($oCfg);
        $oRss->p_loadBaseChannel();
        $edition = strtolower($this->getTestConfig()->getShopEdition());
        $expect = array(
            'title'       => 'name',
            'link'        => 'http://homeurl/extra',
            'description' => '',
            'language'    => 'bb',
            'copyright'   => 'name',
            'selflink'    => '',
            'generator'   => 'name',
            'image'       => array
            (
                'url'   => "http://homeurl/lalala/logo_$edition.png",
                'title' => 'name',
                'link'  => 'http://homeurl/extra',
            )
        );

        $this->assertEquals($expect, $oRss->getChannel());

        $oShop->oxshops__oxinfoemail = new oxField('emaiail.com');
        oxTestModules::addFunction('oxMailValidator', 'isValidEmail', '{return 0;}');
        $oRss->p_loadBaseChannel();
        $this->assertEquals($expect, $oRss->getChannel());

        oxTestModules::addFunction('oxMailValidator', 'isValidEmail', '{return 1;}');
        $oRss->p_loadBaseChannel();
        $expect['managingEditor'] = 'emaiail.com (John Doe)';
        $this->assertEquals($expect, $oRss->getChannel());
    }

    public function testGetCacheId()
    {

        oxTestModules::addFunction('oxlang', 'getBaseLanguage', '{return 4;}');
        oxTestModules::publicize('oxrssfeed', '_getCacheId');
        $oRss = oxNew('oxRssFeed');

        $this->assertEquals('asd_' . $this->getConfig()->getShopId() . '_4_0', $oRss->p_getCacheId('asd'));

        $this->setRequestParameter('currency', 1);
        $this->assertEquals('asd_' . $this->getConfig()->getShopId() . '_4_1', $oRss->p_getCacheId('asd'));
    }

    public function testLoadFromCache()
    {

        oxTestModules::addFunction('oxrssfeed', '_getCacheId', '{return $aA[0]."4";}');
        oxTestModules::addFunction('oxutils', 'fromFileCache', '{return false;}');
        oxTestModules::publicize('oxrssfeed', '_loadFromCache');
        $oRss = oxNew('oxRssFeed');

        $this->assertSame(false, $oRss->p_loadFromCache('asd'));

        oxTestModules::addFunction('oxutils', 'fromFileCache', '{return array("timestamp" => 0, "content" => $aA);}');
        $this->assertSame(false, $oRss->p_loadFromCache('asd'));

        oxTestModules::addFunction('oxutils', 'fromFileCache', '{return array("timestamp" => time()+1, "content" => $aA);}');
        $this->assertEquals(array('asd4'), $oRss->p_loadFromCache('asd'));
    }

    public function testGetLastBuildDate()
    {
        oxTestModules::addFunction('oxutils', 'fromFileCache', '{return false;}');
        oxTestModules::addFunction('oxrssfeed', '_getCacheId', '{return $aA[0]."id";}');
        oxTestModules::publicize('oxrssfeed', '_getLastBuildDate');
        $oRss = oxNew('oxRssFeed');

        $start = time();
        $got = $oRss->p_getLastBuildDate('asdasd', array());
        $this->assertTrue($this->_checkDate('D, d M Y H:i:s O', $start, time(), $got));

        oxTestModules::addFunction('oxutils', 'fromFileCache', '{return array("content" => array("lastBuildDate" => $aA[0], "asd"=>"a"));}');
        $start = time();
        $got = $oRss->p_getLastBuildDate('asd', array());
        $this->assertTrue($this->_checkDate('D, d M Y H:i:s O', $start, time(), $got));


        $this->assertEquals('asdid', $oRss->p_getLastBuildDate('asd', array("lastBuildDate" => 'asd', 'asd' => 'a')));
    }

    private function _checkDate($format, $timestart, $timeend, $got)
    {
        for ($t = $timestart; $t <= $timeend; $t++) {
            if (date($format, $t) == $got) {
                return true;
            }
        }

        return false;
    }

    public function testSaveToCache()
    {
        oxTestModules::addFunction('oxutils', 'toFileCache', '{return $aA;}');
        oxTestModules::addFunction('oxrssfeed', '_getCacheId', '{return $aA[0]."id";}');
        oxTestModules::publicize('oxrssfeed', '_saveToCache');

        $oRss = oxNew('oxRssFeed');

        $start = time();
        $res = $oRss->p_saveToCache('asd', 'content');
        $end = time();

        $this->assertGreaterThanOrEqual($start, $res[1]['timestamp']);
        $this->assertLessThanOrEqual($end, $res[1]['timestamp']);
        $this->assertEquals('content', $res[1]['content']);
        $this->assertEquals('asdid', $res[0]);

    }

    public function testGetArticleItems()
    {
        oxTestModules::addFunction('oxutilsurl', 'prepareUrlForNoSession', '{return $aA[0]."extra";}');
        $this->getConfig()->setConfigParam("bl_perfParseLongDescinSmarty", false);

        $oCfg = $this->getConfig();
        $oCfg->setConfigParam('aCurrencies', array('EUR@1.00@.@.@EUR@1'));
        $oRss = oxNew('oxRssFeed');
        $oRss->setConfig($oCfg);

        $oLongDesc = new stdClass();
        $oLongDesc->value = "artlogndesc";

        $oArt1 = $this->getMock('oxarticle', array("getLink", "getLongDescription"));
        $oArt1->expects($this->any())->method('getLink')->will($this->returnValue("artlink"));
        $oArt1->expects($this->any())->method('getLongDescription')->will($this->returnValue($oLongDesc));
        $oArt1->oxarticles__oxtitle = new oxField('title1');
        $oArt1->oxarticles__oxprice = new oxField(20);
        $oArt1->oxarticles__oxtimestamp = new oxField('2011-09-06 09:46:42');

        $oLongDesc2 = new stdClass();
        $oLongDesc2->value = " &nbsp;<div>";

        $oArt2 = $this->getMock('oxarticle', array("getLink", "getLongDescription"));
        $oArt2->expects($this->any())->method('getLink')->will($this->returnValue("artlink"));
        $oArt2->expects($this->any())->method('getLongDescription')->will($this->returnValue($oLongDesc2));
        $oArt2->oxarticles__oxtitle = new oxField('title2');
        $oArt2->oxarticles__oxprice = new oxField(10);
        $oArt2->oxarticles__oxshortdesc = new oxField('shortdesc');
        $oArt2->oxarticles__oxtimestamp = new oxField('2011-09-06 09:46:42');
        $oArr = oxNew('oxarticlelist');
        $oArr->assign(array($oArt1, $oArt2));

        $oSAr1 = new stdClass();
        $oSAr1->title = 'title1 20.0 EUR';
        $oSAr1->link = 'artlinkextra';
        $oSAr1->guid = 'artlinkextra';
        $oSAr1->isGuidPermalink = true;
        $oSAr1->description = "&lt;img src=&#039;" . $oArt1->getThumbnailUrl() . "&#039; border=0 align=&#039;left&#039; hspace=5&gt;artlogndesc";
        $oSAr1->date = "Tue, 06 Sep 2011 09:46:42 +0200";

        $oSAr2 = new stdClass();
        $oSAr2->title = 'title2 10.0 EUR';
        $oSAr2->link = 'artlinkextra';
        $oSAr2->guid = 'artlinkextra';
        $oSAr2->isGuidPermalink = true;
        $oSAr2->description = "&lt;img src=&#039;" . $oArt2->getThumbnailUrl() . "&#039; border=0 align=&#039;left&#039; hspace=5&gt;shortdesc";
        $oSAr2->date = "Tue, 06 Sep 2011 09:46:42 +0200";

        $this->assertEquals(array($oSAr1, $oSAr2), $oRss->UNITgetArticleItems($oArr));
    }

    public function testGetArticleItemsDescriptionParsedWithSmarty()
    {
        oxTestModules::addFunction('oxutilsurl', 'prepareUrlForNoSession', '{return $aA[0]."extra";}');
        $this->getConfig()->setConfigParam("bl_perfParseLongDescinSmarty", true);

        $oCfg = $this->getConfig();
        $oCfg->setConfigParam('aCurrencies', array('EUR@1.00@.@.@EUR@1'));
        $oRss = oxNew('oxRssFeed');
        $oRss->setConfig($oCfg);

        $oArt1 = $this->getMock('oxarticle', array("getLink", "getLongDesc"));
        $oArt1->expects($this->any())->method('getLink')->will($this->returnValue("artlink"));
        $oArt1->expects($this->any())->method('getLongDesc')->will($this->returnValue("artlogndesc"));
        $oArt1->oxarticles__oxtitle = new oxField('title1');
        $oArt1->oxarticles__oxprice = new oxField(20);
        $oArt1->oxarticles__oxtimestamp = new oxField('2011-09-06 09:46:42');

        $oArt2 = $this->getMock('oxarticle', array("getLink", "getLongDesc"));
        $oArt2->expects($this->any())->method('getLink')->will($this->returnValue("artlink"));
        $oArt2->expects($this->any())->method('getLongDesc')->will($this->returnValue(" &nbsp;<div>"));
        $oArt2->oxarticles__oxtitle = new oxField('title2');
        $oArt2->oxarticles__oxprice = new oxField(10);
        $oArt2->oxarticles__oxshortdesc = new oxField('shortdesc');
        $oArt2->oxarticles__oxtimestamp = new oxField('2011-09-06 09:46:42');
        $oArr = oxNew('oxarticlelist');
        $oArr->assign(array($oArt1, $oArt2));

        $oSAr1 = new stdClass();
        $oSAr1->title = 'title1 20.0 EUR';
        $oSAr1->link = 'artlinkextra';
        $oSAr1->guid = 'artlinkextra';
        $oSAr1->isGuidPermalink = true;
        $oSAr1->description = "&lt;img src=&#039;" . $oArt1->getThumbnailUrl() . "&#039; border=0 align=&#039;left&#039; hspace=5&gt;artlogndesc";
        $oSAr1->date = "Tue, 06 Sep 2011 09:46:42 +0200";

        $oSAr2 = new stdClass();
        $oSAr2->title = 'title2 10.0 EUR';
        $oSAr2->link = 'artlinkextra';
        $oSAr2->guid = 'artlinkextra';
        $oSAr2->isGuidPermalink = true;
        $oSAr2->description = "&lt;img src=&#039;" . $oArt2->getThumbnailUrl() . "&#039; border=0 align=&#039;left&#039; hspace=5&gt;shortdesc";
        $oSAr2->date = "Tue, 06 Sep 2011 09:46:42 +0200";

        $this->assertEquals(array($oSAr1, $oSAr2), $oRss->UNITgetArticleItems($oArr));
    }

    public function testGetArticleItemsWithNoArticlePrice()
    {
        oxTestModules::addFunction('oxutilsurl', 'prepareUrlForNoSession', '{return $aA[0]."extra";}');
        $this->getConfig()->setConfigParam("bl_perfParseLongDescinSmarty", false);

        $oActCur = new stdClass();
        $oActCur->decimal = 1;
        $oActCur->sign = 'EUR';

        $oCfg = $this->getMock('oxconfig', array('getActShopCurrencyObject'));
        $oCfg->expects($this->any())->method('getActShopCurrencyObject')->will($this->returnValue($oActCur));

        $oRss = oxNew('oxRssFeed');
        $oRss->setConfig($oCfg);

        $oLongDesc = new stdClass();
        $oLongDesc->value = "artlogndesc";

        $oArt1 = $this->getMock('oxarticle', array("getLink", "getLongDescription", "getPrice"));
        $oArt1->expects($this->any())->method('getLink')->will($this->returnValue("artlink"));
        $oArt1->expects($this->any())->method('getLongDescription')->will($this->returnValue($oLongDesc));
        $oArt1->expects($this->any())->method('getPrice')->will($this->returnValue(null));
        $oArt1->oxarticles__oxtitle = new oxField('title1');
        $oArt1->oxarticles__oxprice = new oxField(20);
        $oArt1->oxarticles__oxtimestamp = new oxField('2011-09-06 09:46:42');

        $oLongDesc2 = new stdClass();
        $oLongDesc2->value = " &nbsp;<div>";

        $oArt2 = $this->getMock('oxarticle', array("getLink", "getLongDescription", "getPrice"));
        $oArt2->expects($this->any())->method('getLink')->will($this->returnValue("artlink"));
        $oArt2->expects($this->any())->method('getLongDescription')->will($this->returnValue($oLongDesc2));
        $oArt2->expects($this->any())->method('getPrice')->will($this->returnValue(null));
        $oArt2->oxarticles__oxtitle = new oxField('title2');
        $oArt2->oxarticles__oxprice = new oxField(10);
        $oArt2->oxarticles__oxshortdesc = new oxField('shortdesc');
        $oArt2->oxarticles__oxtimestamp = new oxField('2011-09-06 09:46:42');

        $oArr = oxNew('oxarticlelist');
        $oArr->assign(array($oArt1, $oArt2));

        $oSAr1 = new stdClass();
        $oSAr1->title = 'title1';
        $oSAr1->link = 'artlinkextra';
        $oSAr1->guid = 'artlinkextra';
        $oSAr1->isGuidPermalink = true;
        $oSAr1->description = "&lt;img src=&#039;" . $oArt1->getThumbnailUrl() . "&#039; border=0 align=&#039;left&#039; hspace=5&gt;artlogndesc";
        $oSAr1->date = "Tue, 06 Sep 2011 09:46:42 +0200";

        $oSAr2 = new stdClass();
        $oSAr2->title = 'title2';
        $oSAr2->link = 'artlinkextra';
        $oSAr2->guid = 'artlinkextra';
        $oSAr2->isGuidPermalink = true;
        $oSAr2->description = "&lt;img src=&#039;" . $oArt2->getThumbnailUrl() . "&#039; border=0 align=&#039;left&#039; hspace=5&gt;shortdesc";
        $oSAr2->date = "Tue, 06 Sep 2011 09:46:42 +0200";

        $this->assertEquals(array($oSAr1, $oSAr2), $oRss->UNITgetArticleItems($oArr));
    }

    public function testGetArticleItemsDiffCurrency()
    {
        oxTestModules::addFunction('oxutilsurl', 'prepareUrlForNoSession', '{return $aA[0]."extra";}');
        $this->getConfig()->setConfigParam("bl_perfParseLongDescinSmarty", false);

        $oActCur = new stdClass();
        $oActCur->decimal = 1.47;
        $oActCur->dec = ',';
        $oActCur->thousand = '.';
        $oActCur->sign = '<small>CHF</small>';

        $oCfg = $this->getConfig();
        $oCfg->setConfigParam('aCurrencies', array('CHF@1.00@,@.@CHF@1'));

        $oRss = oxNew('oxRssFeed');
        $oRss->setConfig($oCfg);

        $oLongDesc = new stdClass();
        $oLongDesc->value = "artlogndesc";

        $oArt1 = $this->getMock('oxarticle', array("getLink", "getLongDescription"));
        $oArt1->expects($this->any())->method('getLink')->will($this->returnValue("artlink"));
        $oArt1->expects($this->any())->method('getLongDescription')->will($this->returnValue($oLongDesc));
        $oArt1->oxarticles__oxtitle = new oxField('title1');
        $oArt1->oxarticles__oxprice = new oxField(20);
        $oArt1->oxarticles__oxtimestamp = new oxField('2011-09-06 09:46:42');

        $oLongDesc2 = new stdClass();
        $oLongDesc2->value = " <div>";

        $oArt2 = $this->getMock('oxarticle', array("getLink", "getLongDescription"));
        $oArt2->expects($this->any())->method('getLink')->will($this->returnValue("artlink"));
        $oArt2->expects($this->any())->method('getLongDescription')->will($this->returnValue($oLongDesc2));
        $oArt2->oxarticles__oxtitle = new oxField('title2');
        $oArt2->oxarticles__oxprice = new oxField(10);
        $oArt2->oxarticles__oxshortdesc = new oxField('shortdesc');
        $oArt2->oxarticles__oxtimestamp = new oxField('2011-09-06 09:46:42');
        $oArr = oxNew('oxarticlelist');
        $oArr->assign(array($oArt1, $oArt2));

        $oSAr1 = new stdClass();
        $oSAr1->title = 'title1 20,0 CHF';
        $oSAr1->link = 'artlinkextra';
        $oSAr1->guid = 'artlinkextra';
        $oSAr1->isGuidPermalink = true;
        $oSAr1->description = "&lt;img src=&#039;" . $oArt1->getThumbnailUrl() . "&#039; border=0 align=&#039;left&#039; hspace=5&gt;artlogndesc";
        $oSAr1->date = "Tue, 06 Sep 2011 09:46:42 +0200";

        $oSAr2 = new stdClass();
        $oSAr2->title = 'title2 10,0 CHF';
        $oSAr2->link = 'artlinkextra';
        $oSAr2->guid = 'artlinkextra';
        $oSAr2->isGuidPermalink = true;
        $oSAr2->description = "&lt;img src=&#039;" . $oArt2->getThumbnailUrl() . "&#039; border=0 align=&#039;left&#039; hspace=5&gt;shortdesc";
        $oSAr2->date = "Tue, 06 Sep 2011 09:46:42 +0200";
        $this->assertEquals(array($oSAr1, $oSAr2), $oRss->UNITgetArticleItems($oArr));
    }

    public function testPrepareUrlSeoOff()
    {
        oxTestModules::addFunction('oxutilsurl', 'prepareUrlForNoSession', '{return $aA[0]."extra";}');
        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 1;}');
        oxTestModules::addFunction('oxutils', 'seoIsActive', '{return false;}');

        $oCfg = $this->getMock('oxconfig', array('getShopUrl'));
        $oCfg->expects($this->any())->method('getShopUrl')->will($this->returnValue('http://homeurl/?'));

        $oRss = oxNew('oxrssfeed');
        $oRss->setConfig($oCfg);
        $this->assertEquals('http://homeurl/?cl=rss&amp;fnc=topshop&amp;lang=1extra', $oRss->UNITprepareUrl('cl=rss&amp;fnc=topshop', 'asd'));
    }

    public function testPrepareUrlSeoOn()
    {
        oxTestModules::addFunction('oxutilsurl', 'prepareUrlForNoSession', '{return $aA[0]."extra";}');
        oxTestModules::addFunction('oxutils', 'seoIsActive', '{return true;}');
        oxTestModules::addFunction('oxLang', 'translateString', '{return $aA[0]."tr";}');
        oxTestModules::addFunction('oxLang', 'getLanguageIds', '{return array(1=>"as");}');
        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 1;}');
        oxTestModules::addFunction('oxSeoEncoder', 'getDynamicUrl', '{return $aA[0]." - SEO - ".$aA[1];}');

        $oCfg = $this->getMock('oxconfig', array('getShopUrl'));
        $oCfg->expects($this->any())->method('getShopUrl')->will($this->returnValue('http://homeurl/'));

        $oRss = $this->getMock('oxrssfeed', array('getConfig'));
        $oRss->expects($this->any())->method('getConfig')->will($this->returnValue($oCfg));

        oxRegistry::get("oxSeoEncoder")->setConfig($oCfg);

        $this->assertEquals('http://homeurl/?cl=rss&amp;fnc=topshop&amp;lang=1 - SEO - rss/asd/extra', $oRss->UNITprepareUrl('cl=rss&amp;fnc=topshop', 'asd'));
    }

    public function testPrepareFeedName()
    {
        $oRss = oxNew('oxRssFeed');

        $oCfg = $this->getMock('oxconfig', array('getActiveShop'));
        $oShop = oxNew('oxShop');
        $oShop->oxshops__oxname = new oxField('Test Shop');
        $oShop->oxshops__oxversion = new oxField('oxversion');
        $oCfg->expects($this->any())->method('getActiveShop')->will($this->returnValue($oShop));

        $oRss->setConfig($oCfg);
        $this->assertEquals('Test Shop/Test', $oRss->UNITprepareFeedName('Test'));
    }


    public function testGetShopUrl()
    {
        $oCfg = $this->getMock('oxconfig', array('getShopUrl'));
        $oCfg->expects($this->any())->method('getShopUrl')->will($this->returnValue("http://homeurl/?"));

        oxTestModules::publicize('oxrssfeed', '_getShopUrl');
        $oRss = oxNew('oxRssFeed');
        $oRss->setConfig($oCfg);

        $this->assertEquals('http://homeurl/?', $oRss->p_getShopUrl());

        $oCfg = $this->getMock('oxconfig', array('getShopUrl'));
        $oCfg->expects($this->any())->method('getShopUrl')->will($this->returnValue("http://homeurl/"));
        $oRss->setConfig($oCfg);
        $this->assertEquals('http://homeurl/?', $oRss->p_getShopUrl());

        $oCfg = $this->getMock('oxconfig', array('getShopUrl'));
        $oCfg->expects($this->any())->method('getShopUrl')->will($this->returnValue("http://homeurl/?sdf"));
        $oRss->setConfig($oCfg);
        $this->assertEquals('http://homeurl/?sdf&amp;', $oRss->p_getShopUrl());

        $oCfg = $this->getMock('oxconfig', array('getShopUrl'));
        $oCfg->expects($this->any())->method('getShopUrl')->will($this->returnValue("http://homeurl/?sdf&"));
        $oRss->setConfig($oCfg);
        $this->assertEquals('http://homeurl/?sdf&', $oRss->p_getShopUrl());

        $oCfg = $this->getMock('oxconfig', array('getShopUrl'));
        $oCfg->expects($this->any())->method('getShopUrl')->will($this->returnValue("http://homeurl/?sdf&amp;"));
        $oRss->setConfig($oCfg);
        $this->assertEquals('http://homeurl/?sdf&amp;', $oRss->p_getShopUrl());
    }

    public function testLoadData()
    {
        oxTestModules::addFunction('oxrssfeed', '_loadBaseChannel', '{ $this->_aChannel = array("basic"=>true); }');
        oxTestModules::addFunction('oxrssfeed', '_getLastBuildDate', '{ return $aA[0].$aA[1]."lastbd"; }');
        oxTestModules::addFunction('oxrssfeed', '_saveToDb', '{ $this->_aChannel["saved"] =$aA[0]; }');
        oxTestModules::publicize('oxrssfeed', '_loadData');

        $oRss = oxNew('oxRssFeed');
        $oRss->p_loadData('RSS_TopShop', 'topshoptitle', 'DESCRIPTION', 'loadtop5', 'topshopurl', 'targetlink');

        $aChannel = array(
            'basic'         => true,
            'selflink'      => 'topshopurl',
            'title'         => 'topshoptitle',
            'link'          => 'targetlink',
            'image'         => array(
                'link'        => 'targetlink',
                'title'       => 'topshoptitle',
                'description' => 'DESCRIPTION',
            ),
            'description'   => 'DESCRIPTION',
            'items'         => 'loadtop5',
            'lastBuildDate' => 'RSS_TopShopArraylastbd',
        );

        $this->assertEquals($aChannel, $oRss->getChannel());
    }

    public function testLoadDataIfEmptyTag()
    {
        oxTestModules::addFunction('oxrssfeed', '_loadBaseChannel', '{ $this->_aChannel = array("basic"=>true); }');
        oxTestModules::addFunction('oxrssfeed', '_saveToDb', '{ $this->_aChannel["saved"] =$aA[0]; }');
        oxTestModules::publicize('oxrssfeed', '_loadData');
        $iCurrTime = time();
        $this->setTime($iCurrTime);

        $oRss = oxNew('oxRssFeed');
        $oRss->p_loadData(null, 'topshoptitle', 'DESCRIPTION', 'loadtop5', 'topshopurl', 'targetlink');
        $now = date('D, d M Y H:i:s O', $iCurrTime);

        $aChannel = array(
            'basic'         => true,
            'selflink'      => 'topshopurl',
            'title'         => 'topshoptitle',
            'link'          => 'targetlink',
            'image'         => array(
                'link'        => 'targetlink',
                'title'       => 'topshoptitle',
                'description' => 'DESCRIPTION',
            ),
            'description'   => 'DESCRIPTION',
            'items'         => 'loadtop5',
            'lastBuildDate' => $now,
        );

        $this->assertEquals(
            $aChannel, $oRss->getChannel()
        );
    }

    public function testGetTopShopTitle()
    {
        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 1;}');
        oxTestModules::addFunction('oxLang', 'translateString', '{return $aA[0]."tr";}');
        $oRss = oxNew('oxRssFeed');
        $oCfg = $this->getMock('oxconfig', array('getActiveShop'));
        $oShop = oxNew('oxShop');
        $oShop->oxshops__oxname = new oxField('Test Shop');
        $oCfg->expects($this->any())->method('getActiveShop')->will($this->returnValue($oShop));
        $oRss->setConfig($oCfg);

        $this->assertEquals('Test Shop/TOP_OF_THE_SHOPtr', $oRss->getTopInShopTitle());
    }

    public function testGetTopInShopUrl()
    {
        oxTestModules::addFunction('oxrssfeed', '_prepareUrl', '{ return $aA; }');
        oxTestModules::addFunction('oxrssfeed', 'getTopInShopTitle', '{ return "topshoptitle"; }');
        $oRss = oxNew('oxRssFeed');
        $this->assertEquals(array("cl=rss&amp;fnc=topshop", "topshoptitle"), $oRss->getTopInShopUrl());
    }

    public function testLoadTopInShop()
    {
        oxTestModules::addFunction('oxrssfeed', '_loadFromCache', '{ return $aA; }');
        $oRss = oxNew('oxRssFeed');
        $oRss->loadTopInShop();
        $this->assertEquals(array('RSS_TopShop'), $oRss->getChannel());


        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 1;}');
        oxTestModules::addFunction('oxLang', 'translateString', '{return $aA[0]."tr";}');

        oxTestModules::addFunction('oxrssfeed', '_loadFromCache', '{ return false; }');
        oxTestModules::addFunction('oxrssfeed', '_loadData', '{ $this->_aChannel["data"] = $aA; }');
        oxTestModules::addFunction('oxrssfeed', 'getTopInShopUrl', '{ return "topshopurl"; }');
        oxTestModules::addFunction('oxrssfeed', 'getTopInShopTitle', '{ return "topshoptitle"; }');

        oxTestModules::addFunction('oxarticlelist', 'loadTop5Articles', '{ $this->load5 = "loadtop5"; }');
        oxTestModules::addFunction('oxrssfeed', '_getArticleItems', '{ return $aA[0]->load5; }');

        $oRss = oxNew('oxRssFeed');
        $oRss->loadTopInShop();

        $aChannel = array(
            'data' => array(
                '0' => 'RSS_TopShop',
                '1' => 'topshoptitle',
                '2' => 'TOP_SHOP_PRODUCTStr',
                '3' => 'loadtop5',
                '4' => 'topshopurl',
            )
        );

        $this->assertEquals($aChannel, $oRss->getChannel());
    }

    public function testGetNewestArticlesTitle()
    {
        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 1;}');
        oxTestModules::addFunction('oxLang', 'translateString', '{return $aA[0]."tr";}');
        $oRss = oxNew('oxRssFeed');
        $oCfg = $this->getMock('oxconfig', array('getActiveShop'));
        $oShop = oxNew('oxShop');
        $oShop->oxshops__oxname = new oxField('Test Shop');
        $oCfg->expects($this->any())->method('getActiveShop')->will($this->returnValue($oShop));
        $oRss->setConfig($oCfg);
        $this->assertEquals('Test Shop/NEWEST_SHOP_PRODUCTStr', $oRss->getNewestArticlesTitle());
    }

    public function testGeNewestArticlesUrl()
    {
        oxTestModules::addFunction('oxrssfeed', '_prepareUrl', '{ return $aA; }');
        oxTestModules::addFunction('oxrssfeed', 'getNewestArticlesTitle', '{ return "title"; }');
        $oRss = oxNew('oxRssFeed');
        $this->assertEquals(array("cl=rss&amp;fnc=newarts", "title"), $oRss->getNewestArticlesUrl());
    }

    public function testLoadNewestArticles()
    {
        oxTestModules::addFunction('oxrssfeed', '_loadFromCache', '{ return $aA; }');
        $oRss = oxNew('oxRssFeed');
        $oRss->loadNewestArticles();
        $this->assertEquals(array('RSS_NewArts'), $oRss->getChannel());

        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 1;}');
        oxTestModules::addFunction('oxLang', 'translateString', '{return $aA[0]."tr";}');

        oxTestModules::addFunction('oxrssfeed', '_loadFromCache', '{ return false; }');
        oxTestModules::addFunction('oxrssfeed', '_loadData', '{ $this->_aChannel["data"] = $aA; }');
        oxTestModules::addFunction('oxrssfeed', 'getNewestArticlesUrl', '{ return "surl"; }');
        oxTestModules::addFunction('oxrssfeed', 'getNewestArticlesTitle', '{ return "dastitle"; }');

        $this->getConfig()->setConfigParam('iRssItemsCount', 50);
        oxTestModules::addFunction('oxarticlelist', 'loadNewestArticles', '{ $this->load = "loaded".$aA[0]; }');
        oxTestModules::addFunction('oxrssfeed', '_getArticleItems', '{ return $aA[0]->load; }');

        $oRss = oxNew('oxRssFeed');
        $oRss->loadNewestArticles();

        $aChannel = array(
            'data' => array(
                '0' => 'RSS_NewArts',
                '1' => 'dastitle',
                '2' => 'NEWEST_SHOP_PRODUCTStr',
                '3' => 'loaded50',
                '4' => 'surl',
            )
        );

        $this->assertEquals($aChannel, $oRss->getChannel());
    }

    public function testGetCategoryArticlesTitle()
    {
        $sCatId = '8a142c3e44ea4e714.31136811';
        if ($this->getConfig()->getEdition() === 'EE') {
            $sCatId = '30e44ab83159266c7.83602558';
        }
        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 1;}');
        oxTestModules::addFunction('oxLang', 'translateString', '{return $aA[0];}');
        $oRss = oxNew('oxRssFeed');
        $sTitle = '';
        $sSep = '';
        $oCat = oxNew('oxcategory');
        $oCat->load($sCatId);
        while ($oCat) {
            // paruosti oCat title dali
            $sTitle = $oCat->oxcategories__oxtitle->value . $sSep . $sTitle;
            $sSep = '/';
            // load parent
            $oCat = $oCat->getParentCategory();
        }

        $oCfg = $this->getMock('oxconfig', array('getActiveShop'));
        $oShop = oxNew('oxShop');
        $oShop->oxshops__oxname = new oxField('Test Shop');
        $oCfg->expects($this->any())->method('getActiveShop')->will($this->returnValue($oShop));
        $oRss->setConfig($oCfg);

        $oCat = oxNew('oxcategory');
        $oCat->load($sCatId);
        $this->assertEquals('Test Shop/' . $sTitle . 'PRODUCTS', $oRss->getCategoryArticlesTitle($oCat));
    }

    public function testGetCategoryArticlesUrl()
    {
        oxTestModules::addFunction('oxrssfeed', '_prepareUrl', '{ return $aA; }');
        oxTestModules::addFunction('oxrssfeed', 'getCategoryArticlesTitle', '{ return "title"; }');
        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 4;}');
        oxTestModules::addFunction('oxLang', 'translateString', '{return $aA[0]."%s";}');
        $oRss = oxNew('oxRssFeed');
        $oCat = oxNew('oxcategory');
        $oCat->setId('ajai');
        $oCat->oxcategories__oxtitle = new oxField('tsss');
        $this->assertEquals(array("cl=rss&amp;fnc=catarts&amp;cat=ajai", "CATEGORY_PRODUCTS_Stsss"), $oRss->getCategoryArticlesUrl($oCat));
    }

    public function testLoadCategoryArticles()
    {
        oxTestModules::addFunction('oxrssfeed', '_loadFromCache', '{ return $aA; }');

        $oCat = $this->getMock('oxcategory', array("getLink"));
        $oCat->expects($this->any())->method('getLink')->will($this->returnValue("klnk"));
        $oCat->oxcategories__oxtitle = new oxField('tsss');
        $oCat->setId('ajai');

        $oRss = oxNew('oxRssFeed');
        $oRss->loadCategoryArticles($oCat);
        $this->assertEquals(array('RSS_CatArtsajai'), $oRss->getChannel());

        $this->getConfig()->setConfigParam('iRssItemsCount', 50);

        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 1;}');
        oxTestModules::addFunction('oxLang', 'translateString', '{return $aA[0]."tr";}');

        oxTestModules::addFunction('oxrssfeed', '_loadFromCache', '{ return false; }');
        oxTestModules::addFunction('oxrssfeed', '_loadData', '{ $this->_aChannel["data"] = $aA; }');
        oxTestModules::addFunction('oxrssfeed', 'getCategoryArticlesUrl', '{ return "surl"; }');
        oxTestModules::addFunction('oxrssfeed', 'getCategoryArticlesTitle', '{ return "dastitle"; }');
        oxTestModules::addFunction('oxrssfeed', '_getArticleItems', '{ return $aA[0]->load; }');

        oxTestModules::addFunction('oxarticlelist', 'loadCategoryArticles', '{ $this->load = "loaded".$aA[0].$aA[2]; }');

        $oRss = oxNew('oxRssFeed');
        $oRss->loadCategoryArticles($oCat);

        $aChannel = array(
            'data' => array(
                '0' => 'RSS_CatArtsajai',
                '1' => 'dastitle',
                '2' => 'S_CATEGORY_PRODUCTStr',
                '3' => 'loadedajai50',
                '4' => 'surl',
                '5' => 'klnk'
            )
        );

        $this->assertEquals(
            $aChannel, $oRss->getChannel()
        );
    }

    public function testGetObjectField()
    {
        oxTestModules::addFunction('oxBase', 'load', '{$this->a=new oxField($aA[0]);return 1;}');
        oxTestModules::publicize('oxrssfeed', '_getObjectField');

        $oRss = oxNew('oxRssFeed');
        $this->assertEquals('dd', $oRss->p_getObjectField('dd', 'oxbase', 'a'));

        $this->assertEquals('', $oRss->p_getObjectField('', 'oxbase', 'a'));

        oxTestModules::addFunction('oxBase', 'load', '{return 0;}');
        $this->assertEquals('', $oRss->p_getObjectField('dd', 'oxbase', 'a'));
    }

    public function testGetSearchParamsTranslation()
    {
        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 5;}');
        oxTestModules::addFunction('oxLang', 'translateString', '{return $aA[0]."%s";}');
        oxTestModules::addFunction('oxrssfeed', '_getObjectField', '{return $aA[0].$aA[1].$aA[2];}');
        oxTestModules::publicize('oxrssfeed', '_getSearchParamsTranslation');

        $oRss = oxNew('oxRssFeed');

        $this->assertEquals('asrch"', $oRss->p_getSearchParamsTranslation('a', 'srch"', 'cat', 'vend', 'man'));
        $this->assertEquals('CATEGORY_Scatoxcategoryoxcategories__oxtitleaVENDOR_Svendoxvendoroxvendor__oxtitlesrch', $oRss->p_getSearchParamsTranslation('<TAG_CATEGORY>a<TAG_VENDOR>', 'srch', 'cat', 'vend', 'man'));


        oxTestModules::addFunction('oxrssfeed', '_getObjectField', '{return "";}');
        $oRss = oxNew('oxRssFeed');
        $this->assertEquals('asrch', $oRss->p_getSearchParamsTranslation('<TAG_CATEGORY>a<TAG_VENDOR>', 'srch', 'cat', 'vend', 'man'));
    }

    public function testGetSearchArticlesTitle()
    {
        oxTestModules::addFunction('oxrssfeed', '_getSearchParamsTranslation', '{return $aA[0].$aA[1].$aA[2].$aA[3].$aA[4];}');

        $oRss = oxNew('oxRssFeed');
        $oCfg = $this->getMock('oxconfig', array('getActiveShop'));
        $oShop = oxNew('oxShop');
        $oShop->oxshops__oxname = new oxField('Test Shop');
        $oCfg->expects($this->any())->method('getActiveShop')->will($this->returnValue($oShop));
        $oRss->setConfig($oCfg);
        $this->assertEquals('Test Shop/SEARCH_FOR_PRODUCTS_CATEGORY_VENDOR_MANUFACTURERtssscatvendman', $oRss->getSearchArticlesTitle('tsss', 'cat', 'vend', 'man'));
    }

    public function testGetSearchParamsUrl()
    {
        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 5;}');
        oxTestModules::addFunction('oxLang', 'translateString', '{return $aA[0]."%s";}');
        oxTestModules::publicize('oxrssfeed', '_getSearchParamsUrl');
        $oRss = oxNew('oxRssFeed');
        $this->assertEquals('searchparam=a+a', $oRss->p_getSearchParamsUrl('a a', '', '', ''));
        $this->assertEquals('searchparam=+a&amp;searchcnid=b+', $oRss->p_getSearchParamsUrl(' a', 'b ', '', ''));
        $this->assertEquals('searchparam=+a&amp;searchcnid=b+&amp;searchvendor=+c', $oRss->p_getSearchParamsUrl(' a', 'b ', ' c', ''));
        $this->assertEquals('searchparam=+a&amp;searchvendor=+c', $oRss->p_getSearchParamsUrl(' a', '', ' c', ''));
        $this->assertEquals('searchparam=+a&amp;searchmanufacturer=+d+', $oRss->p_getSearchParamsUrl(' a', '', '', ' d '));
    }

    public function testGetSearchArticlesUrl()
    {
        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 5;}');
        oxTestModules::addFunction('oxLang', 'translateString', '{return $aA[0]."%s";}');
        oxTestModules::addFunction('oxrssfeed', '_prepareUrl', '{ return "url?".$aA[0].$aA[1]; }');
        oxTestModules::addFunction('oxrssfeed', 'getSearchArticlesTitle', '{ return "title"; }');
        oxTestModules::addFunction('oxrssfeed', '_getSearchParamsUrl', '{ return "|".$aA[0].$aA[1].$aA[2].$aA[3]."|"; }');
        $oRss = oxNew('oxRssFeed');
        $oCat = oxNew('oxcategory');
        $oCat->setId('ajai');
        $oCat->oxcategories__oxtitle = new oxField('tsss');
        $this->assertEquals("url?cl=rss&amp;fnc=searchartsSEARCH%s&amp;|a|", $oRss->getSearchArticlesUrl('a', '', '', ''));
        $this->assertEquals("url?cl=rss&amp;fnc=searchartsSEARCH%s&amp;|abcd|", $oRss->getSearchArticlesUrl('a', 'b', 'c', 'd'));
    }

    public function testGetSearchArticlesUrlWithParams()
    {
        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 5;}');
        oxTestModules::addFunction('oxLang', 'translateString', '{return $aA[0]."%s";}');
        oxTestModules::addFunction('oxrssfeed', '_prepareUrl', '{ return $aA[0].$aA[1]; }');
        oxTestModules::addFunction('oxrssfeed', 'getSearchArticlesTitle', '{ return "title"; }');
        oxTestModules::addFunction('oxrssfeed', '_getSearchParamsUrl', '{ return "|".$aA[0].$aA[1].$aA[2].$aA[3]."|"; }');
        $oRss = oxNew('oxRssFeed');
        $oCat = oxNew('oxcategory');
        $oCat->setId('ajai');
        $oCat->oxcategories__oxtitle = new oxField('tsss');
        $this->assertEquals("cl=rss&amp;fnc=searchartsSEARCH%s?|a|", $oRss->getSearchArticlesUrl('a', '', '', ''));
        $this->assertEquals("cl=rss&amp;fnc=searchartsSEARCH%s?|abcd|", $oRss->getSearchArticlesUrl('a', 'b', 'c', 'd'));
    }

    public function testLoadSearchArticles()
    {
        oxTestModules::addFunction('oxrssfeed', '_getSearchParamsUrl', '{ return "klnk"; }');
        $oConfig = $this->getConfig();
        $oRss = oxNew('oxRssFeed');
        $oRss->setConfig($oConfig);

        $this->getConfig()->setConfigParam('iRssItemsCount', 50);
        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 1;}');
        oxTestModules::addFunction('oxLang', 'translateString', '{return $aA[0]."tr";}');

        oxTestModules::addFunction('oxrssfeed', '_loadData', '{ $this->_aChannel["data"] = $aA; }');
        oxTestModules::addFunction('oxrssfeed', 'getSearchArticlesUrl', '{ return "surl"; }');
        oxTestModules::addFunction('oxrssfeed', 'getSearchArticlesTitle', '{ return "dastitle"; }');

        oxTestModules::addFunction(
            'oxsearch', 'getSearchArticles', '{
            $oArtList = oxNew("oxArticleList");
            $oArt = oxNew("oxArticle");
            $oArt->setId("loaded".$aA[0].$aA[1].$aA[2].$aA[3].$aA[4]);
            $oArtList->offsetSet(\'test_item\', $oArt);
            return $oArtList;
        }'
        );
        oxTestModules::addFunction('oxrssfeed', '_getArticleItems', '{ return $aA[0]; }');
        oxTestModules::addFunction('oxrssfeed', '_getShopUrl', '{ return "shopurl?"; }');

        $oRss = oxNew('oxRssFeed');
        $oRss->loadSearchArticles("AA", "BB", "CC", "DD");

        $oArtList = oxNew('oxArticleList');
        $oArt = oxNew('oxArticle');
        $oArt->setId('loadedAABBCCDD' . oxNew('oxArticle')->getViewName() . '.oxtimestamp desc');
        $oArtList->offsetSet('test_item', $oArt);

        $aChannel = array(
            'data' => array(
                '0' => null,
                '1' => 'dastitle',
                '2' => 'SEARCH_FOR_PRODUCTS_CATEGORY_VENDOR_MANUFACTURERtr',
                '3' => $oArtList, //'loadedAABBCCDD'.oxNew('oxArticle')->getViewName().'.oxtimestamp desc',
                '4' => 'surl',
                '5' => 'shopurl?cl=search&amp;klnk'
            )
        );

        $this->assertEquals($aChannel, $oRss->getChannel());
        $this->assertEquals(50, $this->getConfig()->getConfigParam('iNrofCatArticles'));
    }

    public function testGetRecommListItems()
    {
        oxTestModules::addFunction('oxutilsurl', 'prepareUrlForNoSession', '{return $aA[0]."extra";}');

        $oArt1 = $this->getMock('oxrecommlist', array("getLink"));
        $oArt1->expects($this->any())->method('getLink')->will($this->returnValue("rllink"));
        $oArt1->oxrecommlists__oxtitle = new oxField('title1');
        $oArt1->oxrecommlists__oxdesc = new oxField('desctitle1');

        $oArt2 = $this->getMock('oxrecommlist', array("getLink"));
        $oArt2->expects($this->any())->method('getLink')->will($this->returnValue("rllink"));
        $oArt2->oxrecommlists__oxtitle = new oxField('title2');
        $oArt2->oxrecommlists__oxdesc = new oxField('desctitle2');

        $oArr = oxNew('oxlist');
        $oArr->assign(array($oArt1, $oArt2));

        $oSAr1 = new stdClass();
        $oSAr1->title = 'title1';
        $oSAr1->link = 'rllinkextra';
        $oSAr1->guid = 'rllinkextra';
        $oSAr1->isGuidPermalink = true;
        $oSAr1->description = 'desctitle1';

        $oSAr2 = new stdClass();
        $oSAr2->title = 'title2';
        $oSAr2->link = 'rllinkextra';
        $oSAr2->guid = 'rllinkextra';
        $oSAr2->isGuidPermalink = true;
        $oSAr2->description = 'desctitle2';

        $oRss = oxNew('oxRssFeed');
        $this->assertEquals(array($oSAr1, $oSAr2), $oRss->UNITgetRecommListItems($oArr));
    }

    public function testGetRecommListsTitle()
    {
        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 1;}');
        oxTestModules::addFunction('oxLang', 'translateString', '{return $aA[0]."%s";}');
        $oRss = oxNew('oxRssFeed');
        $oCfg = $this->getMock('oxconfig', array('getActiveShop'));
        $oShop = oxNew('oxShop');
        $oShop->oxshops__oxname = new oxField('Test Shop');
        $oCfg->expects($this->any())->method('getActiveShop')->will($this->returnValue($oShop));
        $oRss->setConfig($oCfg);
        $oArt = oxNew('oxArticle');
        $oArt->oxarticles__oxtitle = new oxField('tsss');
        $this->assertEquals('Test Shop/LISTMANIA_LIST_FORtsss', $oRss->getRecommListsTitle($oArt));
    }

    /**
     * Test getRecommListsUrl method without Seo
     */
    public function testGetRecommListsUrlSeoOff()
    {
        $oLang = oxRegistry::getLang();
        $oLang->setBaseLanguage(1);
        oxRegistry::set('oxLang', $oLang);

        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('blSeoMode', false);
        $oConfig->setConfigParam('sShopURL', 'http://myshop/');

        $oRss = oxNew('oxRssFeed');

        $oArt = oxNew('oxArticle');
        $oArt->setId('ajai');
        $oArt->oxarticles__oxtitle = new oxField('tsss');
        $sCheckString = "http://myshop/?cl=rss&amp;fnc=recommlists&amp;anid=ajai&amp;lang=1";
        if ($this->getConfig()->getEdition() === 'EE') {
            $sCheckString .= "&amp;shp=1";
        }
        $this->assertEquals($sCheckString, $oRss->getRecommListsUrl($oArt));
    }

    /**
     * Test getRecommListsUrl method with Seo
     */
    public function testGetRecommListsUrlSeoOn()
    {
        $oLang = oxRegistry::getLang();
        $oLang->setBaseLanguage(1);
        oxRegistry::set('oxLang', $oLang);

        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('blSeoMode', true);
        $oConfig->setConfigParam('sShopURL', 'http://myshop/');

        $oRss = oxNew('oxRssFeed');

        $oArt = oxNew('oxArticle');
        $oArt->setId('ajai');
        $oArt->oxarticles__oxtitle = new oxField('tsss');

        $sCheckString = "http://myshop/en/rss/Listmania/tsss/";
        $this->assertEquals($sCheckString, $oRss->getRecommListsUrl($oArt));
    }

    public function testLoadRecommLists()
    {
        oxTestModules::addFunction('oxrssfeed', '_loadFromCache', '{ return $aA; }');

        $oArt = $this->getMock('oxarticle', array('getLink'));
        $oArt->expects($this->any())->method('getLink')->will($this->returnValue("klnk"));
        $oArt->setId('ajai');
        $oArt->oxarticles__oxtitle = new oxField('tsss');

        $oRss = oxNew('oxRssFeed');
        $oRss->loadRecommLists($oArt);
        $this->assertEquals(array('RSS_ARTRECOMMLISTSajai'), $oRss->getChannel());

        $this->getConfig()->setConfigParam('iRssItemsCount', 50);
        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 1;}');
        oxTestModules::addFunction('oxLang', 'translateString', '{return $aA[0]."tr";}');

        oxTestModules::addFunction('oxrssfeed', '_loadFromCache', '{ return false; }');
        oxTestModules::addFunction('oxrssfeed', '_loadData', '{ $this->_aChannel["data"] = $aA; }');
        oxTestModules::addFunction('oxrssfeed', 'getRecommListsUrl', '{ return "surl"; }');
        oxTestModules::addFunction('oxrssfeed', 'getRecommListsTitle', '{ return "dastitle"; }');

        oxTestModules::addFunction('oxrecommlist', 'getRecommListsByIds', '{ $o=new oxList();$o->load = "loaded".str_replace(array(" ", "\n"), "", var_export($aA, 1));return $o; }');
        oxTestModules::addFunction('oxrssfeed', '_getRecommListItems', '{ return $aA[0]->load; }');

        $oRss = oxNew('oxRssFeed');
        $oRss->loadRecommLists($oArt);

        $aChannel = array(
            'data' => array(
                '0' => 'RSS_ARTRECOMMLISTSajai',
                '1' => 'dastitle',
                '2' => 'LISTMANIA_LIST_FORtr',
                '3' => 'loadedarray(0=>array(0=>\'ajai\',),)',
                '4' => 'surl',
                '5' => 'klnk'
            )
        );

        $this->assertEquals(
            $aChannel, $oRss->getChannel()
        );
    }

    public function testLoadRecommListsIfListByIdsNotLoaded()
    {
        oxTestModules::addFunction('oxrssfeed', '_loadFromCache', '{ return $aA; }');

        $oArt = $this->getMock('oxarticle', array("getLink"));
        $oArt->expects($this->any())->method('getLink')->will($this->returnValue("klnk"));
        $oArt->setId('ajai');
        $oArt->oxarticles__oxtitle = new oxField('tsss');

        $oRss = oxNew('oxRssFeed');
        $oRss->loadRecommLists($oArt);
        $this->assertEquals(array('RSS_ARTRECOMMLISTSajai'), $oRss->getChannel());


        $this->getConfig()->setConfigParam('iRssItemsCount', 50);
        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 1;}');
        oxTestModules::addFunction('oxLang', 'translateString', '{return $aA[0]."tr";}');

        oxTestModules::addFunction('oxrssfeed', '_loadFromCache', '{ return false; }');
        oxTestModules::addFunction('oxrssfeed', '_loadData', '{ $this->_aChannel["data"] = $aA; }');
        oxTestModules::addFunction('oxrssfeed', 'getRecommListsUrl', '{ return "surl"; }');
        oxTestModules::addFunction('oxrssfeed', 'getRecommListsTitle', '{ return "dastitle"; }');

        oxTestModules::addFunction('oxrecommlist', 'getRecommListsByIds', '{ return null; }');
        oxTestModules::addFunction('oxrssfeed', '_getRecommListItems', '{ return $aA[0]->load; }');

        $oRss = oxNew('oxRssFeed');
        $oRss->loadRecommLists($oArt);

        $aChannel = array(
            'data' => array(
                '0' => 'RSS_ARTRECOMMLISTSajai',
                '1' => 'dastitle',
                '2' => 'LISTMANIA_LIST_FORtr',
                '3' => '',
                '4' => 'surl',
                '5' => 'klnk'
            )
        );

        $this->assertEquals($aChannel, $oRss->getChannel());
    }

    public function testRecommListArticlesTitle()
    {
        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 1;}');
        oxTestModules::addFunction('oxLang', 'translateString', '{return $aA[0]."%s";}');
        $oRss = oxNew('oxRssFeed');
        $oCfg = $this->getMock('oxconfig', array('getActiveShop'));
        $oShop = oxNew('oxShop');
        $oShop->oxshops__oxname = new oxField('Test Shop');
        $oCfg->expects($this->any())->method('getActiveShop')->will($this->returnValue($oShop));
        $oRss->setConfig($oCfg);
        $oRecommList = oxNew('oxRecommList');
        $oRecommList->oxrecommlists__oxtitle = new oxField('tsss');
        $this->assertEquals('Test Shop/LISTMANIA_LIST_PRODUCTStsss', $oRss->getRecommListArticlesTitle($oRecommList));
    }

    /**
     * Tests that getRecommListArticlesUrl return proper URL, with SEO turned off
     */
    public function testGetRecommListArticlesUrlSeoOff()
    {
        $oLang = oxRegistry::getLang();
        $oLang->setBaseLanguage(1);
        oxRegistry::set('oxLang', $oLang);

        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('blSeoMode', false);
        $oConfig->setConfigParam('sShopURL', 'http://myshop/');

        $oRss = oxNew('oxRssFeed');


        $oRecommList = oxNew('oxRecommList');
        $oRecommList->setId('ajai');
        $oRecommList->oxrecommlists__oxtitle = new oxField('tsss');
        $sCheckString = "http://myshop/?cl=rss&amp;fnc=recommlistarts&amp;recommid=ajai&amp;lang=1";
        if ($this->getConfig()->getEdition() === 'EE') {
            $sCheckString .= "&amp;shp=1";
        }
        $this->assertEquals($sCheckString, $oRss->getRecommListArticlesUrl($oRecommList));
    }

    /**
     * Tests that getRecommListArticlesUrl return proper URL, with SEO turned on
     */
    public function testGetRecommListArticlesUrlSeoOn()
    {
        $oLang = oxRegistry::getLang();
        $oLang->setBaseLanguage(1);
        oxRegistry::set('oxLang', $oLang);

        $oConfig = $this->getConfig();
        $oConfig->setConfigParam('blSeoMode', true);
        $oConfig->setConfigParam('sShopURL', 'http://myshop/');

        $oRss = oxNew('oxRssFeed');

        $oRecommList = oxNew('oxRecommList');
        $oRecommList->setId('ajai');
        $oRecommList->oxrecommlists__oxtitle = new oxField('tsssactive');
        $sCheckString = "http://myshop/en/rss/Listmania/tsssactive/";
        $this->assertEquals($sCheckString, $oRss->getRecommListArticlesUrl($oRecommList));
    }

    public function testLoadRecommListArticles()
    {
        oxTestModules::addFunction('oxrssfeed', '_loadFromCache', '{ return $aA; }');
        oxTestModules::addFunction('oxarticlelist', 'loadRecommArticles', '{ $this->load = "loadedarray()"; }');

        $oRecommList = $this->getMock('oxRecommList', array('getLink'));
        $oRecommList->expects($this->any())->method('getLink')->will($this->returnValue("klnk"));
        $oRecommList->setId('ajai');
        $oRecommList->oxrecommlists__oxtitle = new oxField('tsss');

        $oRss = oxNew('oxRssFeed');
        $oRss->loadRecommListArticles($oRecommList);
        $this->assertEquals(array('RSS_RECOMMLISTARTSajai'), $oRss->getChannel());

        $this->getConfig()->setConfigParam('iRssItemsCount', 50);
        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 1;}');
        oxTestModules::addFunction('oxLang', 'translateString', '{return $aA[0]."tr";}');

        oxTestModules::addFunction('oxrssfeed', '_loadFromCache', '{ return false; }');
        oxTestModules::addFunction('oxrssfeed', '_loadData', '{ $this->_aChannel["data"] = $aA; }');
        oxTestModules::addFunction('oxrssfeed', 'getRecommListArticlesUrl', '{ return "surl"; }');
        oxTestModules::addFunction('oxrssfeed', 'getRecommListArticlesTitle', '{ return "dastitle"; }');

        oxTestModules::addFunction('oxrssfeed', '_getArticleItems', '{ return $aA[0]->load; }');

        $oRss = oxNew('oxRssFeed');
        $oRss->loadRecommListArticles($oRecommList);

        $aChannel = array(
            'data' => array(
                '0' => 'RSS_RECOMMLISTARTSajai',
                '1' => 'dastitle',
                '2' => 'LISTMANIA_LIST_PRODUCTStr',
                '3' => 'loadedarray()',
                '4' => 'surl',
                '5' => 'klnk'
            )
        );

        $this->assertEquals(
            $aChannel, $oRss->getChannel()
        );
    }

    public function testGetBargainTitle()
    {
        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 1;}');
        oxTestModules::addFunction('oxLang', 'translateString', '{return $aA[0]."tr";}');
        $oRss = oxNew('oxRssFeed');
        $oCfg = $this->getMock('oxconfig', array('getActiveShop'));
        $oShop = oxNew('oxShop');
        $oShop->oxshops__oxname = new oxField('Test Shop');
        $oCfg->expects($this->any())->method('getActiveShop')->will($this->returnValue($oShop));
        $oRss->setConfig($oCfg);
        $this->assertEquals('Test Shop/BARGAINtr', $oRss->getBargainTitle());
    }

    public function testGetBargainUrl()
    {
        oxTestModules::addFunction('oxrssfeed', '_prepareUrl', '{ return $aA; }');
        oxTestModules::addFunction('oxrssfeed', 'getBargainTitle', '{ return "bargaintitle"; }');
        $oRss = oxNew('oxRssFeed');
        $this->assertEquals(array("cl=rss&amp;fnc=bargain", "bargaintitle"), $oRss->getBargainUrl());
    }

    public function testLoadBargainShop()
    {
        oxTestModules::addFunction('oxrssfeed', '_loadFromCache', '{ return $aA; }');
        $oRss = oxNew('oxRssFeed');
        $oRss->loadBargain();
        $this->assertEquals(array('RSS_Bargain'), $oRss->getChannel());

        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 1;}');
        oxTestModules::addFunction('oxLang', 'translateString', '{return $aA[0]."tr";}');

        oxTestModules::addFunction('oxrssfeed', '_loadFromCache', '{ return false; }');
        oxTestModules::addFunction('oxrssfeed', '_loadData', '{ $this->_aChannel["data"] = $aA; }');
        oxTestModules::addFunction('oxrssfeed', 'getBargainUrl', '{ return "bargainurl"; }');
        oxTestModules::addFunction('oxrssfeed', 'getBargainTitle', '{ return "bargaintitle"; }');

        oxTestModules::addFunction('oxarticlelist', 'loadActionArticles', '{ $this->load5 = "loadbargainart"; }');
        oxTestModules::addFunction('oxrssfeed', '_getArticleItems', '{ return $aA[0]->load5; }');

        $oRss = oxNew('oxRssFeed');
        $oRss->loadBargain();

        $aChannel = array(
            'data' => array(
                '0' => 'RSS_Bargain',
                '1' => 'bargaintitle',
                '2' => 'BARGAIN_PRODUCTStr',
                '3' => 'loadbargainart',
                '4' => 'bargainurl',
            )
        );

        $this->assertEquals($aChannel, $oRss->getChannel());
    }

    /**
     * Check, that the method 'mapOxActionToFileCache' gives back an empty string, if we give in an empty action name.
     */
    public function testMapOxActionToFileCacheEmptyOxAction()
    {
        $oRssFeed = oxNew('oxRssFeed');

        $sFilename = $oRssFeed->mapOxActionToFileCache('');

        $this->assertSame('', $sFilename);
    }

    /**
     * Check, that the method 'mapOxActionToFileCache' gives back the correct filename for oxnewest action.
     */
    public function testMapOxActionToFileCacheWithExampleOxAction()
    {
        $oRssFeed = oxNew('oxRssFeed');

        $sFilename = $oRssFeed->mapOxActionToFileCache('oxnewest');

        $this->assertSame(oxRssFeed::RSS_NEWARTS, $sFilename);
    }

    /**
     * Check, that the method 'removeCacheFile' calls the delete file method.
     */
    public function testRemoveCacheFileDelegates()
    {
        $oRssFeed = $this->getMockBuilder('oxRssFeed')
            ->setMethods(array('_deleteFile'))
            ->getMock();

        $oRssFeed->expects($this->once())->method('_deleteFile');

        $oRssFeed->removeCacheFile('oxnewest');
    }

}

