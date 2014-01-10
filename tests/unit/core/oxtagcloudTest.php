<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class Unit_Core_oxTagCloudTest extends OxidTestCase
{
    public function testGetFontSizeCustom()
    {
        $aTestData = array( "sTestTag1" => 400,
                            "sTestTag2" => 300,
                            "sTestTag3" => 200,
                            "sTestTag4" => 300,
                            "sTestTag5" => 200,
                            "sTestTag6" => 100,
                            "sTestTag7" => 100 );

        $oTagCloud = $this->getMock( "oxTagCloud", array( "getCloudArray" ) );
        $oTagCloud->expects( $this->any() )->method( 'getCloudArray' )->will( $this->returnValue( array( "sTestTag1" => 20, "sTestTag2" => 17, "sTestTag3" => 13, "sTestTag4" => 15, "sTestTag5" => 12, "sTestTag6" => 1, "sTestTag7" => 5 ) ) );

        foreach ( $aTestData as $sTag => $iVal ) {
            $this->assertEquals( $iVal, $oTagCloud->getTagSize( $sTag ) );
        }
    }

    /**
     * oxTagCloud::setProductId() test case
     *
     * @return null
     */
    public function testSetProductId()
    {
        $oTagCloud = new oxTagCloud();
        $this->assertNull( $oTagCloud->getProductId() );

        $oTagCloud->setProductId( "testProductId" );
        $this->assertEquals( "testProductId", $oTagCloud->getProductId() );
    }

    /**
     * oxTagCloud::setLanguageId() test case
     *
     * @return null
     */
    public function testSetLanguageId()
    {
        $oTagCloud = new oxTagCloud();
        $this->assertEquals( oxLang::getInstance()->getBaseLanguage(), $oTagCloud->getLanguageId() );

        $oTagCloud->setLanguageId( "testLanguagaId" );
        $this->assertEquals( "testLanguagaId", $oTagCloud->getLanguageId() );
    }

    /**
     * oxTagCloud::setExtendedMode() test case
     *
     * @return null
     */
    public function testSetExtendedMode()
    {
        $oTagCloud = new oxTagCloud();
        $this->assertFalse( $oTagCloud->isExtended() );

        $oTagCloud->setExtendedMode( true );
        $this->assertTrue( $oTagCloud->isExtended() );
    }

    /**
     * oxTagCloud::getCloudArray() test case
     *
     * @return null
     */
    public function testGetCloudArray()
    {
        // disabling cache
        oxTestModules::addFunction( "oxUtils", "fromFileCache", "{return null;}" );
        oxTestModules::addFunction( "oxUtils", "toFileCache", "{return false;}" );

        $oTagCloud = $this->getMock( "oxTagCloud", array( "getLanguageId", "isExtended", "getProductId", "_getCacheKey", "getTags" ) );
        $oTagCloud->expects( $this->exactly( 2 ) )->method( 'getLanguageId' )->will( $this->returnValue( 0 ) );
        $oTagCloud->expects( $this->exactly( 2 ) )->method( 'isExtended' )->will( $this->returnValue( true ) );
        $oTagCloud->expects( $this->exactly( 2 ) )->method( 'getProductId' )->will( $this->returnValue( null ) );
        $oTagCloud->expects( $this->exactly( 2 ) )->method( '_getCacheKey' )->with( $this->equalTo( true ), $this->equalTo( 0 ) )->will( $this->returnValue( "testCacheId" ) );
        $oTagCloud->expects( $this->once() )->method( 'getTags' )->with( $this->equalTo( null ), $this->equalTo( true ), $this->equalTo( 0 ) )->will( $this->returnValue( array( "tag1", "tag2" ) ) );

        $this->assertEquals( array( "tag1", "tag2" ), $oTagCloud->getCloudArray() );
        $this->assertEquals( array( "tag1", "tag2" ), $oTagCloud->getCloudArray() );
    }

    /**
     * oxTagCloud::getTagLink() test case, SEO on
     *
     * @return null
     */
    public function testGetTagLinkSeoOn()
    {
        // seo on
        oxTestModules::addFunction( "oxUtils", "seoIsActive", "{return true;}" );

        $oTagCloud = new oxTagCloud();


            $this->assertEquals( oxConfig::getInstance()->getConfigParam("sShopURL")."tag/zauber/", $oTagCloud->getTagLink( "zauber" ) );
            //$this->assertEquals( oxConfig::getInstance()->getConfigParam("sShopURL")."index.php?cl=tag&amp;searchtag=testTag&amp;lang=0", $oTagCloud->getTagLink( "testTag" ) );
    }

    /**
     * oxTagCloud::getTagLink() test case, SEO off
     *
     * @return null
     */
    public function testGetTagLinkSeoOff()
    {
        // seo off
        oxTestModules::addFunction( "oxUtils", "seoIsActive", "{return false;}" );

        $oTagCloud = new oxTagCloud();
        $this->assertEquals( oxConfig::getInstance()->getConfigParam("sShopURL")."index.php?cl=tag&amp;searchtag=testTag&amp;lang=0", $oTagCloud->getTagLink( "testTag" ) );

    }

    /**
     * oxTagCloud::getTagTitle() test case
     *
     * @return null
     */
    public function testGetTagTitle()
    {
        $oTagCloud = new oxTagCloud();
        $this->assertEquals( "testTag", $oTagCloud->getTagTitle( "testTag" ) );
        $this->assertEquals( "test&amp;Tag", $oTagCloud->getTagTitle( "test&Tag" ) );
    }

    /**
     * oxTagCloud::getMaxHit() test case
     *
     * @return null
     */
    public function testGetMaxHit()
    {
        $oTagCloud = $this->getMock( "oxTagCloud", array( "getCloudArray" ) );
        $oTagCloud->expects( $this->once() )->method( 'getCloudArray' )->will( $this->returnValue( array( "tag1" => 999, "tag2" => 666 ) ) );
        $this->assertEquals( 999, $oTagCloud->UNITgetMaxHit() );
    }

    /**
     * oxTagCloud::getTagSize() test case
     *
     * @return null
     */
    public function testGetTagSize()
    {
        $oTagCloud = $this->getMock( "oxTagCloud", array( "getCloudArray", "_getFontSize" ) );
        $oTagCloud->expects( $this->exactly( 2 ) )->method( 'getCloudArray' )->will( $this->returnValue( array( "tag1" => 999, "tag2" => 666 ) ) );
        $oTagCloud->expects( $this->once() )->method( '_getFontSize' )->with( $this->equalTo( 666 ), $this->equalTo( 999 ) )->will( $this->returnValue( 400 ) );

        $this->assertEquals( 400, $oTagCloud->getTagSize( "tag2" ) );
    }

    public function testGetTags()
    {
        $oTagCloud = new oxTagCloud();
        $aTags = $oTagCloud->getTags();
        $this->assertEquals(20, count($aTags));

            $this->assertFalse(isset($aTags['25wbezugshinweis']));
            $this->assertEquals(3, $aTags['wanduhr']);
    }

    public function testGetTagsEn()
    {
        $oTagCloud = new oxTagCloud();
            $iExpt = 20;
        $this->assertEquals($iExpt, count( $oTagCloud->getTags( null, null, 1 ) ) );
    }

    public function testGetTagsExtended()
    {
        $oTagCloud = new oxTagCloud();
        $aTags = $oTagCloud->getTags(null, true);
            $this->assertTrue(isset($aTags['25wbezugshinweis']));
            $this->assertEquals(200, count($aTags));
            $this->assertEquals(3, $aTags['wanduhr']);
    }

    public function testGetTagsExtendedEn()
    {
        $oTagCloud = new oxTagCloud();
            $iExpt = 81;
        $this->assertEquals( $iExpt, count( $oTagCloud->getTags( null, true, 1 ) ) );
    }

    public function testGetTagsArticle()
    {
        $oTagCloud = new oxTagCloud();
        $oTagCloud->resetTagCache();
        $aTags = $oTagCloud->getTags('2000');
        $this->assertTrue(isset($aTags['coolen']));

            $this->assertEquals(5, count($aTags));
    }

    public function testGetTagsArticleEn()
    {
        $oTagCloud = new oxTagCloud();
        $oTagCloud->resetTagCache();
            $iExpt = 1;
        $this->assertEquals( $iExpt, count( $oTagCloud->getTags( '2000', false, 1 ) ) );
    }

    public function testGetTagsArticleExtended()
    {
        $oTagCloud = new oxTagCloud();
        $oTagCloud->resetTagCache();
        $aTags = $oTagCloud->getTags('1126', true );

            $this->assertEquals(9, count($aTags));
            $this->assertTrue(isset($aTags['fee']));
    }

    public function testGetTagsArticleExtendedEn()
    {
        $oTagCloud = new oxTagCloud();
        $oTagCloud->resetTagCache();
            $iExpt = 2;

        $this->assertEquals( $iExpt, count( $oTagCloud->getTags('1126', true, 1 ) ) );
    }

    public function testGetTagsNotFound()
    {
        $oTagCloud = new oxTagCloud();
        $oTagCloud->resetTagCache();
        $aTags = $oTagCloud->getTags('test');
        $this->assertEquals(0, count($aTags));
    }

    public function testGetTags_ArticleTimeRange()
    {
        $blParam = oxConfig::getInstance()->getConfigParam( 'blUseTimeCheck' ) ;
        oxConfig::getInstance()->setConfigParam( 'blUseTimeCheck', 1) ;

        $oArticle = oxNew('oxarticle');
        $oArticle->load('1126');
        $oArticle->oxarticles__oxactive->value = 0;
        $oArticle->oxarticles__oxactivefrom->value = date( 'Y-m-d H:i:s', oxUtilsDate::getInstance()->getTime() - 100 );
        $oArticle->oxarticles__oxactiveto->value = date( 'Y-m-d H:i:s', oxUtilsDate::getInstance()->getTime() + 100 );
        $oArticle->save();

        $oTagCloud = new oxTagCloud();
        $oTagCloud->resetTagCache();
        $aTags = $oTagCloud->getTags('1126', true );

            $this->assertEquals(9, count($aTags));
            $this->assertTrue(isset($aTags['fee']));

        oxConfig::getInstance()->setConfigParam( 'blUseTimeCheck', $blParam) ;
        $oArticle->oxarticles__oxactive->value = 1;
        $oArticle->oxarticles__oxactivefrom->value = '0000-00-00 00:00:00';
        $oArticle->oxarticles__oxactiveto->value = '0000-00-00 00:00:00';
        $oArticle->save();
    }

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
    }

    /*
    public function testAssignTagsFromSearchKeys()
    {
        $this->markTestIncomplete();
    }*/

    public function testStripMetaChars()
    {
        $oTagCloud = new oxTagCloud();
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a+-><()~*"\\b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a+b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a-b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a>b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a<b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a(b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a)b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a~b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a*b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a"b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a\'b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a\\b'));	
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a[]{};:./|!@#$%^&?=`b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a[b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a]b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a{b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a}b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a;b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a:b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a.b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a/b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a|b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a!b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a@b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a#b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a$b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a%b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a^b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a&b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a?b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a=b'));
        $this->assertEquals('a b', $oTagCloud->stripMetaChars('a`b'));
    }

    public function testPrepareTags()
    {
        $oTagCloud = new oxTagCloud();
        $this->assertEquals('tag1,tag2', $oTagCloud->prepareTags('tag1,tag2'));
        $this->assertEquals('tag1,tag2', $oTagCloud->prepareTags('tag1,tag2 '));
        $this->assertEquals('tag1,tag2', $oTagCloud->prepareTags('tag1,tag2,'));
        $this->assertEquals('tag1,tag2', $oTagCloud->prepareTags('tag1,, ,,tag2,'));
        $this->assertEquals('tag1 tag2', $oTagCloud->prepareTags('tag1 tag2 '));
        $this->assertEquals('tag1,tag2', $oTagCloud->prepareTags('tag1, tag2 '));
        $this->assertEquals('tag1,tag2', $oTagCloud->prepareTags('TAG1,tag2'));
        $this->assertEquals('ta__', $oTagCloud->prepareTags('ta'));
        $this->assertEquals('t___,t___', $oTagCloud->prepareTags('t,t'));
        $this->assertEquals('t___ t___', $oTagCloud->prepareTags('t t'));
        $this->assertEquals('', $oTagCloud->prepareTags(' '));
        $this->assertEquals('tag1,ta__,tag2,t___', $oTagCloud->prepareTags('tag1,,,,ta, tag2,t'));
        $this->assertEquals('bar_ set_', $oTagCloud->prepareTags('bar-set'));
        $this->assertEquals('bar_ sett', $oTagCloud->prepareTags('bar-sett'));
        $this->assertEquals('barr sett', $oTagCloud->prepareTags('barr-sett'));
        $this->assertEquals('foo_ bar_', $oTagCloud->prepareTags('"foo-bar"'));
        $this->assertEquals('foo_', $oTagCloud->prepareTags('\\foo\\'));
        $this->assertEquals('long testing string long testing string long testing string', $oTagCloud->prepareTags('Long testing string long testing string long testing string long testing string'));
		$this->assertEquals('a___ long testing string long testing string long testing strin', $oTagCloud->prepareTags('A long testing string long testing string long testing string long testing string'));
    }

    public function testTrimTags()
    {
        $oTagCloud = new oxTagCloud();
        $this->assertEquals('tag1__,tag2', $oTagCloud->trimTags('tag1__,tag2 '));
        $this->assertEquals('tag1__,tag2', $oTagCloud->trimTags('tag1__,,, ,tag2 '));
        $this->assertEquals('tag1__  tag2', $oTagCloud->trimTags('tag1__  tag2 '));
        $this->assertEquals('tag1_tag2', $oTagCloud->trimTags('tag1_tag2 '));
        $this->assertEquals('TAG1__,tag2', $oTagCloud->trimTags('TAG1__,tag2'));
        $this->assertEquals('ta', $oTagCloud->trimTags('ta__'));
        $this->assertEquals('t,t____', $oTagCloud->trimTags('t___,t____'));
        $this->assertEquals('', $oTagCloud->trimTags('____'));
        $this->assertEquals('tag1,ta,tag2,t', $oTagCloud->trimTags('tag1, ta__,tag2 ,t___'));
        $this->assertEquals('tag1 ta tag2 t', $oTagCloud->trimTags('tag1 ta__ tag2 t___'));
        $this->assertEquals('bar set', $oTagCloud->trimTags('bar_ set_'));
        $this->assertEquals('barr set', $oTagCloud->trimTags('barr set_'));
        $this->assertEquals('barr', $oTagCloud->trimTags(',barr,'));
    }

    public function testTagCache()
    {
        $oTagCloud = $this->getProxyClass('oxTagCloud');
        $sCacheKey1 = $oTagCloud->UNITgetCacheKey(true);//  "cloudtag_"."_".oxConfig::getInstance()->getShopID()."|".TRUE;
        $sCacheKey2 = $oTagCloud->UNITgetCacheKey(false);//"cloudtag_"."_".oxConfig::getInstance()->getShopID()."|".FALSE;

        //remove older files
        $oUtils = $this->getProxyClass("oxutils");
        $sFile1 = $oUtils->getCacheFilePath($sCacheKey1);
        $sFile2 = $oUtils->getCacheFilePath($sCacheKey2);
        @unlink($sFile1);
        @unlink($sFile2);

        oxUtils::getInstance()->toFileCache($sCacheKey1, "testValue1");
        oxUtils::getInstance()->toFileCache($sCacheKey2, "testValue2");
        $this->assertEquals("testValue1", oxUtils::getInstance()->fromFileCache($sCacheKey1));
        $this->assertEquals("testValue2", oxUtils::getInstance()->fromFileCache($sCacheKey2));

        $oTagCloud->resetTagCache();

        $this->assertEquals(null, oxUtils::getInstance()->fromFileCache($sCacheKey1));
        $this->assertEquals(null, oxUtils::getInstance()->fromFileCache($sCacheKey2));
    }

    public function testGetCacheNameLang1()
    {
        $oTagCloud = $this->getProxyClass( 'oxTagCloud' );
            $this->assertEquals( 'tagcloud__oxbaseshop_1_1', $oTagCloud->UNITgetCacheKey( true, 1 ) );
            $this->assertEquals( 'tagcloud__oxbaseshop_1_0', $oTagCloud->UNITgetCacheKey( false, 1 ) );
    }

    public function testGetCacheName()
    {
        $oTagCloud = $this->getProxyClass('oxTagCloud');
            $this->assertEquals('tagcloud__oxbaseshop_0_1', $oTagCloud->UNITgetCacheKey(true));
            $this->assertEquals('tagcloud__oxbaseshop_0_0', $oTagCloud->UNITgetCacheKey(false));
    }

}
