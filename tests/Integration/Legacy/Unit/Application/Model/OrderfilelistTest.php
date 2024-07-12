<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;
use \oxDb;
use OxidEsales\Eshop\Core\Field;

class OrderfilelistTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $oOrder = oxNew('oxOrder');
        $oOrder->setId('_orderId_1');

        $oOrder->oxorder__oxuserid = new oxField('_userId');
        $oOrder->save();

        $oOrder = oxNew('oxOrder');
        $oOrder->setId('_orderId_2');

        $oOrder->oxorder__oxpaid = new oxField('2011-01-10 12:12:12');
        $oOrder->oxorder__oxuserid = new oxField('_userId');
        $oOrder->save();

        $oOrderArticle = oxNew('oxOrderArticle');
        $oOrderArticle->setId('_orderArticleId_1');

        $oOrderArticle->oxorderarticles__oxtitle = new oxField('title');
        $oOrderArticle->oxorderarticles__oxartnum = new oxField('artnum');
        $oOrderArticle->oxorderarticles__oxorderid = new Field($oOrder->getId());
        $oOrderArticle->save();

        $oOrderArticle = oxNew('oxOrderArticle');
        $oOrderArticle->setId('_orderArticleId_2');

        $oOrderArticle->oxorderarticles__oxtitle = new oxField('title');
        $oOrderArticle->oxorderarticles__oxartnum = new oxField('artnum');
        $oOrderArticle->oxorderarticles__oxorderid = new Field($oOrder->getId());
        $oOrderArticle->save();

        $oOrderFile1 = oxNew('oxOrderFile');
        $oOrderFile1->setOrderId('_orderId_1');
        $oOrderFile1->setOrderArticleId('_orderArticleId_1');
        $oOrderFile1->setFile('_fileName_1', '_fileId_1', 10, 24, 12);
        $oOrderFile1->save();

        $oOrderFile2 = oxNew('oxOrderFile');
        $oOrderFile2->setOrderId('_orderId_1');
        $oOrderFile2->setOrderArticleId('_orderArticleId_1');
        $oOrderFile2->setFile('_fileName_2', '_fileId_2', 10, 24, 12);
        $oOrderFile2->save();


        $oOrderFile3 = oxNew('oxOrderFile');
        $oOrderFile3->setOrderId('_orderId_2');
        $oOrderFile3->setOrderArticleId('_orderArticleId_2');
        $oOrderFile3->setFile('_fileName_3', '_fileId_3', 10, 24, 12);
        $oOrderFile3->save();

        $oOrderFile4 = oxNew('oxOrderFile');
        $oOrderFile4->setOrderId('_orderId_1');
        $oOrderFile4->setOrderArticleId('_orderArticleId_1');
        $oOrderFile4->setShopId('_shopId');
        $oOrderFile4->setFile('_fileName_2', '_fileId_2', 10, 24, 12);
        $oOrderFile4->save();
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $this->cleanUpTable('oxorder');
        $this->cleanUpTable('oxorderarticles');

        $oDb = oxDb::getDb();
        $oDb->execute("TRUNCATE TABLE `oxorderfiles`");

        parent::tearDown();
    }

    /**
     * Testing oxOrder::getOrderFiles
     */
    public function testLoadUserFiles()
    {
        $oUserFilesList = oxNew('oxOrderFileList');
        $oUserFilesList->loadUserFiles('_userId');

        $this->assertCount(3, $oUserFilesList);

        foreach ($oUserFilesList as $oUserFile) {
            $this->assertSame('title', $oUserFile->oxorderfiles__oxarticletitle->value);
            $this->assertSame('artnum', $oUserFile->oxorderfiles__oxarticleartnum->value);
            if ($oUserFile->oxorderfiles__oxorderid->value == '_orderId_2') {
                $this->assertSame(1, $oUserFile->isPaid());
            } else {
                $this->assertSame(0, $oUserFile->isPaid());
            }
        }
    }

    /**
     * Testing oxOrder::getOrderFiles
     */
    public function testLoadOrderFiles()
    {
        $oOrderFilesList = oxNew('oxOrderFileList');
        $oOrderFilesList->loadOrderFiles('_orderId_1');

        $this->assertCount(2, $oOrderFilesList);

        foreach ($oOrderFilesList as $oOrderFile) {
            $this->assertSame('title', $oOrderFile->oxorderfiles__oxarticletitle->value);
            $this->assertSame('artnum', $oOrderFile->oxorderfiles__oxarticleartnum->value);
        }
    }
}
