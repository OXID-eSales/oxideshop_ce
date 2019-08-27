<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

class ReviewTest extends \OxidTestCase
{
    protected $_oReview = null;
    protected $_iNow = null;
    protected $_iReviewTime = 0;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_iReviewTime = time();
        $this->setTime($this->_iReviewTime);

        $this->_oReview = oxNew('oxReview');
        $this->_oReview->setId('_testId');
        $this->_oReview->oxreviews__oxuserid = new oxField('oxdefaultadmin', oxField::T_RAW);
        $this->_oReview->oxreviews__oxtext = new oxField('deValue', oxField::T_RAW);
        $this->_oReview->oxreviews__oxlang = new oxField(0, oxField::T_RAW);
        $this->_oReview->save();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $myDB = oxDb::getDB();
        $sQ = 'delete from oxuser where oxid="test"';
        $myDB->Execute($sQ);
        $this->cleanUpTable('oxreviews');
        oxRemClassModule('modOxUtilsDate');

        parent::tearDown();
    }

    /**
     * Testing how assign loads user info
     */
    public function testAssignNonExisting()
    {
        $oReview = oxNew('oxReview');
        $oReview->load('xxx');

        $this->assertFalse(isset($oReview->oxuser__oxfname));
    }

    public function testAssignExisting()
    {
        $oReview = oxNew('oxReview');
        $oReview->load('_testId');

        $this->assertTrue(isset($oReview->oxuser__oxfname));
        $this->assertEquals('John', $oReview->oxuser__oxfname->value);
    }

    public function testLoadDe()
    {
        $oReview = oxNew('oxReview');
        $oReview->load('_testId');

        $this->assertEquals('deValue', $oReview->oxreviews__oxtext->value);

        $sCreate = date('d.m.Y H:i:s', $this->_iReviewTime);
        if (oxRegistry::getLang()->getBaseLanguage() == 1) {
            $sCreate = date('Y-m-d H:i:s', $this->_iReviewTime);
        }

        $this->assertEquals($sCreate, $oReview->oxreviews__oxcreate->value);
    }

    public function testUpdate()
    {
        $iCurrTime = time();

        $this->_oReview->oxreviews__oxtext = new oxField('deValue2', oxField::T_RAW);
        $this->_oReview->Save();

        $oReview = oxNew('oxReview');
        $oReview->load('_testId');

        $sCreate = date('d.m.Y H:i:s', $iCurrTime);
        if (oxRegistry::getLang()->getBaseLanguage() == 1) {
            $sCreate = date('Y-m-d H:i:s', $iCurrTime);
        }

        $this->assertEquals('deValue2', $oReview->oxreviews__oxtext->value);
        $this->assertTrue($sCreate >= $oReview->oxreviews__oxcreate->value);
    }

    public function testInsertAddsCreateDate()
    {
        $iCurrTime = time();

        $oReview = oxNew('oxReview');
        $oReview->setId('_testId2');
        $oReview->oxreviews__oxtext = new oxField('deValue', oxField::T_RAW);
        $oReview->save();

        $oReview = oxNew('oxReview');
        $oReview->load('_testId2');

        $sCreate = date('d.m.Y H:i:s', $iCurrTime);
        if (oxRegistry::getLang()->getBaseLanguage() == 1) {
            $sCreate = date('Y-m-d H:i:s', $iCurrTime);
        }

        $this->assertTrue($sCreate >= $oReview->oxreviews__oxcreate->value);
    }

    public function testLoadList()
    {
        oxTestModules::addFunction('oxField', 'convertToFormattedDbDate', '{$this->convertToFormattedDbDate=true;}');
        oxTestModules::addFunction('oxField', 'convertToPseudoHtml', '{$this->convertToPseudoHtml=true;}');
        oxTestModules::addFunction('oxlist', 'selectString($sql, array $parameters = array())', '{$this->selectArgs = $aA;$o=oxNew("oxreview");$o->oxreviews__oxcreate=oxNew("oxField");$o->oxreviews__oxtext=oxNew("oxField");$this->_aArray = array($o);}');
        $oObj = oxNew('oxreview');
        $oList = $oObj->loadList('checktype', array('aId', 'lalaId'));
        $this->assertEquals("select oxreviews.* from oxreviews where oxreviews.oxtype = :oxtype and oxreviews.oxobjectid in ( 'aId', 'lalaId' ) and oxreviews.oxlang = :oxlang and oxreviews.oxtext != \"\"  order by oxreviews.oxcreate desc ", $oList->selectArgs[0]);
        $this->assertTrue($oList[0]->oxreviews__oxcreate->convertToFormattedDbDate);
        $this->assertTrue($oList[0]->oxreviews__oxtext->convertToPseudoHtml);

        $oList = $oObj->loadList('checktype', array('aId', 'lalaId'), 1, 4);
        $this->assertEquals("select oxreviews.* from oxreviews where oxreviews.oxtype = :oxtype and oxreviews.oxobjectid in ( 'aId', 'lalaId' ) and oxreviews.oxlang = :oxlang order by oxreviews.oxcreate desc ", $oList->selectArgs[0]);
        $this->assertTrue($oList[0]->oxreviews__oxcreate->convertToFormattedDbDate);
        $this->assertTrue($oList[0]->oxreviews__oxtext->convertToPseudoHtml);
    }

    public function testLoadListNoIdsPassed()
    {
        $oRev = oxNew('oxReview');
        $this->assertEquals(0, $oRev->loadList('x', null)->count());
    }

    public function testLoadListModerationTest()
    {
        // inserting few test records
        $oRev = oxNew('oxReview');
        $oRev->setId('_testrev1');
        $oRev->oxreviews__oxactive = new oxField(1);
        $oRev->oxreviews__oxobjectid = new oxField('xxx');
        $oRev->oxreviews__oxtype = new oxField('oxarticle');
        $oRev->oxreviews__oxtext = new oxField('revtext');
        $oRev->save();

        $oRev = oxNew('oxReview');
        $oRev->setId('_testrev2');
        $oRev->oxreviews__oxactive = new oxField(0);
        $oRev->oxreviews__oxobjectid = new oxField('xxx');
        $oRev->oxreviews__oxtype = new oxField('oxarticle');
        $oRev->oxreviews__oxtext = new oxField('revtext');
        $oRev->save();

        // moderation is OFF
        $this->getConfig()->setConfigParam('blGBModerate', 0);
        $oRev = oxNew('oxReview');
        $this->assertEquals(2, $oRev->loadList('oxarticle', 'xxx')->count());

        // moderation is ON
        $this->getConfig()->setConfigParam('blGBModerate', 1);
        $this->assertEquals(1, $oRev->loadList('oxarticle', 'xxx')->count());
    }

    public function testGetObjectIdAndType()
    {
        // inserting few test records
        $oRev = oxNew('oxReview');
        $oRev->setId('id1');
        $oRev->oxreviews__oxactive = new oxField(1);
        $oRev->oxreviews__oxobjectid = new oxField('xx1');
        $oRev->oxreviews__oxtype = new oxField('oxarticle');
        $oRev->oxreviews__oxtext = new oxField('revtext');
        $oRev->save();

        $oRev = oxNew('oxReview');
        $oRev->setId('id2');
        $oRev->oxreviews__oxactive = new oxField(1);
        $oRev->oxreviews__oxobjectid = new oxField('xx2');
        $oRev->oxreviews__oxtype = new oxField('oxrecommlist');
        $oRev->oxreviews__oxtext = new oxField('revtext');
        $oRev->save();

        $oRev = oxNew('oxReview');
        $oRev->load('id1');
        $this->assertEquals('xx1', $oRev->getObjectId());
        $this->assertEquals('oxarticle', $oRev->getObjectType());

        $oRev = oxNew('oxReview');
        $oRev->load('id2');
        $this->assertEquals('xx2', $oRev->getObjectId());
        $this->assertEquals('oxrecommlist', $oRev->getObjectType());
    }
}
