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
 * Testing oxseoencodertag class
 */
class Unit_Core_oxSeoEncoderTagTest extends OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        // deleting seo entries
        oxDb::getDb()->execute('delete from oxseo where oxtype != "static"');
        oxDb::getDb()->execute('delete from oxseohistory');

        parent::tearDown();
    }

    public function testGetTagUri()
    {
        $oEncoder = $this->getMock('oxSeoEncoderTag', array('_getDynamicTagUri', 'getStdTagUri'));
        $oEncoder->expects($this->once())->method('getStdTagUri')->will($this->returnValue('stdTagUri'));
        $oEncoder->expects($this->once())->method('_getDynamicTagUri')->with($this->equalTo('sTag'), $this->equalTo('stdTagUri'), "tag/sTag/", 999)->will($this->returnValue('seoTagUri'));

        $this->assertEquals('seoTagUri', $oEncoder->getTagUri('sTag', 999));
    }

    public function testGetStdTagUri()
    {
        $oEncoder = new oxSeoEncoderTag();
        $this->assertEquals("index.php?cl=tag&amp;searchtag=sTag", $oEncoder->getStdTagUri('sTag'));
    }

    public function testGetTagUrl()
    {
        $iLang = oxRegistry::getLang()->getBaseLanguage();
        $sTag = 'sTag';

        $oEncoder = $this->getMock('oxSeoEncoderTag', array('_getFullUrl', 'getTagUri'));
        $oEncoder->expects($this->once())->method('getTagUri')->with($this->equalTo($sTag), $this->equalTo($iLang))->will($this->returnValue('seoTagUri'));
        $oEncoder->expects($this->once())->method('_getFullUrl')->with($this->equalTo('seoTagUri'), $iLang)->will($this->returnValue('seoTagUrl'));

        $this->assertEquals('seoTagUrl', $oEncoder->getTagUrl($sTag));
    }

    public function testGetTagPageUrl()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        $sUrl = oxRegistry::getConfig()->getShopUrl(oxRegistry::getLang()->getBaseLanguage());

        $sTag = "erste";
        $sAltTag = "authentisches";

        $oSeoEncoderTag = new oxSeoEncoderTag();
        $this->assertEquals($sUrl . "tag/{$sTag}/16/", $oSeoEncoderTag->getTagPageUrl($sTag, 15));
        $this->assertEquals($sUrl . "tag/{$sTag}/16/", $oSeoEncoderTag->getTagPageUrl($sTag, 15));

        $this->assertEquals($sUrl . "tag/{$sAltTag}/14/", $oSeoEncoderTag->getTagPageUrl($sAltTag, 13));
    }

    /**
     * oxSeoEncoderTag::_getDynamicTagUri() test case
     *
     * @return null
     */
    public function testGetDynamicTagUriExistsInDb()
    {
        $oEncoder = $this->getMock("oxSeoEncoderTag", array("_trimUrl", "getDynamicObjectId", "_prepareUri", "_loadFromDb", "_copyToHistory", "_processSeoUrl", "_saveToDb"));
        $oEncoder->expects($this->once())->method('_trimUrl')->with($this->equalTo("testStdUrl"))->will($this->returnValue("testStdUrl"));
        $oEncoder->expects($this->once())->method('getDynamicObjectId')->with($this->equalTo(oxRegistry::getConfig()->getShopId()), $this->equalTo("testStdUrl"))->will($this->returnValue("testId"));
        $oEncoder->expects($this->once())->method('_prepareUri')->with($this->equalTo("testSeoUrl"))->will($this->returnValue("testSeoUrl"));
        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('dynamic'), $this->equalTo("testId"), $this->equalTo(0))->will($this->returnValue("testSeoUrl"));
        $oEncoder->expects($this->never())->method('_copyToHistory');
        $oEncoder->expects($this->never())->method('_processSeoUrl');
        $oEncoder->expects($this->never())->method('_saveToDb');

        $this->assertEquals("testSeoUrl", $oEncoder->UNITgetDynamicTagUri("testTag", "testStdUrl", "testSeoUrl", 0, "testId"));
    }

    public function testGetDynamicTagUriCreatingNew()
    {
        $sTag = "zauber";

        $sOxid = "1126";

        $oEncoder = $this->getMock("oxSeoEncoderTag", array("_trimUrl", "getDynamicObjectId", "_prepareUri", "_loadFromDb", "_copyToHistory", "_processSeoUrl", "_saveToDb"));
        $oEncoder->expects($this->once())->method('_trimUrl')->with($this->equalTo("testStdUrl"))->will($this->returnValue("testStdUrl"));
        $oEncoder->expects($this->once())->method('getDynamicObjectId')->with($this->equalTo(oxRegistry::getConfig()->getShopId()), $this->equalTo("testStdUrl"))->will($this->returnValue($sOxid));
        $oEncoder->expects($this->once())->method('_prepareUri')->with($this->equalTo("testSeoUrl"))->will($this->returnValue("testSeoUrl"));
        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('dynamic'), $this->equalTo($sOxid), $this->equalTo(0))->will($this->returnValue("testSeoUrl1"));
        $oEncoder->expects($this->once())->method('_copyToHistory')->with($this->equalTo($sOxid), $this->equalTo(oxRegistry::getConfig()->getShopId()), $this->equalTo(0), $this->equalTo('dynamic'));
        $oEncoder->expects($this->once())->method('_processSeoUrl')->with($this->equalTo("testSeoUrl"), $this->equalTo($sOxid), $this->equalTo(0))->will($this->returnValue("testSeoUrl"));
        $oEncoder->expects($this->once())->method('_saveToDb')->with($this->equalTo("dynamic"), $this->equalTo($sOxid), $this->equalTo("testStdUrl"), $this->equalTo('testSeoUrl'), $this->equalTo(0), $this->equalTo(oxRegistry::getConfig()->getShopId()));

        $this->assertEquals("testSeoUrl", $oEncoder->UNITgetDynamicTagUri($sTag, "testStdUrl", "testSeoUrl", 0, $sOxid));
    }

    public function testGetDynamicTagUriNoSuchTag()
    {
        $sOxid = "1126";

        $oEncoder = $this->getMock("oxSeoEncoderTag", array("_trimUrl", "getDynamicObjectId", "_prepareUri", "_loadFromDb", "_copyToHistory", "_processSeoUrl", "_saveToDb"));
        $oEncoder->expects($this->once())->method('_trimUrl')->with($this->equalTo("testStdUrl"))->will($this->returnValue("testStdUrl"));
        $oEncoder->expects($this->once())->method('getDynamicObjectId')->with($this->equalTo(oxRegistry::getConfig()->getShopId()), $this->equalTo("testStdUrl"))->will($this->returnValue($sOxid));
        $oEncoder->expects($this->once())->method('_prepareUri')->with($this->equalTo("testSeoUrl"))->will($this->returnValue("testSeoUrl"));
        $oEncoder->expects($this->once())->method('_loadFromDb')->with($this->equalTo('dynamic'), $this->equalTo($sOxid), $this->equalTo(0))->will($this->returnValue("testSeoUrl1"));
        $oEncoder->expects($this->once())->method('_copyToHistory')->with($this->equalTo($sOxid), $this->equalTo(oxRegistry::getConfig()->getShopId()), $this->equalTo(0), $this->equalTo('dynamic'));
        $oEncoder->expects($this->once())->method('_processSeoUrl')->with($this->equalTo("testSeoUrl"), $this->equalTo($sOxid), $this->equalTo(0))->will($this->returnValue("testSeoUrl"));
        $oEncoder->expects($this->once())->method('_saveToDb')->with($this->equalTo("dynamic"), $this->equalTo($sOxid), $this->equalTo("testStdUrl"), $this->equalTo('testSeoUrl'), $this->equalTo(0), $this->equalTo(oxRegistry::getConfig()->getShopId()));

        $this->assertEquals("testSeoUrl", $oEncoder->UNITgetDynamicTagUri("testTag", "testStdUrl", "testSeoUrl", 0, $sOxid));
    }

}
