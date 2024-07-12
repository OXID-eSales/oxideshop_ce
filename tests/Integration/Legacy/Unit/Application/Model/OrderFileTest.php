<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;
use \oxDb;
use \oxRegistry;

class OrderFileTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        oxDb::getDB()->execute('delete from `oxorderfiles` ');

        parent::tearDown();
    }

    /**
     * Test oxOrderFile setters
     */
    public function testSetOrderFile()
    {
        $oOrderFileNew = oxNew('oxOrderFile');
        $oOrderFileNew->setOrderId('orderId');
        $oOrderFileNew->setOrderArticleId('orderArticleId');
        $oOrderFileNew->setShopId('1');
        $oOrderFileNew->setFile('fileName', 'fileId', '10', '24', '12');
        $oOrderFileNew->save();

        $id = $oOrderFileNew->getId();

        $oOrderFile = oxNew('oxOrderFile');
        $oOrderFile->load($id);

        $sDate = date('Y-m-d', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() + 24 * 3600);

        $this->assertSame('orderId', $oOrderFile->oxorderfiles__oxorderid->value);
        $this->assertSame('orderArticleId', $oOrderFile->oxorderfiles__oxorderarticleid->value);
        $this->assertSame('fileName', $oOrderFile->oxorderfiles__oxfilename->value);
        $this->assertSame('fileId', $oOrderFile->oxorderfiles__oxfileid->value);
        $this->assertSame('fileId', $oOrderFile->getFileId());
        $this->assertSame('1', $oOrderFile->oxorderfiles__oxshopid->value);
        $this->assertSame('10', $oOrderFile->oxorderfiles__oxmaxdownloadcount->value);
        $this->assertSame('12', $oOrderFile->oxorderfiles__oxdownloadexpirationtime->value);
        $this->assertSame('24', $oOrderFile->oxorderfiles__oxlinkexpirationtime->value);
        $this->assertSame(substr($sDate, 0, 10), substr($oOrderFile->oxorderfiles__oxvaliduntil->value, 0, 10));
    }

    /**
     * Test oxOrderFile isValid
     */
    public function testIsValid()
    {
        $oOrderFileNew = oxNew('oxOrderFile');
        $oOrderFileNew->setOrderId('_orderId');

        $oOrderFileNew->oxorderfiles__oxdownloadcount = new oxField("2");
        $oOrderFileNew->oxorderfiles__oxmaxdownloadcount = new oxField('10');
        $oOrderFileNew->oxorderfiles__oxvaliduntil = new oxField("2050-10-20 12:12:00");

        $this->assertTrue($oOrderFileNew->isValid());
    }

    /**
     * Test oxOrderFile isNotValid
     */
    public function testIsNotValid()
    {
        $oOrderFileNew = oxNew('oxOrderFile');
        $oOrderFileNew->setOrderId('_orderId');

        $oOrderFileNew->oxorderfiles__oxdownloadcount = new oxField("10");
        $oOrderFileNew->oxorderfiles__oxmaxdownloadcount = new oxField('10');
        $oOrderFileNew->oxorderfiles__oxvaliduntil = new oxField("2050-10-20 12:12:00");

        $this->assertFalse($oOrderFileNew->isValid());
    }

    /**
     * Test oxOrderFile reset
     */
    public function testReset()
    {
        $oOrderFileNew = oxNew('oxOrderFile');
        $oOrderFileNew->setOrderId('orderId');
        $oOrderFileNew->setOrderArticleId('orderArticleId');
        $oOrderFileNew->setShopId('1');

        $oOrderFileNew->oxorderfiles__oxfileid = new oxField('fileId');
        $oOrderFileNew->oxorderfiles__oxmaxdownloadcount = new oxField('10');
        $oOrderFileNew->oxorderfiles__oxlinkexpirationtime = new oxField('24');
        $oOrderFileNew->oxorderfiles__oxdownloadexpirationtime = new oxField('12');
        $oOrderFileNew->oxorderfiles__oxvaliduntil = new oxField("2011-10-20 12:12:00");
        $oOrderFileNew->oxorderfiles__oxdownloadcount = new oxField("2");
        $oOrderFileNew->oxorderfiles__oxfirstdownload = new oxField("2011-10-10");
        $oOrderFileNew->oxorderfiles__oxlastdownload = new oxField("2011-10-20");
        $oOrderFileNew->save();

        $id = $oOrderFileNew->getId();

        $oOrderFile = oxNew('oxOrderFile');
        $oOrderFile->load($id);

        $this->assertSame('orderId', $oOrderFile->oxorderfiles__oxorderid->value);
        $this->assertSame('orderArticleId', $oOrderFile->oxorderfiles__oxorderarticleid->value);
        $this->assertSame('fileId', $oOrderFile->oxorderfiles__oxfileid->value);
        $this->assertSame('1', $oOrderFile->oxorderfiles__oxshopid->value);
        $this->assertSame('10', $oOrderFile->oxorderfiles__oxmaxdownloadcount->value);
        $this->assertSame('12', $oOrderFile->oxorderfiles__oxdownloadexpirationtime->value);
        $this->assertSame('24', $oOrderFile->oxorderfiles__oxlinkexpirationtime->value);
        $this->assertSame('2011-10-10 00:00:00', $oOrderFile->oxorderfiles__oxfirstdownload->value);
        $this->assertSame('2011-10-20 00:00:00', $oOrderFile->oxorderfiles__oxlastdownload->value);
        $this->assertSame("2011-10-20 12:12:00", $oOrderFile->oxorderfiles__oxvaliduntil->value);

        $iTime = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime();
        $sDate = date('Y-m-d H:i:s', $iTime + 24 * 3600);

        $oOrderFile->reset();
        $oOrderFile->save();

        $oOrderFileReset = oxNew('oxOrderFile');
        $oOrderFileReset->load($id);

        $this->assertSame('0', $oOrderFileReset->oxorderfiles__oxdownloadcount->value);
        $this->assertGreaterThanOrEqual($sDate, $oOrderFileReset->oxorderfiles__oxvaliduntil->value);
        $this->assertSame('0000-00-00 00:00:00', $oOrderFileReset->oxorderfiles__oxfirstdownload->value);
        $this->assertSame('0000-00-00 00:00:00', $oOrderFileReset->oxorderfiles__oxlastdownload->value);
    }

    /**
     * Test valid until date getter
     */
    public function testGetValidUntil()
    {
        $oOrderFileNew = oxNew('oxOrderFile');
        $oOrderFileNew->oxorderfiles__oxvaliduntil = new oxField('2010-10-10 11:23:12');
        $oOrderFileNew->save();

        $id = $oOrderFileNew->getId();

        $oOrderFile = oxNew('oxOrderFile');
        $oOrderFile->load($id);

        $this->assertSame('2010-10-10 11:23', $oOrderFile->getValidUntil());
    }

    /**
     * Test valid until date getter
     */
    public function testGetLeftDownloadCount()
    {
        $oOrderFileNew = oxNew('oxOrderFile');
        $oOrderFileNew->oxorderfiles__oxmaxdownloadcount = new oxField(10);
        $oOrderFileNew->oxorderfiles__oxdownloadcount = new oxField(7);
        $oOrderFileNew->save();

        $id = $oOrderFileNew->getId();

        $oOrderFile = oxNew('oxOrderFile');
        $oOrderFile->load($id);

        $this->assertSame(3, $oOrderFile->getLeftDownloadCount());
    }

    /**
     * Test valid until date getter
     */
    public function testGetLeftDownloadCountNegative()
    {
        $oOrderFileNew = oxNew('oxOrderFile');
        $oOrderFileNew->oxorderfiles__oxmaxdownloadcount = new oxField(7);
        $oOrderFileNew->oxorderfiles__oxdownloadcount = new oxField(10);
        $oOrderFileNew->save();

        $id = $oOrderFileNew->getId();

        $oOrderFile = oxNew('oxOrderFile');
        $oOrderFile->load($id);

        $this->assertSame(0, $oOrderFile->getLeftDownloadCount());
    }

    /**
     * Test oxOrderFile processOrderFile
     */
    public function testProcessOrderFileFirstDownload()
    {
        $oOrderFileNew = oxNew('oxOrderFile');
        $oOrderFileNew->setId('_orderFileId');
        $oOrderFileNew->setOrderId('_orderId');
        $oOrderFileNew->setOrderArticleId('orderArticleId');
        $oOrderFileNew->setShopId('1');
        $oOrderFileNew->setFile('fileName', 'fileId', '10', '24', '12');
        $oOrderFileNew->save();

        $sNowDate = date('Y-m-d H:i:s');

        $sNow = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime();
        $sDate = date('Y-m-d H:i:s', $sNow);

        $oOrderFile = oxNew('oxOrderFile');
        $oOrderFile->load('_orderFileId');

        $sFileId = $oOrderFile->processOrderFile();

        $this->assertSame('_orderId', $oOrderFile->oxorderfiles__oxorderid->value);
        $this->assertSame('orderArticleId', $oOrderFile->oxorderfiles__oxorderarticleid->value);
        $this->assertSame('fileId', $sFileId);
        $this->assertSame('1', $oOrderFile->oxorderfiles__oxshopid->value);
        $this->assertSame('1', $oOrderFile->oxorderfiles__oxdownloadcount->value);
        $this->assertSame('10', $oOrderFile->oxorderfiles__oxmaxdownloadcount->value);
        $this->assertSame('12', $oOrderFile->oxorderfiles__oxdownloadexpirationtime->value);
        $this->assertSame('24', $oOrderFile->oxorderfiles__oxlinkexpirationtime->value);
        $this->assertGreaterThanOrEqual($sNowDate, $oOrderFile->oxorderfiles__oxfirstdownload->value);
        $this->assertGreaterThanOrEqual($sNowDate, $oOrderFile->oxorderfiles__oxlastdownload->value);
        $this->assertGreaterThanOrEqual($sDate, $oOrderFile->oxorderfiles__oxvaliduntil->value);
    }

    /**
     * Test oxOrderFile processOrderFile
     */
    public function testProcessOrderFile()
    {
        $oOrderFileNew = oxNew('oxOrderFile');
        $oOrderFileNew->setId('_orderFileId');
        $oOrderFileNew->setOrderId('_orderId');
        $oOrderFileNew->setOrderArticleId('orderArticleId');
        $oOrderFileNew->setShopId('1');
        $oOrderFileNew->setFile('fileName', 'fileId', '10', '24', '12');

        $oOrderFileNew->oxorderfiles__oxdownloadcount = new oxField("2");
        $oOrderFileNew->oxorderfiles__oxfirstdownload = new oxField("2011-10-10");
        $oOrderFileNew->oxorderfiles__oxlastdownload = new oxField("2011-10-20");
        $oOrderFileNew->save();

        $sLastDate = date('Y-m-d H:i:s');
        $oOrderFile = oxNew('oxOrderFile');
        $oOrderFile->load('_orderFileId');

        $sFileId = $oOrderFile->processOrderFile();

        $this->assertSame('_orderId', $oOrderFile->oxorderfiles__oxorderid->value);
        $this->assertSame('orderArticleId', $oOrderFile->oxorderfiles__oxorderarticleid->value);
        $this->assertSame('fileId', $sFileId);
        $this->assertSame('1', $oOrderFile->oxorderfiles__oxshopid->value);
        $this->assertSame('3', $oOrderFile->oxorderfiles__oxdownloadcount->value);
        $this->assertSame('10', $oOrderFile->oxorderfiles__oxmaxdownloadcount->value);
        $this->assertSame('12', $oOrderFile->oxorderfiles__oxdownloadexpirationtime->value);
        $this->assertSame('24', $oOrderFile->oxorderfiles__oxlinkexpirationtime->value);
        $this->assertSame('2011-10-10 00:00:00', $oOrderFile->oxorderfiles__oxfirstdownload->value);
        $this->assertGreaterThanOrEqual($sLastDate, $oOrderFile->oxorderfiles__oxlastdownload->value);
    }
}
