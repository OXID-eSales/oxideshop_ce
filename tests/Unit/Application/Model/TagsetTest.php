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

/**
 * Class TagsetTest
 *
 * @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
 *
 * @package Unit\Application\Model
 */
class TagsetTest extends \OxidTestCase
{

    /**
     * Test setting and getting separator
     */
    public function testSetGetSeparator()
    {
        $oTagSet = oxNew('oxTagSet');

        $oTagSet->setSeparator("|");
        $this->assertEquals("|", $oTagSet->getSeparator());
    }

    /**
     * Test if not default separator is used for tags generation
     */
    public function testNotDefaultSeparator()
    {
        $oTagSet = oxNew('oxTagSet');

        $oTagSet->setSeparator("|");

        $oTag1 = oxNew('oxTag');
        $oTag1->set("test1,test2");
        $oTag2 = oxNew('oxTag');
        $oTag2->set("test3,test4");

        $aTags = array(
            "test1,test2" => $oTag1,
            "test3,test4" => $oTag2,
        );

        $oTagSet->set($oTag1 . "|" . $oTag2);
        $this->assertEquals("test1,test2|test3,test4", $oTagSet->formString());
        $this->assertEquals($aTags, $oTagSet->get());
    }

    /**
     * Test setting and getting of tags set.
     *
     * @return null
     */
    public function testSetGet()
    {
        $oTagSet = oxNew('oxTagSet');

        $oTagSet->set("test1,test2,test3");

        $aTags = array(
            "test1" => new oxTag("test1"),
            "test2" => new oxTag("test2"),
            "test3" => new oxTag("test3"),
        );

        $this->assertEquals($aTags, $oTagSet->get());
    }

    /**
     * Test adding of multiple tags
     *
     * @return null
     */
    public function testAdd()
    {
        $oTagSet = oxNew('oxTagSet');
        $oTagSet->add("test1,test2");
        $oTagSet->add("test3,test4");

        $aTags = array(
            "test1" => new oxTag("test1"),
            "test2" => new oxTag("test2"),
            "test3" => new oxTag("test3"),
            "test4" => new oxTag("test4"),
        );

        $this->assertEquals($aTags, $oTagSet->get());
    }

    /**
     * Test adding of single tags
     *
     * @return null
     */
    public function testAddTag()
    {
        $oTagSet = oxNew('oxTagSet');

        $oTagSet->addTag("test1");
        $oTagSet->addTag("test2");

        $aTags = array(
            "test1" => new oxTag("test1"),
            "test2" => new oxTag("test2"),
        );

        $this->assertEquals($aTags, $oTagSet->get());
    }

    /**
     * Test adding of single not valid tags and checking if they get to invalid tags list
     *
     * @return null
     */
    public function testAddTagNotValid()
    {
        $oTagSet = oxNew('oxTagSet');

        // empty strings should not be stored in invalid tags list
        $oTagSet->addTag("");
        $oTagSet->addTag("admin");
        $oTagSet->addTag("validtag");

        $aTags = array("validtag" => new oxTag("validtag"));
        $aInvalidTags = array("admin" => new oxTag("admin"));

        $this->assertEquals($aTags, $oTagSet->get());
        $this->assertEquals($aInvalidTags, $oTagSet->getInvalidTags());
    }

    /**
     * Getting invalid tags when all tags were valid
     *
     * @return null
     */
    public function testInvalidTagsWithAllValidTags()
    {
        $oTagSet = oxNew('oxTagSet');
        $oTagSet->addTag("validtag");
        $this->assertEquals(array(), $oTagSet->getInvalidTags());
    }

    /**
     * Test adding tag as oxTag object
     *
     * @return null
     */
    public function testAddTagObject()
    {
        $oTagSet = oxNew('oxTagSet');

        $oTag1 = new oxTag("test1");
        $oTag2 = new oxTag("test2");

        $oTagSet->addTag($oTag1);
        $oTagSet->addTag($oTag2);

        $aTags = array(
            "test1" => $oTag1,
            "test2" => $oTag2,
        );

        $this->assertEquals($aTags, $oTagSet->get());
    }

