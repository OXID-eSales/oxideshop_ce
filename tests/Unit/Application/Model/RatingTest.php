<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;
use \oxDb;
use \oxRegistry;

class RatingTest extends \OxidTestCase
{
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $oDB = oxDb::getDb();
        $myConfig = $this->getConfig();
        $sDate = date('Y-m-d', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() - 5 * 24 * 60 * 60);
        $sInsert = "INSERT INTO `oxratings` (`OXID` ,`OXSHOPID` ,`OXUSERID` ,`OXOBJECTID` ,`OXRATING` ,`OXTIMESTAMP` ,
                    `OXTYPE`) VALUES ('test', '" . $myConfig->getShopId() . "', 'oxdefaultadmin', '1651', '5', '$sDate', 'oxarticle')";
        $oDB->Execute($sInsert);
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $oDB = oxDb::getDb();
        $myConfig = $this->getConfig();
        $sInsert = "DELETE from `oxratings` where OXID='test'";
        $oDB->Execute($sInsert);

        parent::tearDown();
    }

    public function testAllowRating()
    {
        $oRating = oxNew('oxrating');
        $this->getConfig()->setConfigParam('iRatingLogsTimeout', 0);

        $this->assertFalse($oRating->allowRating('oxdefaultadmin', 'oxarticle', '1651'));
        $this->assertTrue($oRating->allowRating('test', 'oxarticle', '1651'));
    }

    public function testAllowRatingIfTimeout()
    {
        $oRating = oxNew('oxrating');
        $this->getConfig()->setConfigParam('iRatingLogsTimeout', 1);
        $this->assertTrue($oRating->allowRating('oxdefaultadmin', 'oxarticle', '1651'));
    }

    public function testGetRatingAverage()
    {
        // inserting few test records
        $oRev = oxNew('oxreview');
        $oRev->setId('_testrev1');
        $oRev->oxreviews__oxobjectid = new oxField('xxx');
        $oRev->oxreviews__oxtype = new oxField('oxarticle');
        $oRev->oxreviews__oxrating = new oxField(3);
        $oRev->save();

        $oRev = oxNew('oxreview');
        $oRev->setId('_testrev2');
        $oRev->oxreviews__oxobjectid = new oxField('xxx');
        $oRev->oxreviews__oxtype = new oxField('oxarticle');
        $oRev->oxreviews__oxrating = new oxField(1);
        $oRev->save();

        $oRev = oxNew('oxreview');
        $oRev->setId('_testrev3');
        $oRev->oxreviews__oxobjectid = new oxField('yyy');
        $oRev->oxreviews__oxtype = new oxField('oxarticle');
        $oRev->oxreviews__oxrating = new oxField(5);
        $oRev->save();

        $oRating = oxNew('oxRating');
        $this->assertEquals(2, $oRating->getRatingAverage('xxx', 'oxarticle'));
        $this->assertEquals(2, $oRating->getRatingCount('xxx', 'oxarticle'));
        $this->assertEquals(3, $oRating->getRatingAverage('xxx', 'oxarticle', array('yyy')));
        $this->assertEquals(3, $oRating->getRatingCount('xxx', 'oxarticle', array('yyy')));
    }

    public function testGetObjectIdAndType()
    {
        // inserting few test records
        $oRat = oxNew('oxRating');
        $oRat->setId('id1');
        $oRat->oxratings__oxobjectid = new oxField('xx1');
        $oRat->oxratings__oxtype = new oxField('oxarticle');
        $oRat->oxratings__oxrating = new oxField(1);
        $oRat->save();

        $oRat = oxNew('oxRating');
        $oRat->setId('id2');
        $oRat->oxratings__oxobjectid = new oxField('xx2');
        $oRat->oxratings__oxtype = new oxField('oxrecommlist');
        $oRat->oxratings__oxrating = new oxField(2);
        $oRat->save();

        $oRat = oxNew('oxRating');
        $oRat->load('id1');
        $this->assertEquals('id1', $oRat->getId());
        $this->assertEquals('xx1', $oRat->getObjectId());
        $this->assertEquals('oxarticle', $oRat->getObjectType());

        $oRat = oxNew('oxRating');
        $oRat->load('id2');
        $this->assertEquals('id2', $oRat->getId());
        $this->assertEquals('xx2', $oRat->getObjectId());
        $this->assertEquals('oxrecommlist', $oRat->getObjectType());
    }
}
