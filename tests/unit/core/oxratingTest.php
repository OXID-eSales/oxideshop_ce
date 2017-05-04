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

class Unit_Core_oxratingTest extends OxidTestCase
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
        $myConfig = oxRegistry::getConfig();
        $sDate = date('Y-m-d', oxRegistry::get("oxUtilsDate")->getTime() - 5 * 24 * 60 * 60);
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
        $myConfig = oxRegistry::getConfig();
        $sInsert = "DELETE from `oxratings` where OXID='test'";
        $oDB->Execute($sInsert);

        parent::tearDown();
    }

    public function testAllowRating()
    {
        $oRating = oxNew('oxrating');
        modConfig::getInstance()->setConfigParam('iRatingLogsTimeout', 0);

        $this->assertFalse($oRating->allowRating('oxdefaultadmin', 'oxarticle', '1651'));
        $this->assertTrue($oRating->allowRating('test', 'oxarticle', '1651'));
    }

    public function testAllowRatingIfTimeout()
    {
        $oRating = oxNew('oxrating');
        modConfig::getInstance()->setConfigParam('iRatingLogsTimeout', 1);
        $this->assertTrue($oRating->allowRating('oxdefaultadmin', 'oxarticle', '1651'));
    }

    public function testGetRatingAverage()
    {
        // inserting few test records
        $oRev = new oxreview();
        $oRev->setId('_testrev1');
        $oRev->oxreviews__oxobjectid = new oxField('xxx');
        $oRev->oxreviews__oxtype = new oxField('oxarticle');
        $oRev->oxreviews__oxrating = new oxField(3);
        $oRev->save();

        $oRev = new oxreview();
        $oRev->setId('_testrev2');
        $oRev->oxreviews__oxobjectid = new oxField('xxx');
        $oRev->oxreviews__oxtype = new oxField('oxarticle');
        $oRev->oxreviews__oxrating = new oxField(1);
        $oRev->save();

        $oRev = new oxreview();
        $oRev->setId('_testrev3');
        $oRev->oxreviews__oxobjectid = new oxField('yyy');
        $oRev->oxreviews__oxtype = new oxField('oxarticle');
        $oRev->oxreviews__oxrating = new oxField(5);
        $oRev->save();

        $oRating = new oxRating();
        $this->assertEquals(2, $oRating->getRatingAverage('xxx', 'oxarticle'));
        $this->assertEquals(2, $oRating->getRatingCount('xxx', 'oxarticle'));
        $this->assertEquals(3, $oRating->getRatingAverage('xxx', 'oxarticle', array('yyy')));
        $this->assertEquals(3, $oRating->getRatingCount('xxx', 'oxarticle', array('yyy')));
    }

    public function testGetObjectIdAndType()
    {
        // inserting few test records
        $oRat = new oxrating();
        $oRat->setId('id1');
        $oRat->oxratings__oxobjectid = new oxField('xx1');
        $oRat->oxratings__oxtype = new oxField('oxarticle');
        $oRat->oxratings__oxrating = new oxField(1);
        $oRat->save();

        $oRat = new oxrating();
        $oRat->setId('id2');
        $oRat->oxratings__oxobjectid = new oxField('xx2');
        $oRat->oxratings__oxtype = new oxField('oxrecommlist');
        $oRat->oxratings__oxrating = new oxField(2);
        $oRat->save();

        $oRat = new oxrating();
        $oRat->load('id1');
        $this->assertEquals('id1', $oRat->getId());
        $this->assertEquals('xx1', $oRat->getObjectId());
        $this->assertEquals('oxarticle', $oRat->getObjectType());

        $oRat = new oxrating();
        $oRat->load('id2');
        $this->assertEquals('id2', $oRat->getId());
        $this->assertEquals('xx2', $oRat->getObjectId());
        $this->assertEquals('oxrecommlist', $oRat->getObjectType());
    }
}