    /**
     * Test adding multiple tags with repeating value
     *
     * @return null
     */
    public function testAddRepeatingTags()
    {
        $oTagSet = oxNew('oxTagSet');

        $oTagSet->set("test1,  test2  ,test1 ");
        $oTagSet->add("test2, test2 , test2");
        $oTagSet->addTag(" test1 ");
        $oTag1 = new oxTag("test1");
        $oTag1->setHitCount(3);
        $oTag2 = new oxTag("test2");
        $oTag2->setHitCount(4);
        $aTags = array("test1" => $oTag1, "test2" => $oTag2);

        $this->assertEquals($aTags, $oTagSet->get());
    }

    /**
     * Test adding of invalid tags
     *
     * @return null
     */
    public function testAddInvalidTags()
    {
        $oTagSet = oxNew('oxTagSet');

        $oTagSet->set("");
        $oTagSet->add(",,,,");
        $oTagSet->addTag("");

        $this->assertEquals(array(), $oTagSet->get());
    }

    /**
     * Test clearing of tags in set
     *
     * @return null
     */
    public function testClear()
    {
        $oTagSet = oxNew('oxTagSet');

        $oTagSet->set("test1,test2");
        $this->assertEquals("test1,test2", $oTagSet->formString());
        $oTagSet->clear();
        $this->assertEquals(array(), $oTagSet->get());
    }

    /**
     * Using tags set object as string should work
     *
     * @return null
     */
    public function testFormingTagsString()
    {
        $oTagSet = oxNew('oxTagSet');

        $oTagSet->set("test1, test2, test2  , test1 ");
        $this->assertEquals("test1,test1,test2,test2", $oTagSet->formString());
    }

    /**
     * Using tags set object as string should work
     *
     * @return null
     */
    public function testTagSetUsingAsString()
    {
        $oTagSet = oxNew('oxTagSet');

        $oTagSet->set("test1, test2, test2  , test1 ");
        $this->assertEquals("Result: test1,test1,test2,test2", 'Result: ' . $oTagSet);
    }

    /**
     * Testing tagset slicing
     *
     * @return null
     */
    public function testTagSetSlicing()
    {
        $oTagSet = oxNew('oxTagSet');

        $oTagSet->set("test1, test2, test2  , test1, test3 ");
        $oTagSet->slice(0, 2);

        $oTag1 = new oxTag("test1");
        $oTag1->setHitCount(2);
        $oTag2 = new oxTag("test2");
        $oTag2->setHitCount(2);
        $aTags = array("test1" => $oTag1, "test2" => $oTag2);

        $this->assertEquals($aTags, $oTagSet->get());
    }

    /**
     * Testing tagset sorting
     *
     * @return null
     */
    public function testTagSetSort()
    {
        $oTagSet = oxNew('oxTagSet');

        $oTagSet->set("btag,ctag,atag,1tag");
        $oTagSet->sort();

        $aTags = array(
            "1tag" => new oxTag('1tag'),
            "atag" => new oxTag('atag'),
            "btag" => new oxTag('btag'),
            "ctag" => new oxTag('ctag'),
        );

        $this->assertEquals($aTags, $oTagSet->get());
    }

    /**
     * Testing tagset sorting by tags hitcount
     *
     * @return null
     */
    public function testTagSetSortByHitCount()
    {
        $oTagSet = oxNew('oxTagSet');

        $oTagSet->set("atag,ctag,ctag,ctag,dtag,dtag,dtag,dtag,btag,btag");
        $oTagSet->sortByHitCount();

        $aTags = array(
            "dtag" => new oxTag('dtag'),
            "ctag" => new oxTag('ctag'),
            "btag" => new oxTag("btag"),
            "atag" => new oxTag("atag"),
        );
        $aTags["dtag"]->setHitCount(4);
        $aTags["ctag"]->setHitCount(3);
        $aTags["btag"]->setHitCount(2);
        $aTags["atag"]->setHitCount(1);

        $this->assertEquals($aTags, $oTagSet->get());
    }

    /**
     * Test implementation of ArrayAccess on oxTagSet
     */
    public function testIterator()
    {
        $oTagSet = oxNew('oxTagSet');
        $oTagSet->set("test1,test2");

        $aTags = array();
        foreach ($oTagSet as $iKey => $oTag) {
            $aTags[$iKey] = $oTag;
        }

        $this->assertEquals($aTags, $oTagSet->get());
    }
}