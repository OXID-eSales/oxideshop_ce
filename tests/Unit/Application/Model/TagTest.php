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
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version       OXID eShop CE
 */
namespace Unit\Application\Model;

use \oxTag;

/**
 * Class TagTest
 *
 * @deprecated v5.3 (2016-05-04); Tags will be moved to own module.
 *             
 * @package    Unit\Application\Model
 */
class TagTest extends \OxidTestCase
{

    /**
     * Test construction of tag
     *
     * @return null
     */
    public function testConstructing()
    {
        $oTag = oxNew('oxTag');
        $this->assertEquals("", $oTag->get());

        $oTag = new oxTag("test");
        $this->assertEquals("test", $oTag->get());
    }

    /**
     * Test setting max length, if tag length is equal to set max length
     * and correct value is returned when getting max length
     */
    public function testSetGetMaxLength()
    {
        $oTag = oxNew('oxTag');

        $iNewMaxLength = $oTag->getMaxLength() + 10;
        $oTag->setMaxLength($iNewMaxLength);

        $sRandomString = str_repeat("a", $iNewMaxLength + 10);
        $oTag->set($sRandomString);

        $this->assertEquals($iNewMaxLength, strlen($oTag->get()));
        $this->assertEquals($iNewMaxLength, $oTag->getMaxLength());
    }

    /**
     * Test setting and getting of tag hit count
     */
    public function testSetGetHitCount()
    {
        $oTag = oxNew('oxTag');
        $oTag->setHitCount(5);
        $this->assertEquals(5, $oTag->getHitCount());
    }

    /**
     * Test getting of default tag hit count
     */
    public function testGetHitCount()
    {
        $oTag = oxNew('oxTag');
        $this->assertEquals(1, $oTag->getHitCount());
    }

    /**
     * Test increasing of tag hit count on default value
     */
    public function testIncreaseHitCount()
    {
        $oTag = oxNew('oxTag');
        $oTag->increaseHitCount();
        $this->assertEquals(2, $oTag->getHitCount());
    }

    /**
     * Test increasing of tag hit count on default value
     */
    public function testIncreaseHitCountOnNotDefaultValue()
    {
        $oTag = oxNew('oxTag');
        $oTag->setHitCount(5);
        $oTag->increaseHitCount();
        $this->assertEquals(6, $oTag->getHitCount());
    }

    /**
     * Test setting and getting of tags.
     *
     * @return null
     */
    public function testSetGetTag()
    {
        $oTag = oxNew('oxTag');

        $sExpTags = "test";
        $oTag->set($sExpTags);

        $this->assertEquals($sExpTags, $oTag->get());
    }

    /**
     * Test tag validation
     */
    public function testIsValid()
    {
        $oTag = oxNew('oxTag');

        $oTag->set("");
        $this->assertFalse($oTag->isValid());

        // admin should be in forbidden words list
        $oTag->set("admin");
        $this->assertFalse($oTag->isValid());

        $oTag->set("testas");
        $this->assertTrue($oTag->isValid());
    }

    /**
     * Test tag link generation, when SEO is on
     *
     * @return null
     */
    public function testGetTagLinkSeoOn()
    {
        $this->getConfig()->setConfigParam('blSeoMode', true);

        $oTag = oxNew('oxTag');

        $oTag->set("zauber");
        $this->assertEquals($this->getConfig()->getConfigParam("sShopURL") . "tag/zauber/", $oTag->getLink());

        $oTag->set("testTag");
        $this->assertEquals($this->getConfig()->getConfigParam("sShopURL") . "tag/testtag/", $oTag->getLink());
    }

    /**
     * Test tag link generation, when SEO is off
     *
     * @return null
     */
    public function testGetTagLinkSeoOff()
    {
        $this->getConfig()->setConfigParam('blSeoMode', false);

        $oTag = oxNew('oxTag');

        $oTag->set("testTag");
        $this->assertEquals($this->getConfig()->getConfigParam("sShopURL") . "index.php?cl=tag&amp;searchtag=testtag&amp;lang=0", $oTag->getLink());
    }

    /**
     * oxTag::getTagTitle() test case
     *
     * @return null
     */
    public function testGetTagTitle()
    {
        $oTag = oxNew('oxTag');

        $oTag->set("testTag");
        $this->assertEquals("testtag", $oTag->getTitle());

        $oTag->set("test&Tag");
        $this->assertEquals("test tag", $oTag->getTitle());
    }

