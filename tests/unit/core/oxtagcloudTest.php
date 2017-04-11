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

class Unit_Core_oxTagCloudTest extends OxidTestCase
{

    /**
     * Test getting font size for tag
     */
    public function testGetFontSizeCustom()
    {
        $aTestData = array(
            "sTestTag1" => new oxTag("sTestTag1"),
            "sTestTag2" => new oxTag("sTestTag2"),
            "sTestTag3" => new oxTag("sTestTag3"),
            "sTestTag4" => new oxTag("sTestTag4"),
            "sTestTag5" => new oxTag("sTestTag5"),
            "sTestTag6" => new oxTag("sTestTag6"),
            "sTestTag7" => new oxTag("sTestTag7")
        );
        $aTestData["sTestTag1"]->setHitCount(20);
        $aTestData["sTestTag2"]->setHitCount(17);
        $aTestData["sTestTag3"]->setHitCount(13);
        $aTestData["sTestTag4"]->setHitCount(15);
        $aTestData["sTestTag5"]->setHitCount(12);
        $aTestData["sTestTag6"]->setHitCount(1);
        $aTestData["sTestTag7"]->setHitCount(5);

        $aTestResults = array(
            "sTestTag1" => 400,
            "sTestTag2" => 300,
            "sTestTag3" => 200,
            "sTestTag4" => 300,
            "sTestTag5" => 200,
            "sTestTag6" => 100,
            "sTestTag7" => 100
        );
        $oTagCloud = new oxTagCloud();
        $oTagCloud->setCloudArray($aTestData);

        foreach ($aTestResults as $sTag => $iVal) {
            $this->assertEquals($iVal, $oTagCloud->getTagSize($sTag));
        }
    }

    /* Should be tested by testGetFontSizeCustom
    public function testGetFontSize()
    {
        $oTagCloud = $this->getProxyClass('oxTagCloud');
        $this->assertEquals(285, $oTagCloud->UNITgetFontSize(10, 15));
        $this->assertEquals(250, $oTagCloud->UNITgetFontSize(10, 18));
        $this->assertEquals(700, $oTagCloud->UNITgetFontSize(18, 10));
    }
    public function testGetFontSizeExceptionalCases()
    {
        $oTagCloud = $this->getProxyClass('oxTagCloud');
        $this->assertEquals(OXTAGCLOUD_MINFONT, $oTagCloud->UNITgetFontSize(15, 2));
        $this->assertEquals(OXTAGCLOUD_MINFONT, $oTagCloud->UNITgetFontSize(15, 0));
        $this->assertEquals(OXTAGCLOUD_MINFONT, $oTagCloud->UNITgetFontSize(15, 1));
        $this->assertEquals(OXTAGCLOUD_MINFONT, $oTagCloud->UNITgetFontSize(-1, 10));
    }*/

    /**
     * Test formation of tagCloud array
     *
     * @return null
     */
    public function testGetCloudArray()
    {
        $oTagSet = new oxTagSet();
        $oTagSet->set("tag1,tag2");

        $oTagList = $this->getMock('oxtaglist', array('getCacheId', 'loadList', 'get'));
        $oTagList->expects($this->any())->method('getCacheId')->will($this->returnValue(null));
        $oTagList->expects($this->any())->method('loadList')->will($this->returnValue(true));
        $oTagList->expects($this->any())->method('get')->will($this->returnValue($oTagSet));

        $oTagCloud = new oxTagCloud();
        $oTagCloud->setTagList($oTagList);

        $aTags = array(
            "tag1" => new oxTag("tag1"),
            "tag2" => new oxTag("tag2"),
        );

        $this->assertEquals($aTags, $oTagCloud->getCloudArray());
    }

    /**
     * Test setting of extended mode
     */
    public function testSettingExtendedMode()
    {
        $oTagCloud = new oxTagCloud();

        $oTagCloud->setExtendedMode(true);
        $this->assertTrue($oTagCloud->isExtended());

        $oTagCloud->setExtendedMode(false);
        $this->assertFalse($oTagCloud->isExtended());
    }

