<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Multilanguage;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\DbMetaDataHandler;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Tests\DatabaseTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('triggers-implicit-transaction-commit')]
final class AdditionalTablesTest extends TestCase
{
    use DatabaseTrait;
    use MultilanguageTrait;

    public function tearDown(): void
    {
        parent::tearDown();

        $this->setupShopDatabase();
    }

    public function testCreateLanguagesAfterAdditionalTable(): void
    {
        $this->createMultilanguageTable();
        $languageId = $this->createLanguages();

        $this->insertTestData($languageId);

        $this->assertEquals('addtest_set1', $this->getTableName());
        $this->assertEquals('latin1_general_ci', $this->getTableCollation());
        $this->assertEquals('latin1', $this->getColumnCharset());
        $this->assertEquals('some additional title', $this->getTitleInLanguage($languageId));
        $this->assertEquals('some default title', $this->getTitleInLanguage(0));
    }

    public function testCreateAdditionalTableAfterCreatingLanguages(): void
    {
        $languageId = $this->createLanguages();
        $this->createMultilanguageTable();
        oxNew(DbMetaDataHandler::class)->updateViews();

        $this->insertTestData($languageId);

        $this->assertEquals('addtest_set1', $this->getTableName());
        $this->assertEquals('latin1_general_ci', $this->getTableCollation());
        $this->assertEquals('latin1', $this->getColumnCharset());
        $this->assertSame('some additional title', $this->getTitleInLanguage($languageId));
        $this->assertSame('some default title', $this->getTitleInLanguage(0));
    }

    private function createMultilanguageTable(): void
    {
        $sql = 'CREATE TABLE `addtest` (' .
            "`OXID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Item id'," .
            "`TITLE` varchar(128) NOT NULL DEFAULT '' COMMENT 'Title (multilanguage)'," .
            "`TITLE_1` varchar(128) NOT NULL DEFAULT ''," .
            "`TITLE_2` varchar(128) NOT NULL DEFAULT ''," .
            "`TITLE_3` varchar(128) NOT NULL DEFAULT ''," .
            "`TITLE_4` varchar(128) NOT NULL DEFAULT ''," .
            "`TITLE_5` varchar(128) NOT NULL DEFAULT ''," .
            "`TITLE_6` varchar(128) NOT NULL DEFAULT ''," .
            "`TITLE_7` varchar(128) NOT NULL DEFAULT ''," .
            'PRIMARY KEY (`OXID`)' .
            ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='for testing'";

        DatabaseProvider::getDb()->execute($sql);
        Registry::getConfig()->setConfigParam('aMultiLangTables', ['addtest']);
    }

    private function insertTestData(int $languageId): void
    {
        DatabaseProvider::getDb()
            ->execute(
                "INSERT INTO addtest (OXID, TITLE) VALUES ('_test101', 'some default title')"
            );
        DatabaseProvider::getDb()
            ->execute(
                "INSERT INTO addtest_set1 (OXID, TITLE_$languageId) VALUES ('_test101', 'some additional title')"
            );
    }

    private function getTableName(): string
    {
        return DatabaseProvider::getDb()
            ->getOne(
                "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_NAME = 'addtest_set1'"
            );
    }

    private function getTableCollation(): string
    {
        return DatabaseProvider::getDb()
            ->getOne(
                "SELECT TABLE_COLLATION  FROM INFORMATION_SCHEMA.TABLES
                                    WHERE TABLE_NAME = 'addtest_set1'"
            );
    }

    private function getColumnCharset(): string
    {
        return DatabaseProvider::getDb()
            ->getOne(
                "SELECT character_set_name FROM INFORMATION_SCHEMA.`COLUMNS` WHERE table_name = 'addtest_set1'
                              AND column_name = 'TITLE_8';"
            );
    }

    private function getTitleInLanguage(int $languageId): string
    {
        return DatabaseProvider::getDb()->getOne(
            sprintf(
                "SELECT TITLE FROM %s WHERE OXID = '_test101'",
                oxNew(TableViewNameGenerator::class)->getViewName('addtest', $languageId)
            )
        );
    }
}
