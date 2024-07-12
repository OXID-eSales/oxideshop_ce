<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxDb;

class DbMetaDataHandlerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $this->cleanUpTable('oxactions');

        //dorpping test table
        oxDb::getDb()->execute("DROP TABLE IF EXISTS `testDbMetaDataHandler`");

        parent::tearDown();
    }

    /**
     * Create a test table
     */
    protected function createTestTable()
    {
        $sSql = " CREATE TABLE `testDbMetaDataHandler` (
                    `OXID` char(32) NOT NULL,
                    `OXTITLE` varchar(255) NOT NULL,
                    `OXTITLE_1` varchar(255) NOT NULL,
                    `OXLONGDESC` text NOT NULL,
                    `OXLONGDESC_1` text NOT NULL,
                     PRIMARY KEY (`OXID`),
                     KEY `OXTITLE` (`OXTITLE`),
                     KEY `OXTITLE_1` (`OXTITLE_1`),
                     FULLTEXT KEY `OXLONGDESC` (`OXLONGDESC`),
                     FULLTEXT KEY `OXLONGDESC_1` (`OXLONGDESC_1`)
                  ) ENGINE = MyISAM";

        oxDb::getDb()->execute($sSql);
    }

    /**
     * Create a test table without indices
     */
    protected function createTestTableWithoutIndices()
    {
        $sSql = "CREATE TABLE `testDbMetaDataHandlerWithoutIndices` (`OXID` char(32) NOT NULL) ENGINE = InnoDB";

        oxDb::getDb()->execute($sSql);
    }

    /**
     * Drop a test table without indices
     */
    protected function dropTestTableWithoutIndices()
    {
        $sSql = "DROP TABLE `testDbMetaDataHandlerWithoutIndices`";

        oxDb::getDb()->execute($sSql);
    }

    /**
     * Test getting table fields list
     */
    public function testGetFields()
    {
        $sTable = 'oxreviews';
        $aTestFields = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getAll('show columns from ' . $sTable);
        $aFields = [];

        foreach ($aTestFields as $aField) {
            $aFields[$aField['Field']] = sprintf('%s.%s', $sTable, $aField['Field']);
        }

        $oDbMeta = oxNew("oxDbMetaDataHandler");
        $aTableFields = $oDbMeta->getFields("oxreviews");

        $this->assertGreaterThan(0, count($aTableFields));
        $this->assertEquals($aFields, $aTableFields);
    }

    /**
     * Test if field name exists in given table
     */
    public function testFieldExists()
    {
        $oDbMeta = oxNew("oxDbMetaDataHandler");
        $this->assertTrue($oDbMeta->fieldExists("oxuserid", "oxreviews"));
        $this->assertTrue($oDbMeta->fieldExists("OXUSERID", "oxreviews"));
        $this->assertFalse($oDbMeta->fieldExists("oxblablabla", "oxreviews"));
        $this->assertFalse($oDbMeta->fieldExists("", "oxreviews"));
    }

    /**
     * Test if field name (camelCase) exists in given table
     */
    public function testFieldExistsCamelCase()
    {
        $oDbMeta = $this->getMock(\OxidEsales\Eshop\Core\DbMetaDataHandler::class, ['getFields']);
        $oDbMeta->expects($this->once())->method('getFields')->with('oxreviews')->willReturn(["oxreviews.field1", 'oxreviews.field2Name', 'oxreviews.FIELD']);

        $this->assertTrue($oDbMeta->fieldExists("field2Name", "oxreviews"));
    }

    /**
     * Test, that the method getIndices gives back an empty array in case of a not existing table
     */
    public function testGetIndicesWithNotExistingTable()
    {
        $dbMetaDataHandler = oxNew(\OxidEsales\EshopCommunity\Core\DbMetaDataHandler::class);

        $indices = $dbMetaDataHandler->getIndices('NOT_EXISTING_TABLE_NAME');

        $this->assertEmpty($indices);
    }

    /**
     * Test, that the method getIndices gives back an empty array in case of a table with no indices
     */
    public function testGetIndicesWithTableWithoutIndices()
    {
        $this->createTestTableWithoutIndices();

        $dbMetaDataHandler = oxNew(\OxidEsales\EshopCommunity\Core\DbMetaDataHandler::class);

        $indices = $dbMetaDataHandler->getIndices('testDbMetaDataHandlerWithoutIndices');

        $this->dropTestTableWithoutIndices();
        $this->assertCount(0, $indices);
    }

    /**
     * Test, that the method getIndices gives back all indices in case of a table with indices
     */
    public function testGetIndicesWithTableWithIndices()
    {
        $this->createTestTable();
        $dbMetaDataHandler = oxNew(\OxidEsales\EshopCommunity\Core\DbMetaDataHandler::class);

        $indices = $dbMetaDataHandler->getIndices('testDbMetaDataHandler');

        $this->assertCount(5, $indices);
        $this->assertSame('OXID', $indices[0]['Column_name']);
        $this->assertSame('OXTITLE', $indices[1]['Column_name']);
        $this->assertSame('OXTITLE_1', $indices[2]['Column_name']);
        $this->assertSame('OXLONGDESC', $indices[3]['Column_name']);
        $this->assertSame('OXLONGDESC_1', $indices[4]['Column_name']);
    }

    /**
     * Test, that the method hasIndex gives back false, if we check for a non existing index name
     */
    public function testHasIndexWithNotExistingIndex()
    {
        $dbMetaDataHandler = oxNew(\OxidEsales\EshopCommunity\Core\DbMetaDataHandler::class);

        $this->assertFalse($dbMetaDataHandler->hasIndex('NON_EXISTENT_INDEX_NAME', 'oxarticles'));
    }

    /**
     * Test, that the method hasIndex gives back false, if we check for a non existing index name
     */
    public function testHasIndexWithExistingIndex()
    {
        $dbMetaDataHandler = oxNew(\OxidEsales\EshopCommunity\Core\DbMetaDataHandler::class);

        $this->assertTrue($dbMetaDataHandler->hasIndex('OXID', 'oxarticles'));
    }

    /**
     * Test, that the method getIndexByName gives back null, if we search for a not existing table
     */
    public function testGetIndexByNameWithNotExistingTable()
    {
        $dbMetaDataHandler = oxNew(\OxidEsales\EshopCommunity\Core\DbMetaDataHandler::class);

        $this->assertNull($dbMetaDataHandler->getIndexByName('OXID', 'NOT_EXISTANT_TABLE_NAME'));
    }

    /**
     * Test, that the method getIndexByName gives back null, if we search for a not existing index name
     */
    public function testGetIndexByNameWithNotExistingName()
    {
        $dbMetaDataHandler = oxNew(\OxidEsales\EshopCommunity\Core\DbMetaDataHandler::class);

        $this->assertNull($dbMetaDataHandler->getIndexByName('NON_EXISTANT_INDEX_NAME', 'oxarticles'));
    }

    /**
     * Test, that the method getIndexByName gives back the correct index array, if we search for an existing index name
     */
    public function testGetIndexByNameWithExistingName()
    {
        $dbMetaDataHandler = oxNew(\OxidEsales\EshopCommunity\Core\DbMetaDataHandler::class);

        $index = $dbMetaDataHandler->getIndexByName('OXID', 'oxarticles');

        $this->assertNotNull($index);
        $this->assertSame('PRIMARY', $index['Key_name']);
    }

    /**
     * Test getting all db tables list (except views)
     */
    public function testGetAllTables()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }

        $aTables = oxDb::getDb()->getAll("show tables");

        foreach ($aTables as $aTableInfo) {
            $aTablesList[] = $aTableInfo[0];
        }

        $oDbMeta = oxNew("oxDbMetaDataHandler");
        $this->assertGreaterThan(1, count($aTablesList));
        $this->assertEquals($aTablesList, $oDbMeta->getAllTables());
    }

    /**
     * Test if returned sql for creating new table set is correct
     */
    public function testGetCreateTableSetSql()
    {
        $sTestSql = "CREATE TABLE `oxcountry_set1` (`OXID` char(32) NOT NULL, PRIMARY KEY (`OXID`)) DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci ENGINE= InnoDB COMMENT='Countries list'";

        $oDbMeta = $this->getProxyClass("oxDbMetaDataHandler");

        //comparing in case insensitive form
        $this->assertEqualsIgnoringCase(
            $sTestSql,
            $oDbMeta->getCreateTableSetSql("oxcountry", 8),
            ''
        );
    }

    /**
     * Test if returned sql for creating new field is correct
     */
    public function testGetAddFieldSql()
    {
        $sTestSql = "alter TABLE `oxcountry` ADD `OXTITLE_4` varchar(128) NOT NULL default ''  AFTER `OXTITLE_3`";

        $oDbMeta = $this->getProxyClass("oxDbMetaDataHandler");

        //comparing in case insensitive form
        $this->assertEqualsIgnoringCase(
            $sTestSql,
            $oDbMeta->getAddFieldSql("oxcountry", "OXTITLE", "OXTITLE_4", "OXTITLE_3"),
            ''
        );
    }

    /**
     * Test if returned sql for creating new field indexes is correct
     */
    public function testGetAddFieldIndexSql()
    {
        $this->createTestTable();
        $dbMetaDataHandler = oxNew(\OxidEsales\EshopCommunity\Core\DbMetaDataHandler::class);

        $expectedSqls = [
            "ALTER TABLE `testDbMetaDataHandler` ADD KEY  (`OXTITLE_4`)",
            "ALTER TABLE `testDbMetaDataHandler` ADD FULLTEXT KEY  (`OXLONGDESC_4`)",
            "ALTER TABLE `testDbMetaDataHandler` ADD FULLTEXT KEY  (`OXLONGDESC_5`)",
            "ALTER TABLE `testDbMetaDataHandler_set1` ADD FULLTEXT KEY  (`OXLONGDESC_8`)",
            "ALTER TABLE `testDbMetaDataHandler_set2` ADD FULLTEXT KEY  (`OXLONGDESC_20`)"
        ];

        $resultSqls = [
            $dbMetaDataHandler->getAddFieldIndexSql("testDbMetaDataHandler", "OXTITLE", "OXTITLE_4"),
            $dbMetaDataHandler->getAddFieldIndexSql("testDbMetaDataHandler", "OXLONGDESC", "OXLONGDESC_4"),
            $dbMetaDataHandler->getAddFieldIndexSql("testDbMetaDataHandler", "OXLONGDESC", "OXLONGDESC_5"),
            $dbMetaDataHandler->getAddFieldIndexSql("testDbMetaDataHandler", "OXLONGDESC", "OXLONGDESC_8", "testDbMetaDataHandler_set1"),
            $dbMetaDataHandler->getAddFieldIndexSql("testDbMetaDataHandler", "OXLONGDESC", "OXLONGDESC_20", "testDbMetaDataHandler_set2")
        ];

        foreach ($expectedSqls as $index => $value) {
            $this->assertSame([$value], $resultSqls[$index]);
        }
    }

    /**
     * Test getting current max lang base id
     */
    public function testGetCurrentMaxLangId()
    {
        $oDbMeta = oxNew("oxDbMetaDataHandler");

        $this->assertSame(3, $oDbMeta->getCurrentMaxLangId());
    }

    /**
     * Test getting next available base lang id
     */
    public function testGetNextLangId()
    {
        $oDbMeta = oxNew("oxDbMetaDataHandler");

        $this->assertSame(4, $oDbMeta->getNextLangId());
    }

    /**
     * Test getting multilang fields from selected table
     */
    public function testGetMultilangFields()
    {
        $oDbMeta = oxNew("oxDbMetaDataHandler");
        $aRes = ["OXTITLE", "OXSHORTDESC", "OXLONGDESC"];

        $this->assertSame($aRes, $oDbMeta->getMultilangFields("oxcountry"));
    }

    /**
     * Test getting multilang fields from selected table which does not has any
     * multilang field
     */
    public function testGetMultilangFieldsFromNonMultilangTable()
    {
        $oDbMeta = oxNew("oxDbMetaDataHandler");
        $aRes = [];

        $this->assertSame($aRes, $oDbMeta->getMultilangFields("oxuser"));
    }

    /**
     * Checks wether getSingleLangField returns fields in associative array
     */
    public function testGetSingleLangFields()
    {
        $aFields = ['OXID'       => "OXID", 'OXSHOPID_1' => "oxarticles.OXSHOPID_1", 'OXPARENTID' => "oxarticles.OXPARENTID", 'OXACTIVE'   => "oxarticles.OXACTIVE"];

        $aExpectedFields = ['OXID', 'OXSHOPID', 'OXPARENTID', 'OXACTIVE'];

        /** @var oxDbMetaDataHandler|PHPUnit\Framework\MockObject\MockObject $oDbMeta */
        $oDbMeta = $this->getMock(\OxidEsales\Eshop\Core\DbMetaDataHandler::class, ['getFields']);
        $oDbMeta->method('getFields')->willReturn($aFields);
        $this->assertSame($aExpectedFields, array_keys($oDbMeta->getSinglelangFields('oxarticles', 1)));
    }

    /**
     * Test if method collects sql for creating table new multilang fields
     */
    public function testAddNewMultilangFieldAlterTable()
    {
        $aTestSql[] = "ALTER TABLE `oxcountry` ADD `OXTITLE_4` varchar(128) NOT NULL DEFAULT ''  AFTER `OXTITLE_3`";
        $aTestSql[] = "ALTER TABLE `oxcountry` ADD `OXSHORTDESC_4` varchar(255) NOT NULL DEFAULT ''  AFTER `OXSHORTDESC_3`";
        $aTestSql[] = "ALTER TABLE `oxcountry` ADD `OXLONGDESC_4` varchar(255) NOT NULL DEFAULT ''  AFTER `OXLONGDESC_3`";

        /** @var oxDbMetaDataHandler|PHPUnit\Framework\MockObject\MockObject $oDbMeta */
        $oDbMeta = $this->getMock(\OxidEsales\Eshop\Core\DbMetaDataHandler::class, ['executeSql']);

        $oDbMeta->expects($this->once())->method('executeSql')->with($aTestSql); //case insensitive

        $oDbMeta->addNewMultilangField("oxcountry");
    }

    /**
     * Test if method collects sql for creating table new multilang fields
     */
    public function testAddNewMultilangFieldCreateTable()
    {
        $aTestSql[] = "CREATE TABLE `oxcountry_set1` (`OXID` char(32) NOT NULL, PRIMARY KEY (`OXID`)) DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci ENGINE= InnoDB COMMENT='Countries list'";
        $aTestSql[] = "ALTER TABLE `oxcountry_set1` ADD `OXTITLE_8` varchar(128) NOT NULL DEFAULT '' ";
        $aTestSql[] = "ALTER TABLE `oxcountry_set1` ADD `OXSHORTDESC_8` varchar(255) NOT NULL DEFAULT '' ";
        $aTestSql[] = "ALTER TABLE `oxcountry_set1` ADD `OXLONGDESC_8` varchar(255) NOT NULL DEFAULT '' ";

        /** @var oxDbMetaDataHandler|PHPUnit\Framework\MockObject\MockObject $oDbMeta */
        $oDbMeta = $this->getMock(\OxidEsales\Eshop\Core\DbMetaDataHandler::class, ['executeSql', 'getCurrentMaxLangId']);
        $oDbMeta->method('getCurrentMaxLangId')->willReturn(7);
        $oDbMeta->expects($this->once())->method('executeSql')->with($aTestSql); //case insensitive

        $oDbMeta->addNewMultilangField("oxcountry");
    }

    /**
     * Testing real db table update on adding new fields
     */
    public function testAddNewMultilangFieldUpdatesTable()
    {
        $this->createTestTable();

        /** @var oxDbMetaDataHandler|PHPUnit\Framework\MockObject\MockObject $oDbMeta */
        $oDbMeta = $this->getMock(\OxidEsales\Eshop\Core\DbMetaDataHandler::class, ['getCurrentMaxLangId']);
        $oDbMeta->method('getCurrentMaxLangId')->willReturn(1);

        $oDbMeta->addNewMultilangField("testDbMetaDataHandler");

        $aTestFields = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getAll("show columns from testDbMetaDataHandler");
        $aFileds = [];

        foreach ($aTestFields as $aField) {
            $aFileds[] = $aField["Field"];
        }

        $this->assertContains("OXTITLE_2", $aFileds);
        $this->assertContains("OXLONGDESC_2", $aFileds);
    }

    /**
     * Testing real db table update on adding correct indexes
     */
    public function testAddNewMultilangFieldUpdatesTableIndexes()
    {
        $this->createTestTable();

        $oDbMeta = $this->getMock(\OxidEsales\Eshop\Core\DbMetaDataHandler::class, ['getCurrentMaxLangId']);
        $oDbMeta->method('getCurrentMaxLangId')->willReturn(1);

        /** @var oxDbMetaDataHandler|PHPUnit\Framework\MockObject\MockObject $oDbMeta */
        $oDbMeta->addNewMultilangField("testDbMetaDataHandler");

        $aIndexes = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getAll("show index from testDbMetaDataHandler");

        $this->assertSame("PRIMARY", $aIndexes[0]["Key_name"]);
        $this->assertSame("OXID", $aIndexes[0]["Column_name"]);

        //checking newly added index for column OXTITLE
        $this->assertSame("OXTITLE", $aIndexes[1]["Key_name"]);
        $this->assertSame("OXTITLE", $aIndexes[1]["Column_name"]);
        $this->assertSame("OXTITLE_1", $aIndexes[2]["Key_name"]);
        $this->assertSame("OXTITLE_1", $aIndexes[2]["Column_name"]);
        $this->assertSame("OXTITLE_2", $aIndexes[3]["Key_name"]);
        $this->assertSame("OXTITLE_2", $aIndexes[3]["Column_name"]);


        //checking newly added index for column OXLONGDESC
        $this->assertSame("OXLONGDESC", $aIndexes[4]["Key_name"]);
        $this->assertSame("OXLONGDESC", $aIndexes[4]["Column_name"]);
        $this->assertSame("OXLONGDESC_1", $aIndexes[5]["Key_name"]);
        $this->assertSame("OXLONGDESC_1", $aIndexes[5]["Column_name"]);
        $this->assertSame("OXLONGDESC_2", $aIndexes[6]["Key_name"]);
        $this->assertSame("OXLONGDESC_2", $aIndexes[6]["Column_name"]);
        $this->assertSame("FULLTEXT", $aIndexes[6]["Index_type"]);
    }

    /**
     * Test, that the ensuration is called for all language ids for the given table.
     */
    public function testEnsureAllMultiLanguageFields()
    {
        $tableName = 'OXTESTTABLE';

        $dbMetaDataHandler = $this->getMock(\OxidEsales\Eshop\Core\DbMetaDataHandler::class, ['getCurrentMaxLangId', 'ensureMultiLanguageFields']);
        $dbMetaDataHandler->expects($this->once())->method('getCurrentMaxLangId')->willReturn(8);
        $dbMetaDataHandler->expects($this->exactly(8))->method('ensureMultiLanguageFields')->withConsecutive(
            [$this->equalTo($tableName), $this->equalTo(1)],
            [$this->equalTo($tableName), $this->equalTo(2)],
            [$this->equalTo($tableName), $this->equalTo(3)],
            [$this->equalTo($tableName), $this->equalTo(4)],
            [$this->equalTo($tableName), $this->equalTo(5)],
            [$this->equalTo($tableName), $this->equalTo(6)],
            [$this->equalTo($tableName), $this->equalTo(7)],
            [$this->equalTo($tableName), $this->equalTo(8)]
        );

        $dbMetaDataHandler->ensureAllMultiLanguageFields($tableName);
    }

    /**
     * Testing real db table update on adding correct indexes
     */
    public function testAddNewMultilangFieldAddsTable()
    {
        $this->createTestTable();

        /** @var oxDbMetaDataHandler|PHPUnit\Framework\MockObject\MockObject $oDbMeta */
        $oDbMeta = $this->getMock(\OxidEsales\Eshop\Core\DbMetaDataHandler::class, ['getCurrentMaxLangId']);
        $oDbMeta->method('getCurrentMaxLangId')->willReturn(8);

        $oDbMeta->addNewMultilangField("testDbMetaDataHandler");
    }

    /**
     * Test if method call another method which creates sql's with correct params
     */
    public function testAddNewLangToDb()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }

        $aTables = oxDb::getDb()->getAll("show tables");

        $aTablesList = [];
        foreach ($aTables as $aTableInfo) {
            $aTablesList[] = $aTableInfo[0];
        }

        /** @var oxDbMetaDataHandler|PHPUnit\Framework\MockObject\MockObject $oDbMeta */
        $oDbMeta = $this->getMock(\OxidEsales\Eshop\Core\DbMetaDataHandler::class, ['addNewMultilangField']);

        $firstTableName = reset($aTablesList);
        $oDbMeta
            ->method('addNewMultilangField')
            ->withConsecutive([$firstTableName]);

        $oDbMeta->addNewLangToDb();
    }

    /**
     * Test method executes sql's array
     */
    public function testExecuteSql()
    {
        $aSql[] = " insert into oxactions set oxid='_testId1', oxtitle = 'testValue1' ";
        $aSql[] = " insert into oxactions set oxid='_testId2', oxtitle = 'testValue2' ";

        $oDbMeta = $this->getProxyClass("oxDbMetaDataHandler");
        $oDbMeta->executeSql($aSql);

        $oDb = oxDb::getDb();
        $this->assertSame(1, $oDb->getOne("select 1 from oxactions where oxid='_testId1' and oxtitle = 'testValue1' "));
        $this->assertSame(1, $oDb->getOne("select 1 from oxactions where oxid='_testId2' and oxtitle = 'testValue2' "));
    }

    /**
     * Test if nothing is executed if param is not an array
     */
    public function testExecuteSqlWhenParamIsNotSql()
    {
        $sSql = " insert into oxactions set oxid='_testId1', oxtitle = 'testValue1' ";

        $oDbMeta = $this->getProxyClass("oxDbMetaDataHandler");
        $oDbMeta->executeSql($sSql);

        $oDb = oxDb::getDb();
        $this->assertFalse($oDb->getOne("select 1 from oxactions where oxid='_testId1' and oxtitle = 'testValue1' "));
    }

    /**
     * Testing reseting multilangual fields
     */
    public function testResetMultilangFields()
    {
        $this->createTestTable();

        oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
        $oDbMeta = $this->getProxyClass("oxDbMetaDataHandler");

        // inserting test data
        $sSql = "INSERT INTO testDbMetaDataHandler SET
                    OXTITLE = 'aaa',
                    OXLONGDESC = 'bbb',
                    OXTITLE_1 = 'aaa 1',
                    OXLONGDESC_1 = 'bbb 1'
                ";

        oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->execute($sSql);

        $oDbMeta->resetMultilangFields(1, "testDbMetaDataHandler");

        $aRes = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getAll("SELECT * FROM testDbMetaDataHandler");

        $this->assertSame("aaa", $aRes[0]["OXTITLE"]);
        $this->assertSame("bbb", $aRes[0]["OXLONGDESC"]);
        $this->assertSame("", $aRes[0]["OXTITLE_1"]);
        $this->assertSame("", $aRes[0]["OXLONGDESC_1"]);
    }

    /**
     * Testing reseting multilangual fields when language ID is zero
     */
    public function testResetMultilangFields_skipsResetingWhenIdIsZero()
    {
        $this->createTestTable();

        oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
        $oDbMeta = $this->getProxyClass("oxDbMetaDataHandler");

        // inserting test data
        $sSql = "INSERT INTO testDbMetaDataHandler SET
                    OXTITLE = 'aaa',
                    OXLONGDESC = 'bbb'
                ";

        oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->execute($sSql);

        $oDbMeta->resetMultilangFields(0, "testDbMetaDataHandler");

        $aRes = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getAll("SELECT * from testDbMetaDataHandler");

        $this->assertSame("aaa", $aRes[0]["OXTITLE"]);
        $this->assertSame("bbb", $aRes[0]["OXLONGDESC"]);
    }

    /**
     * Testing if method calls method "resetMultilangFields" with correct params
     */
    public function testResetLanguage()
    {
        $oDbMeta = $this->getMock(\OxidEsales\Eshop\Core\DbMetaDataHandler::class, ['getAllTables', 'resetMultilangFields']);
        $oDbMeta->expects($this->once())->method('getAllTables')->willReturn(["testTable1", "testTable2"]);
        $oDbMeta
            ->method('resetMultilangFields')
            ->withConsecutive(
                [1, 'testTable1'],
                [1, 'testTable2'],
            );

        $oDbMeta->resetLanguage(1, "testDbMetaDataHandler");
    }

    /**
     * Testing if method does nothing then language id = 0
     */
    public function testResetLanguage_zeroId()
    {
        $oDbMeta = $this->getMock(\OxidEsales\Eshop\Core\DbMetaDataHandler::class, ['getAllTables', 'resetMultilangFields']);
        $oDbMeta->expects($this->never())->method('getAllTables');
        $oDbMeta->expects($this->never())->method('resetMultilangFields');

        $oDbMeta->resetLanguage(0, "testDbMetaDataHandler");
    }

    /**
     * Testing if method skips tables that does not requires reset (oxcountry)
     */
    public function testResetLanguage_skipTables()
    {
        $oDbMeta = $this->getMock(\OxidEsales\Eshop\Core\DbMetaDataHandler::class, ['getAllTables', 'resetMultilangFields']);
        $oDbMeta->expects($this->once())->method('getAllTables')->willReturn(["oxcountry", "testTable2"]);
        $oDbMeta->expects($this->once())->method('resetMultilangFields')->with(1, "testTable2");

        $oDbMeta->resetLanguage(1, "testDbMetaDataHandler");
    }

    /**
     * Check whether merging inside getSingleLangFields returns proper values
     *
     * @bug https://bugs.oxid-esales.com/view.php?id=5990
     */
    public function testGetSingleLangFieldsWith9thLanguage()
    {
        /** @var oxDbMetaDataHandler $oHandler */
        $oHandler = $this->getMock(\OxidEsales\Eshop\Core\DbMetaDataHandler::class, ['getFields']);
        $oHandler
            ->method('getFields')
            ->willReturnOnConsecutiveCalls(
                [
                    'OXID' => 'oxarticles.OXID',
                    'OXVARNAME_1' => 'oxarticles.OXVARNAME_1',
                    'OXVARSELECT_1' => 'oxarticles.OXVARSELECT_1'
                ],
                [
                    'OXID' => 'oxarticles_set1.OXID',
                    'OXVARNAME_8' => 'oxarticles_set1.OXVARNAME_8',
                    'OXVARSELECT_8' => 'oxarticles_set1.OXVARSELECT_8'
                ]
            );

        $aExpectedResult = ['OXID' => 'oxarticles.OXID', 'OXVARNAME' => 'oxarticles_set1.OXVARNAME_8', 'OXVARSELECT' => 'oxarticles_set1.OXVARSELECT_8'];

        $this->assertSame($aExpectedResult, $oHandler->getSinglelangFields('oxarticles', 8));
    }
}
