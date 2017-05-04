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

class Unit_Core_oxorderfilelistTest extends OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        $oOrder = new oxOrder();
        $oOrder->setId('_orderId_1');
        $oOrder->oxorder__oxuserid = new oxField('_userId');
        $oOrder->save();

        $oOrder = new oxOrder();
        $oOrder->setId('_orderId_2');
        $oOrder->oxorder__oxpaid = new oxField('2011-01-10 12:12:12');
        $oOrder->oxorder__oxuserid = new oxField('_userId');
        $oOrder->save();

        $oOrderArticle = new oxOrderArticle();
        $oOrderArticle->setId('_orderArticleId_1');
        $oOrderArticle->oxorderarticles__oxtitle = new oxField('title');
        $oOrderArticle->oxorderarticles__oxartnum = new oxField('artnum');
        $oOrderArticle->save();

        $oOrderArticle = new oxOrderArticle();
        $oOrderArticle->setId('_orderArticleId_2');
        $oOrderArticle->oxorderarticles__oxtitle = new oxField('title');
        $oOrderArticle->oxorderarticles__oxartnum = new oxField('artnum');
        $oOrderArticle->save();

        $oOrderFile1 = new oxOrderFile();
        $oOrderFile1->setOrderId('_orderId_1');
        $oOrderFile1->setOrderArticleId('_orderArticleId_1');
        $oOrderFile1->setFile('_fileName_1', '_fileId_1', 10, 24, 12);
        $oOrderFile1->save();

        $oOrderFile2 = new oxOrderFile();
        $oOrderFile2->setOrderId('_orderId_1');
        $oOrderFile2->setOrderArticleId('_orderArticleId_1');
        $oOrderFile2->setFile('_fileName_2', '_fileId_2', 10, 24, 12);
        $oOrderFile2->save();


        $oOrderFile3 = new oxOrderFile();
        $oOrderFile3->setOrderId('_orderId_2');
        $oOrderFile3->setOrderArticleId('_orderArticleId_2');
        $oOrderFile3->setFile('_fileName_3', '_fileId_3', 10, 24, 12);
        $oOrderFile3->save();

        $oOrderFile4 = new oxOrderFile();
        $oOrderFile4->setOrderId('_orderId_1');
        $oOrderFile4->setOrderArticleId('_orderArticleId_1');
        $oOrderFile4->setShopId('_shopId');
        $oOrderFile4->setFile('_fileName_2', '_fileId_2', 10, 24, 12);
        $oOrderFile4->save();

        parent::setUp();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxorder');
        $this->cleanUpTable('oxorderarticles');

        $oDb = oxDb::getDb();
        $oDb->execute("TRUNCATE TABLE `oxorderfiles`");

        parent::tearDown();
    }

    /**
     * Testing oxOrder::getOrderFiles
     *
     * @return null
     */
    public function testLoadUserFiles()
    {

        $oUserFilesList = new oxOrderFileList();
        $oUserFilesList->loadUserFiles('_userId');

        $this->assertEquals(3, count($oUserFilesList));

        foreach ($oUserFilesList as $oUserFile) {
            $this->assertEquals('title', $oUserFile->oxorderfiles__oxarticletitle->value);
            $this->assertEquals('artnum', $oUserFile->oxorderfiles__oxarticleartnum->value);
            if ($oUserFile->oxorderfiles__oxorderid->value == '_orderId_2') {
                $this->assertEquals(1, $oUserFile->isPaid());
            } else {
                $this->assertEquals(0, $oUserFile->isPaid());
            }
        }
    }

    /**
     * Testing oxOrder::getOrderFiles
     *
     * @return null
     */
    public function testLoadOrderFiles()
    {
        $oOrderFilesList = new oxOrderFileList();
        $oOrderFilesList->loadOrderFiles('_orderId_1');

        $this->assertEquals(2, count($oOrderFilesList));

        foreach ($oOrderFilesList as $oOrderFile) {
            $this->assertEquals('title', $oOrderFile->oxorderfiles__oxarticletitle->value);
            $this->assertEquals('artnum', $oOrderFile->oxorderfiles__oxarticleartnum->value);
        }
    }
}
