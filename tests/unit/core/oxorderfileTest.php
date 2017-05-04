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

class Unit_Core_oxOrderFileTest extends OxidTestCase
{

    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        oxDb::getDB()->execute('delete from `oxorderfiles` ');

        parent::tearDown();
    }

    /**
     * Test oxOrderFile setters
     */
    public function testSetOrderFile()
    {
        $oOrderFileNew = new oxOrderFile();
        $oOrderFileNew->setOrderId('orderId');
        $oOrderFileNew->setOrderArticleId('orderArticleId');
        $oOrderFileNew->setShopId('1');
        $oOrderFileNew->setFile('fileName', 'fileId', '10', '24', '12');
        $oOrderFileNew->save();

        $id = $oOrderFileNew->getId();

        $oOrderFile = new oxOrderFile();
        $oOrderFile->load($id);

        $sDate = date('Y-m-d', oxRegistry::get('oxUtilsDate')->getTime() + 24 * 3600);

        $this->assertEquals('orderId', $oOrderFile->oxorderfiles__oxorderid->value);
        $this->assertEquals('orderArticleId', $oOrderFile->oxorderfiles__oxorderarticleid->value);
        $this->assertEquals('fileName', $oOrderFile->oxorderfiles__oxfilename->value);
        $this->assertEquals('fileId', $oOrderFile->oxorderfiles__oxfileid->value);
        $this->assertEquals('fileId', $oOrderFile->getFileId());
        $this->assertEquals('1', $oOrderFile->oxorderfiles__oxshopid->value);
        $this->assertEquals('10', $oOrderFile->oxorderfiles__oxmaxdownloadcount->value);
        $this->assertEquals('12', $oOrderFile->oxorderfiles__oxdownloadexpirationtime->value);
        $this->assertEquals('24', $oOrderFile->oxorderfiles__oxlinkexpirationtime->value);
        $this->assertEquals(substr($sDate, 0, 10), substr($oOrderFile->oxorderfiles__oxvaliduntil->value, 0, 10));

    }

    /**
     * Test oxOrderFile isValid
     */
    public function testIsValid()
    {
        $oOrderFileNew = new oxOrderFile();
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
        $oOrderFileNew = new oxOrderFile();
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
        $oOrderFileNew = new oxOrderFile();
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

        $oOrderFile = new oxOrderFile();
        $oOrderFile->load($id);

        $this->assertEquals('orderId', $oOrderFile->oxorderfiles__oxorderid->value);
        $this->assertEquals('orderArticleId', $oOrderFile->oxorderfiles__oxorderarticleid->value);
        $this->assertEquals('fileId', $oOrderFile->oxorderfiles__oxfileid->value);
        $this->assertEquals('1', $oOrderFile->oxorderfiles__oxshopid->value);
        $this->assertEquals('10', $oOrderFile->oxorderfiles__oxmaxdownloadcount->value);
        $this->assertEquals('12', $oOrderFile->oxorderfiles__oxdownloadexpirationtime->value);
        $this->assertEquals('24', $oOrderFile->oxorderfiles__oxlinkexpirationtime->value);
        $this->assertEquals('2011-10-10 00:00:00', $oOrderFile->oxorderfiles__oxfirstdownload->value);
        $this->assertEquals('2011-10-20 00:00:00', $oOrderFile->oxorderfiles__oxlastdownload->value);
        $this->assertEquals("2011-10-20 12:12:00", $oOrderFile->oxorderfiles__oxvaliduntil->value);

        $iTime = oxRegistry::get('oxUtilsDate')->getTime();
        $sDate = date('Y-m-d H:i:s', $iTime + 24 * 3600);

        $oOrderFile->reset();
        $oOrderFile->save();

        $oOrderFileReset = new oxOrderFile();
        $oOrderFileReset->load($id);

        $this->assertEquals('0', $oOrderFileReset->oxorderfiles__oxdownloadcount->value);
        $this->assertTrue($oOrderFileReset->oxorderfiles__oxvaliduntil->value >= $sDate);
        $this->assertEquals('0000-00-00 00:00:00', $oOrderFileReset->oxorderfiles__oxfirstdownload->value);
        $this->assertEquals('0000-00-00 00:00:00', $oOrderFileReset->oxorderfiles__oxlastdownload->value);

    }

    /**
     * Test valid until date getter
     */
    public function testGetValidUntil()
    {
        $oOrderFileNew = new oxOrderFile();
        $oOrderFileNew->oxorderfiles__oxvaliduntil = new oxField('2010-10-10 11:23:12');
        $oOrderFileNew->save();

        $id = $oOrderFileNew->getId();

        $oOrderFile = new oxOrderFile();
        $oOrderFile->load($id);

        $this->assertEquals('2010-10-10 11:23', $oOrderFile->getValidUntil());
    }

    /**
     * Test valid until date getter
     */
    public function testGetLeftDownloadCount()
    {
        $oOrderFileNew = new oxOrderFile();
        $oOrderFileNew->oxorderfiles__oxmaxdownloadcount = new oxField(10);
        $oOrderFileNew->oxorderfiles__oxdownloadcount = new oxField(7);
        $oOrderFileNew->save();

        $id = $oOrderFileNew->getId();

        $oOrderFile = new oxOrderFile();
        $oOrderFile->load($id);

        $this->assertEquals(3, $oOrderFile->getLeftDownloadCount());
    }

    /**
     * Test valid until date getter
     */
    public function testGetLeftDownloadCountNegative()
    {
        $oOrderFileNew = new oxOrderFile();
        $oOrderFileNew->oxorderfiles__oxmaxdownloadcount = new oxField(7);
        $oOrderFileNew->oxorderfiles__oxdownloadcount = new oxField(10);
        $oOrderFileNew->save();

        $id = $oOrderFileNew->getId();

        $oOrderFile = new oxOrderFile();
        $oOrderFile->load($id);

        $this->assertEquals(0, $oOrderFile->getLeftDownloadCount());
    }

    /**
     * Test oxOrderFile processOrderFile
     */
    public function testProcessOrderFileFirstDownload()
    {
        $oOrderFileNew = new oxOrderFile();
        $oOrderFileNew->setId('_orderFileId');
        $oOrderFileNew->setOrderId('_orderId');
        $oOrderFileNew->setOrderArticleId('orderArticleId');
        $oOrderFileNew->setShopId('1');
        $oOrderFileNew->setFile('fileName', 'fileId', '10', '24', '12');
        $oOrderFileNew->save();

        $sNowDate = date('Y-m-d H:i:s');

        $sNow = oxRegistry::get("oxUtilsDate")->getTime();
        $sDate = date('Y-m-d H:i:s', $sNow);

        $oOrderFile = new oxOrderFile();
        $oOrderFile->load('_orderFileId');
        $sFileId = $oOrderFile->processOrderFile();

        $this->assertEquals('_orderId', $oOrderFile->oxorderfiles__oxorderid->value);
        $this->assertEquals('orderArticleId', $oOrderFile->oxorderfiles__oxorderarticleid->value);
        $this->assertEquals('fileId', $sFileId);
        $this->assertEquals('1', $oOrderFile->oxorderfiles__oxshopid->value);
        $this->assertEquals('1', $oOrderFile->oxorderfiles__oxdownloadcount->value);
        $this->assertEquals('10', $oOrderFile->oxorderfiles__oxmaxdownloadcount->value);
        $this->assertEquals('12', $oOrderFile->oxorderfiles__oxdownloadexpirationtime->value);
        $this->assertEquals('24', $oOrderFile->oxorderfiles__oxlinkexpirationtime->value);
        $this->assertTrue($oOrderFile->oxorderfiles__oxfirstdownload->value >= $sNowDate);
        $this->assertTrue($oOrderFile->oxorderfiles__oxlastdownload->value >= $sNowDate);
        $this->assertTrue($oOrderFile->oxorderfiles__oxvaliduntil->value >= $sDate);
    }

    /**
     * Test oxOrderFile processOrderFile
     */
    public function testProcessOrderFile()
    {
        $oOrderFileNew = new oxOrderFile();
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
        $oOrderFile = new oxOrderFile();
        $oOrderFile->load('_orderFileId');
        $sFileId = $oOrderFile->processOrderFile();

        $this->assertEquals('_orderId', $oOrderFile->oxorderfiles__oxorderid->value);
        $this->assertEquals('orderArticleId', $oOrderFile->oxorderfiles__oxorderarticleid->value);
        $this->assertEquals('fileId', $sFileId);
        $this->assertEquals('1', $oOrderFile->oxorderfiles__oxshopid->value);
        $this->assertEquals('3', $oOrderFile->oxorderfiles__oxdownloadcount->value);
        $this->assertEquals('10', $oOrderFile->oxorderfiles__oxmaxdownloadcount->value);
        $this->assertEquals('12', $oOrderFile->oxorderfiles__oxdownloadexpirationtime->value);
        $this->assertEquals('24', $oOrderFile->oxorderfiles__oxlinkexpirationtime->value);
        $this->assertEquals('2011-10-10 00:00:00', $oOrderFile->oxorderfiles__oxfirstdownload->value);
        $this->assertTrue($oOrderFile->oxorderfiles__oxlastdownload->value >= $sLastDate);
    }
}
