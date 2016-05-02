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

use \oxTag;

class TaglistTest extends \OxidTestCase
{

    /**
     * Test loading articles list and returning it
     */
    public function testLoadingAndGettingTagSet()
    {
        $oTagList = oxNew('oxTagList');
        $this->assertTrue($oTagList->loadList());
        $oTagSet = $oTagList->get();
        $aTags = $oTagSet->get();

        if ($this->getTestConfig()->getShopEdition() === 'EE') {
            $keyToCheck = 'otl';
            $tagsCount = 324;
        } else {
            $keyToCheck = 'fee';
            $tagsCount = 209;
        }

        $this->assertEquals($tagsCount, count($aTags));
        $this->assertTrue(array_key_exists($keyToCheck, $aTags));
    }

    /**
     * Test adding tags to list
     */
    public function testAddingTagsToList()
    {
        $oTagList = oxNew('oxTagList');
        $oTagList->addTag('testTag');
        $oTagSet = $oTagList->get();
        $aExpResult = array('testtag' => new oxTag('testTag'));

        $this->assertEquals($aExpResult, $oTagSet->get());
    }

    /**
     * Test usage of english language
     */
    public function testGetTagsEn()
    {
        $oTagList = oxNew('oxTagList');
        $oTagList->setLanguage(1);
        $oTagList->loadList();
        $oTagSet = $oTagList->get();

        $iExpt = $this->getTestConfig()->getShopEdition() === 'EE' ? 19 : 81;

        $this->assertEquals($iExpt, count($oTagSet->get()));
    }

    /**
     * Tests cache formation
     */
    public function testgetCacheId()
    {
        $oTagList = oxNew('oxTagList');
        $oTagList->setLanguage(1);
        $this->assertEquals('tag_list_1', $oTagList->getCacheId());
        $oTagList->setLanguage(2);
        $this->assertEquals('tag_list_2', $oTagList->getCacheId());
    }
}