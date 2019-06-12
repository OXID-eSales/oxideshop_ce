<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxField;
use \Exception;
use \oxDb;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use \oxTestModules;

/**
 * Tests for Newsletter_Selection class
 */
class NewsletterSelectionTest extends \OxidTestCase
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

        $shopId = ShopIdCalculator::BASE_SHOP_ID;
        if ($this->getConfig()->getEdition() == 'EE') {
            $shopId = 1;
        }

        $sInsert = "INSERT INTO `oxnewsletter` VALUES ( 'newstest', '{$shopId}', 'Test', 'TestHTML', 'TestPlain', 'TestSubject', NOW() )";
        $oDB->Execute($sInsert);

        $sInsert = "INSERT INTO `oxobject2group` VALUES ( 'test', '{$shopId}', '_testUserId', 'oxidnewcustomer', NOW() )";
        $oDB->Execute($sInsert);

        $sInsert = "INSERT INTO `oxobject2group` VALUES ( 'test2', '{$shopId}', 'newstest', 'oxidnewcustomer', NOW() )";
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
        $this->setRequestParameter("oxid", 'newstest');
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
        $this->setRequestParameter("iStart", 0);
        $this->setRequestParameter("oxid", 'newstest');
        $oNewsletter = oxNew('Newsletter_selection');
        $this->assertEquals(1, $oNewsletter->getUserCount());

        $oDB = oxDb::getDb();
        $sDelete = "delete from oxobject2group where oxobjectid='_testUserId'";
        $oDB->Execute($sDelete);
        $this->setRequestParameter("iStart", 0);
        $this->setRequestParameter("oxid", 'newstest');
        $oNewsletter = oxNew('Newsletter_selection');
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
            $oView = oxNew('Newsletter_Selection');
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Newsletter_Plain::save()");

            return;
        }
        $this->fail("error in Newsletter_Selection::save()");
    }
}