    /**
     * Test tags cloud generating when extended mode is enabled or disabled
     */
    public function testGetCloudArrayInExtendedMode()
    {
        $oTagCloud = new oxTagCloud();
        $oTagCloud->setExtendedMode(true);
        $iMaxAmount = $oTagCloud->GetMaxAmount() + 10;

        $oTagSet = new oxTagSet();
        for ($i = 1; $i < $iMaxAmount; $i++) {
            $oTagSet->addTag('tag' . $i);
        }

        $oTagList = $this->getMock('oxtaglist', array('getCacheId', 'loadList', 'get'));
        $oTagList->expects($this->any())->method('getCacheId')->will($this->returnValue(null));
        $oTagList->expects($this->any())->method('loadList')->will($this->returnValue(true));
        $oTagList->expects($this->any())->method('get')->will($this->returnValue($oTagSet));

        $oTagCloud->setTagList($oTagList);

        // should be taken from db
        $this->assertEquals($oTagCloud->GetMaxAmount(), count($oTagCloud->getCloudArray()));

        $oTagCloud->setExtendedMode(false);

        $this->assertEquals($oTagCloud->GetMaxAmount(), count($oTagCloud->getCloudArray()));
    }

    /**
     * Test getting max articles amount
     */
    public function testGetMaxAmount()
    {
        $oTagCloud = new oxTagCloud();

        $oTagCloud->setExtendedMode(true);
        $this->assertEquals(OXTAGCLOUD_EXTENDEDCOUNT, $oTagCloud->GetMaxAmount());

        $oTagCloud->setExtendedMode(false);
        $this->assertEquals(OXTAGCLOUD_STARTPAGECOUNT, $oTagCloud->GetMaxAmount());
    }

    /**
     * Test setting and resetting cache of tagCloudArray
     */
    public function testTagCache()
    {
        $oTagSet = new oxTagSet();
        $oTagSet->add('tag1,tag2');

        $oTagList = $this->getMock('oxtaglist', array('getCacheId', 'loadList', 'get'));
        $oTagList->expects($this->any())->method('getCacheId')->will($this->returnValue("cacheId_1"));
        // Load list should be called first time and after reset
        $oTagList->expects($this->exactly(2))->method('loadList')->will($this->returnValue(true));
        $oTagList->expects($this->any())->method('get')->will($this->returnValue($oTagSet));

        $oTagCloud = new oxTagCloud();
        $oTagCloud->setTagList($oTagList);

        $aTags = array(
            "tag1" => new oxTag("tag1"),
            "tag2" => new oxTag("tag2"),
        );

        // should be taken from db
        $this->assertEquals($aTags, $oTagCloud->getCloudArray());

        // Set new oxTagCloud, to reset the local class caching
        $oTagCloud = new oxTagCloud();
        $oTagCloud->setTagList($oTagList);

        // should be taken from cache, loadList should not be called
        $this->assertEquals($aTags, $oTagCloud->getCloudArray());

        $oTagCloud->resetCache();

        // Set new oxTagCloud, to reset the local class caching
        $oTagCloud = new oxTagCloud();
        $oTagCloud->setTagList($oTagList);

        // should be taken from db again, because we resetted cache.
        $this->assertEquals($aTags, $oTagCloud->getCloudArray());
    }

    /**
     * Test not using cache when cacheId is null
     */
    public function testTagCacheWithCacheIdNull()
    {
        $oTagSet = new oxTagSet();
        $oTagSet->add('tag1,tag2');

        $oTagList = $this->getMock('oxTagList', array('getCacheId', 'loadList', 'get'));
        $oTagList->expects($this->any())->method('getCacheId')->will($this->returnValue(null));
        $oTagList->expects($this->any())->method('get')->will($this->returnValue($oTagSet));
        // Load list should be called all times, cache should not be used
        $oTagList->expects($this->exactly(2))->method('loadList')->will($this->returnValue(true));

        $oTagCloud = new oxTagCloud();
        $oTagCloud->setTagList($oTagList);

        $aTags = array(
            "tag1" => new oxTag("tag1"),
            "tag2" => new oxTag("tag2"),
        );

        // should be taken from db
        $this->assertEquals($aTags, $oTagCloud->getCloudArray());

        // Set new oxTagCloud, to reset the local class caching
        $oTagCloud = new oxTagCloud();
        $oTagCloud->setTagList($oTagList);

        // should be taken from cache, loadList should not be called
        $this->assertEquals($aTags, $oTagCloud->getCloudArray());
    }
}
