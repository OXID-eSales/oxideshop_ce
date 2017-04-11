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
 * Tests for Newsletter_Selection class
 */
class Unit_Admin_NewsletterSelectionTest extends OxidTestCase
{

    private $_oNewsSub = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $oDB = oxDb::getDb();

        $sInsert = "INSERT INTO `oxnewsletter` VALUES ( 'newstest', 'oxbaseshop', 'Test', 'TestHTML', 'TestPlain', 'TestSubject', NOW() )";
        $oDB->Execute($sInsert);

        $sInsert = "INSERT INTO `oxobject2group` VALUES ( 'test', 'oxbaseshop', '_testUserId', 'oxidnewcustomer', NOW() )";
        $oDB->Execute($sInsert);

        $sInsert = "INSERT INTO `oxobject2group` VALUES ( 'test2', 'oxbaseshop', 'newstest', 'oxidnewcustomer', NOW() )";
        $oDB->Execute($sInsert);

        $this->_oNewsSub = oxNew("oxnewssubscribed");
        $this->_oNewsSub->setId('_testNewsSubscrId');
        $this->_oNewsSub->oxnewssubscribed__oxuserid = new oxField('_testUserId', oxField::T_RAW);
        $this->_oNewsSub->oxnewssubscribed__oxemail = new oxField('useremail@useremail.nl', oxField::T_RAW);
        $this->_oNewsSub->oxnewssubscribed__oxdboptin = new oxField('1', oxField::T_RAW);
        $this->_oNewsSub->oxnewssubscribed__oxunsubscribed = new oxField('0000-00-00 00:00:00', oxField::T_RAW);
        $this->_oNewsSub->save();

    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $oDB = oxDb::getDb();
        $sDelete = "delete from oxnewsletter where oxid='newstest'";
        $oDB->Execute($sDelete);

        $sDelete = "delete from oxobject2group where oxobjectid='newstest' or oxobjectid='_testUserId'";
        $oDB->Execute($sDelete);
        $this->_oNewsSub->delete('_testNewsSubscrId');
        parent::tearDown();
    }

    /**
     * Testing newsletter selection render (#FS1694)
     *
     * @return null
     */
    public function testRender()
    {
        modConfig::setRequestParameter("oxid", 'newstest');
        $oNewsletter = $this->getProxyClass("Newsletter_selection");
        $this->assertEquals('newsletter_selection.tpl', $oNewsletter->render());
        $aViewData = $oNewsletter->getNonPublicVar('_aViewData');

        $this->assertTrue(isset($aViewData['edit']));
    }

    /**
     * Testing newsletter selection render, if user is not added to group
     * (#FS1694)
     *
     * @return null
     */
    public function testGetUserCount()
    {
        modConfig::setRequestParameter("iStart", 0);
        modConfig::setRequestParameter("oxid", 'newstest');
        $oNewsletter = new Newsletter_selection();
        $this->assertEquals(1, $oNewsletter->getUserCount());

        $oDB = oxDb::getDb();
        $sDelete = "delete from oxobject2group where oxobjectid='_testUserId'";
        $oDB->Execute($sDelete);
        modConfig::setRequestParameter("iStart", 0);
        modConfig::setRequestParameter("oxid", 'newstest');
        $oNewsletter = new Newsletter_selection();
        $this->assertEquals(0, $oNewsletter->getUserCount());
    }

    /**
     * Newsletter_Selection::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        // testing..
        oxTestModules::addFunction('oxnewsletter', 'save', '{ throw new Exception( "save" ); }');

        // testing..
        try {
            $oView = new Newsletter_Selection();
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Newsletter_Plain::save()");

            return;
        }
        $this->fail("error in Newsletter_Selection::save()");
    }
}
