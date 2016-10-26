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
namespace Unit\Application\Controller\Admin;

use \OxidEsales\EshopCommunity\Application\Model\OrderFileList;

use \oxField;
use \oxDb;
use \oxRegistry;

/**
 * Tests for Order_Article class
 */
class OrderDownloadsTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $myConfig = $this->getConfig();

        $this->getConfig()->setConfigParam('blPerfNoBasketSaving', true);

        // adding test order
        $oOrder = oxNew('oxbase');
        $oOrder->init('oxorder');
        $oOrder->setId('_testOrder');
        $oOrder->oxorder__oxuserid = new oxField('oxdefaultadmin');
        $oOrder->save();

        // adding test article
        $oArticle = oxNew('oxbase');
        $oArticle->init('oxarticles');
        $oArticle->load('1126');
        $oArticle->setId('_testArticle');
        $oArticle->oxarticles__oxartnum = new oxField('_testArticle');
        $oArticle->oxarticles__oxstock = new oxField(100);
        $oArticle->save();

        //set order
        $oOrder = oxNew("oxOrder");
        $oOrder->setId('_testOrderId1');
        $oOrder->oxorder__oxshopid = new oxField($myConfig->getShopId(), oxField::T_RAW);
        $oOrder->oxorder__oxuserid = new oxField("_testUserId", oxField::T_RAW);
        $oOrder->oxorder__oxbillcountryid = new oxField('10', oxField::T_RAW);
        $oOrder->oxorder__oxdelcountryid = new oxField('11', oxField::T_RAW);
        $oOrder->oxorder__oxdeltype = new oxField('_testDeliverySetId', oxField::T_RAW);
        $oOrder->oxorder__oxpaymentid = new oxField('_testPaymentId', oxField::T_RAW);
        $oOrder->oxorder__oxpaymenttype = new oxField('_testPaymentId', oxField::T_RAW);
        $oOrder->oxorder__oxcardid = new oxField('_testWrappingId', oxField::T_RAW);

        $oOrder->save();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDB()->execute('delete from oxorderfiles where oxid="_testOrderFile"');

        parent::tearDown();
    }

    /**
     * Test get edit object.
     *
     * @return null
     */
    public function testGetEditObject()
    {
        $this->setRequestParameter("oxid", null);

        $oView = oxNew('Order_Downloads');
        $this->assertNull($oView->getEditObject());

        $this->setRequestParameter("oxid", "_testOrderId1");

        $oView = oxNew('Order_Downloads');
        $oOrderFiles = $oView->getEditObject();
        $this->assertTrue($oOrderFiles instanceof orderfilelist);
    }

    /**
     * Test get protuct list.
     *
     * @return null
     */
    public function testGetProductList()
    {
        $this->setRequestParameter("oxorderfileid", "_testOrderFile");
        oxDb::getDB()->execute(
            'insert into oxorderfiles set oxid="_testOrderFile", oxfileid="fileId", oxmaxdownloadcount="10", oxlinkexpirationtime="24",
                                    oxdownloadexpirationtime="12", oxvaliduntil="2011-10-20 12:12:00", oxdownloadcount="2", oxfirstdownload="2011-10-10", oxlastdownload="2011-10-20"'
        );

        $sNow = oxRegistry::get("oxUtilsDate")->getTime();
        $sDate = date('Y-m-d H:i:s', $sNow);

        $oView = oxNew('Order_Downloads');
        $oView->resetDownloadLink();

        $oOrderFile = oxNew('oxOrderFile');
        $oOrderFile->load("_testOrderFile");
        $this->assertEquals('0', $oOrderFile->oxorderfiles__oxdownloadcount->value);
        $this->assertTrue($oOrderFile->oxorderfiles__oxvaliduntil->value >= $sDate);
        $this->assertEquals('0000-00-00 00:00:00', $oOrderFile->oxorderfiles__oxfirstdownload->value);
        $this->assertEquals('0000-00-00 00:00:00', $oOrderFile->oxorderfiles__oxlastdownload->value);

    }

}