    /**
     * Test using tag object as string
     *
     * @return null
     */
    public function testUsingTagAsString()
    {
        $oTag = new oxTag("test1");
        $sTag = "tag: " . $oTag;

        $this->assertEquals("tag: test1", $sTag);
    }

    /**
     * Data provider for testStripMetaChars.
     *
     * @return array
     */
    public static function testStripMetaChars_dataProvider()
    {
        return array(
            array('a b', 'a+-><()~*"\\b'),
            array('a b', 'a+b'),
            array('a b', 'a-b'),
            array('a b', 'a>b'),
            array('a b', 'a<b'),
            array('a b', 'a(b'),
            array('a b', 'a)b'),
            array('a b', 'a~b'),
            array('a b', 'a*b'),
            array('a b', 'a"b'),
            array('a b', 'a\'b'),
            array('a b', 'a\\b'),
            array('a b', 'a[]{};:./|!@#$%^&?=`b'),
            array('a b', 'a[b'),
            array('a b', 'a]b'),
            array('a b', 'a{b'),
            array('a b', 'a{b'),
            array('a b', 'a}b'),
            array('a b', 'a;b'),
            array('a b', 'a:b'),
            array('a b', 'a.b'),
            array('a b', 'a/b'),
            array('a b', 'a|b'),
            array('a b', 'a!b'),
            array('a b', 'a@b'),
            array('a b', 'a#b'),
            array('a b', 'a$b'),
            array('a b', 'a%b'),
            array('a b', 'a^b'),
            array('a b', 'a&b'),
            array('a b', 'a?b'),
            array('a b', 'a=b'),
            array('a b', 'a`b'),
        );
    }

    /**
     * Test striping of meta characters from tag
     *
     * @dataProvider testStripMetaChars_dataProvider
     *
     * @param string $data
     * @param string $result
     *
     * @return null
     */
    public function testStripMetaChars($result, $data)
    {
        $oTag = oxNew('oxTag');

        $this->assertEquals($result, $oTag->stripMetaChars($data));
    }

    /**
     * Test preparation of tag.
     *
     * @return null
     */
    public function testPrepareTag()
    {
        $oTag = oxNew('oxTag');

        $this->assertEquals('a b', $oTag->prepare('a[]{};:./|!@#$%^&?=`b'));

        $sRandomString = str_repeat("a", $oTag->getMaxLength());
        $this->assertEquals($sRandomString, $oTag->prepare($sRandomString . 'should_be_cut'));
    }

    /**
     * Data provider for testAddRemoveUnderscores.
     *
     * @return array
     */
    public static function testAddRemoveUnderscores_dataProvider()
    {
        return array(
            array('a', 'a___'),
            array('ab', 'ab__'),
            array('abc', 'abc_'),
            array('abcd', 'abcd'),
            array('abcde', 'abcde'),
            array('ab_cde', 'ab_cde'),
            array('ab cde', 'ab__ cde_'),
            array('ab abc a abcd abcde', 'ab__ abc_ a___ abcd abcde'),
        );
    }

    /**
     * Testing add and remove dashes for tag
     *
     * @dataProvider testAddRemoveUnderscores_dataProvider
     *
     * @param string $sTag    tag to test
     * @param string $sResult expected result
     */
    public function testAddRemoveUnderscores($sTag, $sResult)
    {
        $oTag = new oxTag($sTag);

        $oTag->addUnderscores();
        $this->assertEquals($sResult, $oTag->get());

        $oTag->removeUnderscores();
        $this->assertEquals($sTag, $oTag->get());
    }

    /**
     * Testing removeUnderscores should split by - and remove underscores also.
     *
     * @dataProvider testAddRemoveUnderscores_dataProvider
     *
     * @param string $sTag    tag to test
     * @param string $sResult expected result
     */
    public function testRemoveUnderscoresSplitByDashes($sTag, $sResult)
    {
        $oTag = oxNew('oxTag');
        $oTag->set(str_replace(' ', '-', $sResult), false);

        $oTag->removeUnderscores();
        $this->assertEquals(str_replace(' ', '-', $sTag), $oTag->get());
    }
}