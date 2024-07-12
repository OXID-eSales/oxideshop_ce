<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use oxDb;
use OxidEsales\EshopCommunity\Core\GenericImport\GenericImport;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use OxidTestCase;
use oxUser;
use oxUtilsServer;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Without this class it is not possible to fake log in without errors.
 */
class GenericImportTest_oxUtilsServer extends oxUtilsServer
{
    public function getOxCookie($sName = null)
    {
        return true;
    }
}

class GenericImportTest extends OxidTestCase
{
    protected function tearDown(): void
    {
        $this->cleanUpTable('oxuser');
        parent::tearDown();
    }

    /**
     * Test method init()
     */
    public function testInit()
    {
        $this->addClassExtension(\OxidEsales\EshopCommunity\Tests\Unit\Core\GenericImportTest_oxUtilsServer::class, 'oxUtilsServer');
        $oImport = new GenericImport();

        /** @var oxUser|MockObject $oUser */
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['isAdmin']);
        $oUser->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oUser->login(\OXADMIN_LOGIN, \OXADMIN_PASSWD);
        $oUser->loadAdminUser();

        $this->assertTrue($oImport->init());
    }

    /**
     * Test method init() - with not logged in user
     */
    public function testInitWhenUserIsNotLoggedIn()
    {
        $this->expectException('Exception');

        $oImport = new GenericImport();
        $oImport->init();
    }

    public function testCreationOfImportObject()
    {
        $importer = new GenericImport();

        $importObject = $importer->getImportObject('A');
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Core\GenericImport\ImportObject\Article::class, $importObject);
    }

    /**
     * @return array
     */
    public function providerMapFields()
    {
        return [[['aa', 'bb', 'cc'], ['OXID' => 'oxid', 'OXTITLE' => 'oxtitle', 'OXNAME' => 'oxname'], ['oxid' => 'aa', 'oxtitle' => 'bb', 'oxname' => 'cc']], [['aa', 'bb', 'cc'], ['OXID' => 'oxid', 'OXTITLE' => '', 'OXNAME' => 'oxname'], ['oxid' => 'aa', 'oxname' => 'cc']], [['aa', 'bb', 'NULL'], ['OXID' => 'oxid', 'OXNAME' => 'oxname', 'OXVAT' => 'oxvat'], ['oxid' => 'aa', 'oxname' => 'bb', 'oxvat' => null]]];
    }

    /**
     * @dataProvider providerMapFields
     *
     * @param array $dataToMap
     * @param array $csvFields
     * @param array $mappedData
     */
    public function testMapFields($dataToMap, $csvFields, $mappedData)
    {
        $importObject = $this->getMock(\OxidEsales\EshopCommunity\Core\GenericImport\ImportObject\ImportObject::class, ['import']);
        $importObject->expects($this->once())->method('import')->with($mappedData)->will($this->returnValue(1));

        /** @var GenericImport|MockObject $oImport */
        $oImport = $this->getMock(\OxidEsales\EshopCommunity\Core\GenericImport\GenericImport::class, ['createImportObject', 'checkAccess']);
        $oImport->expects($this->any())->method('createImportObject')->will($this->returnValue($importObject));

        $oImport->setImportType('A');
        $oImport->setCsvFileFieldsOrder($csvFields);
        $oImport->importData([$dataToMap]);
    }

    public function testCalculationOfImportedRows()
    {
        $importObject = $this->getMock(\OxidEsales\EshopCommunity\Core\GenericImport\ImportObject\ImportObject::class, ['import']);
        $importObject->expects($this->any())->method('import')->will($this->returnValue(1));

        /** @var GenericImport|MockObject $oImport */
        $oImport = $this->getMock(\OxidEsales\EshopCommunity\Core\GenericImport\GenericImport::class, ['createImportObject', 'checkAccess']);
        $oImport->expects($this->any())->method('createImportObject')->will($this->returnValue($importObject));

        $this->assertEquals(0, $oImport->getImportedRowCount());

        $oImport->setImportType('A');
        $oImport->setCsvFileFieldsOrder(['OXID' => 'oxid', 'OXTITLE' => 'oxtitle', 'OXNAME' => 'oxname']);
        $oImport->importData([['aa', 'bb', 'cc']]);

        $this->assertEquals(1, $oImport->getImportedRowCount());
    }

    /**
     * Test method importFile() - if an exception is thrown when user is not logged in
     */
    public function testDoImportFailsWhenUserIsNotLoggedIn()
    {
        $importer = new GenericImport();
        $this->assertEquals('ERPGENIMPORT_ERROR_USER_NO_RIGHTS', $importer->importFile());
    }

    /**
     * Test method importFile() - if fails when bad import file specified
     */
    public function testDoImportFailsWhenImportFileNotFound()
    {
        /** @var GenericImport|MockObject $oImport */
        $oImport = $this->getMock(\OxidEsales\EshopCommunity\Core\GenericImport\GenericImport::class, ['init']);
        $oImport->expects($this->once())->method('init')->will($this->returnValue(true));

        $this->assertEquals('ERPGENIMPORT_ERROR_WRONG_FILE', $oImport->importFile('nosuchfile'));
    }

    /**
     * Test method importFile()
     */
    public function testDoImport()
    {
        /** @var GenericImport|MockObject $oImport */
        $oImport = $this->getMock(\OxidEsales\EshopCommunity\Core\GenericImport\GenericImport::class, ['init', 'checkAccess']);
        $oImport->expects($this->once())->method('init')->will($this->returnValue(true));
        $oImport->expects($this->any())->method('checkAccess')->will($this->returnValue(true));

        $oImport->setCsvContainsHeader(true);
        $oImport->setImportType('U');
        $oImport->setCsvFileFieldsOrder(["OXID", "OXACTIVE", "OXSHOPID", "OXUSERNAME", "OXFNAME", "OXLNAME"]);

        $csvWithHeaders = $this->createCsvFile(true);
        $oImport->importFile($csvWithHeaders);

        $shopId = ShopIdCalculator::BASE_SHOP_ID;
        $aTestData1 = [["_testId1", "1", $shopId, "userName1", "FirstName1", "LastName1"]];
        $aTestData2 = [["_testId2", "1", $shopId, "userName2", "FirstName2", "LastName2"]];

        $aUser1 = oxDb::getDb()->getAll("select OXID, OXACTIVE, OXSHOPID, OXUSERNAME, OXFNAME, OXLNAME from oxuser where oxid='_testId1'");
        $aUser2 = oxDb::getDb()->getAll("select OXID, OXACTIVE, OXSHOPID, OXUSERNAME, OXFNAME, OXLNAME from oxuser where oxid='_testId2'");

        $this->assertEquals($aTestData1, $aUser1);
        $this->assertEquals($aTestData2, $aUser2);
    }

    /**
     * Test method importFile() - if skips header line
     */
    public function testDoImportSkipsHeaderLine()
    {
        /** @var GenericImport|MockObject $oImport */
        $oImport = $this->getMock(\OxidEsales\EshopCommunity\Core\GenericImport\GenericImport::class, ['init', 'checkAccess']);
        $oImport->expects($this->once())->method('init')->will($this->returnValue(true));
        $oImport->expects($this->any())->method('checkAccess')->will($this->returnValue(true));

        $oImport->setCsvContainsHeader(true);
        $oImport->setImportType('U');
        $oImport->setCsvFileFieldsOrder(["OXID", "OXACTIVE", "OXSHOPID", "OXUSERNAME", "OXFNAME", "OXLNAME"]);

        //checking if header line was not saved to DB
        $csvWithHeaders = $this->createCsvFile(true);
        $oImport->importFile($csvWithHeaders);
        $this->assertEquals(2, count($oImport->getStatistics()));
        $this->assertFalse(oxDb::getDb()->getOne("select OXID from oxuser where oxid='OXID'"));
    }

    /**
     * Test method importFile() - when no header line is in csv file
     */
    public function testDoImportWithCsvWithoutHeaderLine()
    {
        /** @var GenericImport|MockObject $oImport */
        $oImport = $this->getMock(\OxidEsales\EshopCommunity\Core\GenericImport\GenericImport::class, ['init', 'checkAccess']);
        $oImport->expects($this->once())->method('init')->will($this->returnValue(true));
        $oImport->expects($this->any())->method('checkAccess')->will($this->returnValue(true));

        $oImport->setCsvContainsHeader(false);
        $oImport->setImportType('U');
        $oImport->setCsvFileFieldsOrder(["OXID", "OXACTIVE", "OXSHOPID", "OXUSERNAME", "OXFNAME", "OXLNAME"]);

        //checking if first line from csv file was saved to DB
        $csvWithoutHeaders = $this->createCsvFile(false);
        $oImport->importFile($csvWithoutHeaders);
        $this->assertEquals('_testId1', oxDb::getDb()->getOne("select oxid from oxuser where oxid='_testId1'"));
    }

    /**
     * Creates Csv file with header and returns path to it.
     *
     * @param bool $addHeaders
     *
     * @return string Csv file path.
     */
    private function createCsvFile($addHeaders = true)
    {
        $content = '"_testId1";"1";"1";"userName1";"FirstName1";"LastName1"' . "\n";
        $content .= '"_testId2";"1";"1";"userName2";"FirstName2";"LastName2"' . "\n";
        if ($addHeaders) {
            $content = '"OXID";"OXACTIVE";"OXSHOPID";"OXUSERNAME";"OXFNAME";"OXLNAME"' . "\n" . $content;
        }

        return $this->createFile('csvWithHeader.csv', $content);
    }
}
