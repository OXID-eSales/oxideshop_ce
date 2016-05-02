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

use \oxTagSet;
use \oxRegistry;

class ArticletaglistTest extends \OxidTestCase
{

    /**
     * Test setting and getting article id
     */
    public function testSetGetArticleId()
    {
        $oArticleTagList = oxNew('oxArticleTagList');
        $oArticleTagList->setArticleId("testArticle");
        $this->assertEquals("testArticle", $oArticleTagList->getArticleId());
    }

    /**
     * Test loading article tags with set article id
     */
    public function testLoadingArticleTagsWithSetArticleId()
    {
        $oArticleTagList = oxNew('oxArticleTagList');
        $oArticleTagList->load('1126');
        $aTags = $oArticleTagList->getArray();

        if ($this->getConfig()->getEdition() === 'EE') {
            $this->assertEquals(6, count($aTags));
            $this->assertTrue(array_key_exists("wild", $aTags));
        } else {
            $this->assertEquals(9, count($aTags));
            $this->assertTrue(array_key_exists("fee", $aTags));
        }
    }

    /**
     * Test loading in english language
     */
    public function testGetArticleTagsEn()
    {
        $oArticleTagList = oxNew('oxArticleTagList');
        $oArticleTagList->setLanguage(1);
        $oArticleTagList->load('2000');
        $oTagSet = $oArticleTagList->get();

        if ($this->getConfig()->getEdition() === 'EE') {
            $iExpt = 0;
        } else {
            $iExpt = 1;
        }
        $this->assertEquals($iExpt, count($oTagSet->get()));
    }

    /**
     * Test loading article tags with no article id set
     */
    public function testLoadingArticleTagsWithNoArticleId()
    {
        $oArticleTagList = oxNew('oxArticleTagList');
        $this->assertFalse($oArticleTagList->loadList());
        $this->assertEquals(new oxTagSet(), $oArticleTagList->get());
    }

    /**
     * Test setting and getting of tags.
     *
     * @return null
     */
    public function testSetGetTags()
    {
        $oArticleTagList = oxNew('oxArticleTagList');

        $sExpTags = "bier,zukunft,mehr,mithalten,edles";
        $oArticleTagList->set($sExpTags);

        $oxTagSet = oxNew('oxTagSet');
        $oxTagSet->set($sExpTags);

        $this->assertEquals($oxTagSet, $oArticleTagList->get());
    }

    /**
     * Test setting and getting array of tags.
     *
     * @return null
     */
    public function testSetGetTagsArray()
    {
        $oArticleTagList = oxNew('oxArticleTagList');

        $sExpTags = "bier,zukunft,mehr,mithalten,edles";
        $oArticleTagList->set($sExpTags);

        $oxTagSet = oxNew('oxTagSet');
        $oxTagSet->set($sExpTags);

        $this->assertEquals($oxTagSet->get(), $oArticleTagList->getArray());
    }

    /**
     * Test save tags with short tags
     */
    public function testSaveTags()
    {
        $oArticleTagList = oxNew('oxArticleTagList');
        $oArticleTagList->load("1126");
        $sOriginalTags = $oArticleTagList->get()->__toString();
        $oArticleTagList->addTag("testtag1");
        $oArticleTagList->addTag("a");
        $this->assertTrue($oArticleTagList->save());

        $oArticleTagList = oxNew('oxArticleTagList');
        $oArticleTagList->load("1126");
        $oTagList = $oArticleTagList->get();
        $aTags = $oTagList->get();

        if ($this->getConfig()->getEdition() === 'EE') {
            $this->assertEquals(8, count($aTags));
            $this->assertTrue(array_key_exists("testtag1", $aTags));
            $this->assertTrue(array_key_exists("a", $aTags));
        } else {
            $this->assertEquals(11, count($aTags));
            $this->assertTrue(array_key_exists("testtag1", $aTags));
            $this->assertTrue(array_key_exists("a", $aTags));
        }

        $oArticleTagList->set($sOriginalTags);
        $this->assertTrue($oArticleTagList->save());
    }

    /**
     * Test addition of tag.
     *
     * @return null
     */
    public function testAddTag()
    {
        $oArticleTagList = oxNew('oxArticleTagList');

        $oArticleTagList->set("test1");
        $oArticleTagList->addTag("test2");

        $oxTagSet = oxNew('oxTagSet');
        $oxTagSet->set("test1,test2");

        $this->assertEquals($oxTagSet, $oArticleTagList->get());
    }

    /**
     * Test add tag when tags is empty
     *
     * @return null
     */
    public function testAddTagForNewArt()
    {
        $oArticleTagList = oxNew('oxArticleTagList');
        $oTagSet = oxNew('oxTagSet');
        $this->assertEquals($oTagSet, $oArticleTagList->get());
        $oArticleTagList->addTag("tag1");
        $oTagSet->set("tag1");
        $this->assertEquals($oTagSet, $oArticleTagList->get());
    }

    /**
     * Test formation of single tags
     *
     * @return null
     */
    public function testFormationOfSingleTags()
    {
        $oArticleTagList = oxNew('oxArticleTagList');
        $this->assertEquals("", $oArticleTagList->get()->__toString());

        $oArticleTagList->addTag("tag1");
        $oArticleTagList->addTag("TAG2");
        $oArticleTagList->addTag(" tag3 ");
        $oArticleTagList->addTag("   ");
        $oArticleTagList->addTag("");
        $oArticleTagList->addTag("one sentence tag");
        $oArticleTagList->addTag(" one  sentence  tag ");
        $oArticleTagList->addTag("long testing string long testing string long testing string");

        $this->assertEquals("tag1,tag2,tag3,one sentence tag,one sentence tag,long testing string long testing string long testing string", $oArticleTagList->get()->__toString());
    }

    /**
     * Checks if time check if is article active checked
     */
    public function testGetTagsArticleTimeRange()
    {
        $blParam = $this->getConfig()->getConfigParam('blUseTimeCheck');
        $this->getConfig()->setConfigParam('blUseTimeCheck', 1);

        $oArticle = oxNew('oxArticle');
        $oArticle->load('1126');
        $oArticle->oxarticles__oxactive->value = 0;
        $oArticle->oxarticles__oxactivefrom->value = date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime() - 100);
        $oArticle->oxarticles__oxactiveto->value = date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime() + 100);
        $oArticle->save();

        $oArticleTagList = oxNew('oxArticleTagList');
        $oArticleTagList->load('1126');
        $oTagSet = $oArticleTagList->get();
        $aTags = $oTagSet->get();

        if ($this->getConfig()->getEdition() === 'EE') {
            $this->assertEquals(6, count($aTags));
            $this->assertTrue(array_key_exists('wild', $aTags));
        } else {
            $this->assertEquals(9, count($aTags));
            $this->assertTrue(array_key_exists('fee', $aTags));
        }

        $this->getConfig()->setConfigParam('blUseTimeCheck', $blParam);
        $oArticle->oxarticles__oxactive->value = 1;
        $oArticle->oxarticles__oxactivefrom->value = '0000-00-00 00:00:00';
        $oArticle->oxarticles__oxactiveto->value = '0000-00-00 00:00:00';
        $oArticle->save();
    }
}
