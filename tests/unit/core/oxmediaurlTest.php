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

class Unit_Core_oxmediaurlTest extends OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $this->cleanUpTable('oxmediaurls');
        $sQ = "insert into oxmediaurls (oxid, oxobjectid, oxurl, oxdesc, oxisuploaded) values ('_test1', '1436', 'test.jpg', 'test1', 1)";
        oxDb::getDb()->execute($sQ);
        $sQ = "insert into oxmediaurls (oxid, oxobjectid, oxurl, oxdesc, oxisuploaded) values ('_test2', '1437', 'http://www.youtube.com/watch?v=ZN239G6aJZo', 'test2', 0)";
        oxDb::getDb()->execute($sQ);
        $sQ = "insert into oxmediaurls (oxid, oxobjectid, oxurl, oxdesc, oxisuploaded) values ('_test3', '1438', 'test.jpg', 'test3', 0)";
        oxDb::getDb()->execute($sQ);
        $sQ = "insert into oxmediaurls (oxid, oxobjectid, oxurl, oxdesc, oxisuploaded) values ('_test4', '1439', 'http://www.site.com/watch?v=ZN239G6aJZo', 'test4', 0)";
        oxDb::getDb()->execute($sQ);
        $sQ = "insert into oxmediaurls (oxid, oxobjectid, oxurl, oxdesc, oxisuploaded) values ('_test5', '1440', 'http://www.youtube.com/watch?v=GQ3AcPEPbH0&loop=1&rel=0', 'test5', 0)";
        oxDb::getDb()->execute($sQ);
        $sQ = "insert into oxmediaurls (oxid, oxobjectid, oxurl, oxdesc, oxisuploaded) values ('_test6', '1441', 'http://youtu.be/tRCwo6pSHnk', 'test6', 0)";
        oxDb::getDb()->execute($sQ);
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxmediaurls');
        $sFilePath = oxRegistry::getConfig()->getConfigParam('sShopDir') . '/out/media/test.jpg';
        if (file_exists($sFilePath)) {
            unlink($sFilePath);
        }

        return parent::tearDown();
    }

    public function testGetHtml()
    {
        $oCfg = $this->getMock('oxConfig', array('isSsl', 'getShopUrl', 'getSslShopUrl'));
        $oCfg->expects($this->any())->method('isSsl')->will($this->returnValue(0));
        $oCfg->expects($this->any())->method('getShopUrl')->will($this->returnValue('http://shop/'));
        $oCfg->expects($this->never())->method('getSslShopUrl')->will($this->returnValue('https://shop/'));

        $oMediaUrl = $this->getMock('oxMediaUrl', array('getConfig'), array(), '', false);
        $oMediaUrl->expects($this->any())->method('getConfig')->will($this->returnValue($oCfg));

        // uploaded file
        $oMediaUrl->oxmediaurls__oxurl = new oxField('test.jpg', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxdesc = new oxField('test1', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(1, oxField::T_RAW);
        $sExpt = '<a href="http://shop/out/media/test.jpg" target="_blank">test1</a>';
        $this->assertEquals($sExpt, $oMediaUrl->getHtml());

        // youtube link
        $oMediaUrl->oxmediaurls__oxurl = new oxField('http://www.youtube.com/watch?v=ZN239G6aJZo', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxdesc = new oxField('test2', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(0, oxField::T_RAW);
        $sExpt = 'test2<br><iframe width="425" height="344" src="http://www.youtube.com/embed/ZN239G6aJZo" frameborder="0" allowfullscreen></iframe>';
        $this->assertEquals($sExpt, $oMediaUrl->getHtml());

        // simple link
        $oMediaUrl->oxmediaurls__oxurl = new oxField('http://www.site.com/watch?v=ZN239G6aJZo', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxdesc = new oxField('test4', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(0, oxField::T_RAW);
        $sExpt = "<a href=\"http://www.site.com/watch?v=ZN239G6aJZo\" target=\"_blank\">test4</a>";
        $this->assertEquals($sExpt, $oMediaUrl->getHtml());

        // -- SSL ----------

        $oCfg = $this->getMock('oxConfig', array('isSsl', 'getShopUrl', 'getSslShopUrl'));
        $oCfg->expects($this->any())->method('isSsl')->will($this->returnValue(1));
        $oCfg->expects($this->never())->method('getShopUrl')->will($this->returnValue('http://shop/'));
        $oCfg->expects($this->any())->method('getSslShopUrl')->will($this->returnValue('https://shop/'));

        $oMediaUrl = $this->getMock('oxMediaUrl', array('getConfig'), array(), '', false);
        $oMediaUrl->expects($this->any())->method('getConfig')->will($this->returnValue($oCfg));

        // uploaded file
        $oMediaUrl->oxmediaurls__oxurl = new oxField('test.jpg', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxdesc = new oxField('test1', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(1, oxField::T_RAW);
        $sExpt = '<a href="https://shop/out/media/test.jpg" target="_blank">test1</a>';
        $this->assertEquals($sExpt, $oMediaUrl->getHtml());

        // youtube link
        $oMediaUrl->oxmediaurls__oxurl = new oxField('http://www.youtube.com/watch?v=ZN239G6aJZo', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxdesc = new oxField('test2', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(0, oxField::T_RAW);
        $sExpt = 'test2<br><iframe width="425" height="344" src="http://www.youtube.com/embed/ZN239G6aJZo" frameborder="0" allowfullscreen></iframe>';
        $this->assertEquals($sExpt, $oMediaUrl->getHtml());

        // simple link
        $oMediaUrl->oxmediaurls__oxurl = new oxField('http://www.site.com/watch?v=ZN239G6aJZo', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxdesc = new oxField('test4', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(0, oxField::T_RAW);
        $sExpt = "<a href=\"http://www.site.com/watch?v=ZN239G6aJZo\" target=\"_blank\">test4</a>";
        $this->assertEquals($sExpt, $oMediaUrl->getHtml());
    }

    public function testGetHtmlLink($blNewPage = false)
    {
        $oCfg = $this->getMock('oxConfig', array('isSsl', 'getShopUrl', 'getSslShopUrl'));
        $oCfg->expects($this->any())->method('isSsl')->will($this->returnValue(0));
        $oCfg->expects($this->any())->method('getShopUrl')->will($this->returnValue('http://shop/'));
        $oCfg->expects($this->never())->method('getSslShopUrl')->will($this->returnValue('https://shop/'));

        $oMediaUrl = $this->getMock('oxMediaUrl', array('getConfig'), array(), '', false);
        $oMediaUrl->expects($this->any())->method('getConfig')->will($this->returnValue($oCfg));

        // uploaded file
        $oMediaUrl->oxmediaurls__oxurl = new oxField('test.jpg', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxdesc = new oxField('test1', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(1, oxField::T_RAW);
        $sExpt = '<a href="http://shop/out/media/test.jpg" target="_blank">test1</a>';
        $this->assertEquals($sExpt, $oMediaUrl->getHtmlLink());

        // youtube link
        $oMediaUrl->oxmediaurls__oxurl = new oxField('http://www.youtube.com/watch?v=ZN239G6aJZo', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxdesc = new oxField('test2', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(0, oxField::T_RAW);
        $sExpt = '<a href="http://www.youtube.com/watch?v=ZN239G6aJZo" target="_blank">test2</a>';
        $this->assertEquals($sExpt, $oMediaUrl->getHtmlLink());

        // simple link
        $oMediaUrl->oxmediaurls__oxurl = new oxField('http://www.site.com/watch?v=ZN239G6aJZo', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxdesc = new oxField('test4', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(0, oxField::T_RAW);
        $sExpt = "<a href=\"http://www.site.com/watch?v=ZN239G6aJZo\" target=\"_blank\">test4</a>";
        $this->assertEquals($sExpt, $oMediaUrl->getHtmlLink());

        // -- SSL -------------------

        $oCfg = $this->getMock('oxConfig', array('isSsl', 'getShopUrl', 'getSslShopUrl'));
        $oCfg->expects($this->any())->method('isSsl')->will($this->returnValue(1));
        $oCfg->expects($this->never())->method('getShopUrl')->will($this->returnValue('http://shop/'));
        $oCfg->expects($this->any())->method('getSslShopUrl')->will($this->returnValue('https://shop/'));

        $oMediaUrl = $this->getMock('oxMediaUrl', array('getConfig'), array(), '', false);
        $oMediaUrl->expects($this->any())->method('getConfig')->will($this->returnValue($oCfg));

        // uploaded file
        $oMediaUrl->oxmediaurls__oxurl = new oxField('test.jpg', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxdesc = new oxField('test1', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(1, oxField::T_RAW);
        $sExpt = '<a href="https://shop/out/media/test.jpg" target="_blank">test1</a>';
        $this->assertEquals($sExpt, $oMediaUrl->getHtmlLink());

        // youtube link
        $oMediaUrl->oxmediaurls__oxurl = new oxField('http://www.youtube.com/watch?v=ZN239G6aJZo', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxdesc = new oxField('test2', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(0, oxField::T_RAW);
        $sExpt = '<a href="http://www.youtube.com/watch?v=ZN239G6aJZo" target="_blank">test2</a>';
        $this->assertEquals($sExpt, $oMediaUrl->getHtmlLink());

        // simple link
        $oMediaUrl->oxmediaurls__oxurl = new oxField('http://www.site.com/watch?v=ZN239G6aJZo', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxdesc = new oxField('test4', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(0, oxField::T_RAW);
        $sExpt = "<a href=\"http://www.site.com/watch?v=ZN239G6aJZo\" target=\"_blank\">test4</a>";
        $this->assertEquals($sExpt, $oMediaUrl->getHtmlLink());

    }

    public function testGetLink()
    {
        $sFilePath = oxRegistry::getConfig()->getConfigParam('sShopDir') . '/out/media/test.jpg';
        file_put_contents($sFilePath, 'test jpg file');
        $oCfg = $this->getMock('oxConfig', array('isSsl', 'getShopUrl', 'getSslShopUrl'));
        $oCfg->expects($this->any())->method('isSsl')->will($this->returnValue(0));
        $oCfg->expects($this->any())->method('getShopUrl')->will($this->returnValue('http://shop/'));
        $oCfg->expects($this->never())->method('getSslShopUrl')->will($this->returnValue('https://shop/'));

        $oMediaUrl = $this->getMock('oxMediaUrl', array('getConfig'), array(), '', false);
        $oMediaUrl->expects($this->any())->method('getConfig')->will($this->returnValue($oCfg));

        // uploaded file
        $oMediaUrl->oxmediaurls__oxurl = new oxField('test.jpg', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxdesc = new oxField('test1', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(0, oxField::T_RAW);
        $sExpt = 'test.jpg';
        $this->assertEquals($sExpt, $oMediaUrl->getLink());

        // youtube link
        $oMediaUrl->oxmediaurls__oxurl = new oxField('http://www.youtube.com/watch?v=ZN239G6aJZo', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxdesc = new oxField('test2', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(0, oxField::T_RAW);
        $sExpt = 'http://www.youtube.com/watch?v=ZN239G6aJZo';
        $this->assertEquals($sExpt, $oMediaUrl->getLink());

        // simple link
        $oMediaUrl->oxmediaurls__oxurl = new oxField('http://www.site.com/watch?v=ZN239G6aJZo', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxdesc = new oxField('test4', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(0, oxField::T_RAW);
        $sExpt = 'http://www.site.com/watch?v=ZN239G6aJZo';
        $this->assertEquals($sExpt, $oMediaUrl->getLink());

        // -- SSL -------------------

        $oCfg = $this->getMock('oxConfig', array('isSsl', 'getShopUrl', 'getSslShopUrl'));
        $oCfg->expects($this->any())->method('isSsl')->will($this->returnValue(1));
        $oCfg->expects($this->never())->method('getShopUrl')->will($this->returnValue('http://shop/'));
        $oCfg->expects($this->any())->method('getSslShopUrl')->will($this->returnValue('https://shop/'));

        $oMediaUrl = $this->getMock('oxMediaUrl', array('getConfig'), array(), '', false);
        $oMediaUrl->expects($this->any())->method('getConfig')->will($this->returnValue($oCfg));

        // uploaded file
        $oMediaUrl->oxmediaurls__oxurl = new oxField('test.jpg', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxdesc = new oxField('test1', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(1, oxField::T_RAW);
        $sExpt = 'https://shop/out/media/test.jpg';
        $this->assertEquals($sExpt, $oMediaUrl->getLink());

        // uploaded file with full url (#2444)
        $oMediaUrl->oxmediaurls__oxurl = new oxField('https://shop/out/media/test.jpg', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxdesc = new oxField('test1', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(1, oxField::T_RAW);
        $sExpt = 'https://shop/out/media/test.jpg';
        $this->assertEquals($sExpt, $oMediaUrl->getLink());

        // uploaded file with different url (#2444) is it ok so?
        $oMediaUrl->oxmediaurls__oxurl = new oxField('https://shop/out/mymedia/test.jpg', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxdesc = new oxField('test1', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(1, oxField::T_RAW);
        $sExpt = 'https://shop/out/media/test.jpg';
        $this->assertEquals($sExpt, $oMediaUrl->getLink());

        // youtube link
        $oMediaUrl->oxmediaurls__oxurl = new oxField('http://www.youtube.com/watch?v=ZN239G6aJZo', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxdesc = new oxField('test2', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(0, oxField::T_RAW);
        $sExpt = 'http://www.youtube.com/watch?v=ZN239G6aJZo';
        $this->assertEquals($sExpt, $oMediaUrl->getLink());

        // simple link
        $oMediaUrl->oxmediaurls__oxurl = new oxField('http://www.site.com/watch?v=ZN239G6aJZo', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxdesc = new oxField('test4', oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(0, oxField::T_RAW);
        $sExpt = 'http://www.site.com/watch?v=ZN239G6aJZo';
        $this->assertEquals($sExpt, $oMediaUrl->getLink());

    }

    public function testDeleteNonUploaded($sOXID = null)
    {
        $sFilePath = oxRegistry::getConfig()->getConfigParam('sShopDir') . '/out/media/test.jpg';
        file_put_contents($sFilePath, 'test jpg file');
        $oMediaUrl = new oxMediaUrl();
        $oMediaUrl->load('_test3');
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(false, oxField::T_RAW);
        $this->assertTrue(file_exists($sFilePath));
        $oMediaUrl->delete();
        $this->assertTrue(file_exists($sFilePath));
    }


    public function testDeleteUploaded($sOXID = null)
    {
        $sFilePath = oxRegistry::getConfig()->getConfigParam('sShopDir') . '/out/media/test.jpg';
        file_put_contents($sFilePath, 'test jpg file');
        $oMediaUrl = new oxMediaUrl();
        $oMediaUrl->load('_test3');
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(true, oxField::T_RAW);
        $this->assertTrue(file_exists($sFilePath));
        $oMediaUrl->delete();
        $this->assertFalse(file_exists($sFilePath));
    }

    public function testDeleteUploadedIfFullPathAdded($sOXID = null)
    {
        $sFilePath = oxRegistry::getConfig()->getConfigParam('sShopDir') . '/out/media/test.jpg';
        file_put_contents($sFilePath, 'test jpg file');
        $oMediaUrl = new oxMediaUrl();
        $oMediaUrl->load('_test3');
        $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(true, oxField::T_RAW);
        $oMediaUrl->oxmediaurls__oxurl = new oxField(modConfig::getInstance()->getShopUrl() . '/out/media/test.jpg', oxField::T_RAW);
        $this->assertTrue(file_exists($sFilePath));
        $oMediaUrl->delete();
        $this->assertFalse(file_exists($sFilePath));
    }

    public function testGetYoutubeHtml()
    {
        $oMediaUrl = $this->getProxyClass('oxMediaUrl');
        $oMediaUrl->load('_test2');
        $sExpt = 'test2<br><iframe width="425" height="344" src="http://www.youtube.com/embed/ZN239G6aJZo" frameborder="0" allowfullscreen></iframe>';
        $this->assertEquals($sExpt, $oMediaUrl->UNITgetYoutubeHtml());
    }

    public function testGetYoutubeHtmlWithParams()
    {
        $oMediaUrl = $this->getProxyClass('oxMediaUrl');
        $oMediaUrl->load('_test5');
        $sExpt = 'test5<br><iframe width="425" height="344" src="http://www.youtube.com/embed/GQ3AcPEPbH0?loop=1&amp;rel=0" frameborder="0" allowfullscreen></iframe>';
        $this->assertEquals($sExpt, $oMediaUrl->UNITgetYoutubeHtml());
    }

    public function testNewYoutubePattern()
    {
        $oMediaUrl = $this->getProxyClass('oxMediaUrl');
        $oMediaUrl->load('_test6');
        $sExpt = 'test6<br><iframe width="425" height="344" src="http://www.youtube.com/embed/tRCwo6pSHnk" frameborder="0" allowfullscreen></iframe>';
        $this->assertEquals($sExpt, $oMediaUrl->UNITgetYoutubeHtml());
    }

}
