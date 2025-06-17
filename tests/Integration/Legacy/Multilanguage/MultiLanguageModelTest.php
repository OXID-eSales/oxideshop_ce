<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Multilanguage;

use OxidEsales\Eshop\Core\Model\MultiLanguageModel;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;

final class MultiLanguageModelTest extends MultilanguageTestCase
{
    use ContainerTrait;

    private string $testTableName = 'test';
    private string $testRecordId = 'test_multilang_lowercase_fields';

    public function setUp(): void
    {
        parent::setUp();
        $this->addTableToMultilanguageConfiguration();
    }

    public function tearDown(): void
    {
        $this->removeTableFromMultilanguageConfiguration();
        parent::tearDown();
    }

    public function testGetAvailableInLangsReturnsLanguagesWithData(): void
    {
        $this->removeTestTable();
        $this->createTestTableWithUppercaseFields();
        $this->updateViews();

        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();
        $queryBuilder
            ->insert($this->testTableName)
            ->values([
                'OXID' => ':oxid',
                'TEST_FIELD' => ':testField',
                'TEST_FIELD_1' => ':testField1',
                'TEST_FIELD_2' => ':testField2',
            ])
            ->setParameters([
                'oxid' => $this->testRecordId,
                'testField' => 'test',
                'testField1' => 'test_en',
                'testField2' => '',
            ])
            ->execute();

        $multiLanguageModel = oxNew(MultiLanguageModel::class);
        $multiLanguageModel->init($this->testTableName);
        $multiLanguageModel->load($this->testRecordId);
        $availableLanguages = $multiLanguageModel->getAvailableInLangs();

        $this->assertCount(2, $availableLanguages);
    }

    public function testGetAvailableInLangsWorksWithLowercaseFieldNames(): void
    {
        $this->removeTestTable();
        $this->createTestTableWithLowercaseFields();
        $this->updateViews();

        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();
        $queryBuilder
            ->insert($this->testTableName)
            ->values([
                'oxid' => ':oxid',
                'test_field' => ':testField',
                'test_field_1' => ':testField1',
            ])
            ->setParameters([
                'oxid' => $this->testRecordId,
                'testField' => 'test',
                'testField1' => 'test_en',
            ])
            ->execute();

        $multiLanguageModel = oxNew(MultiLanguageModel::class);
        $multiLanguageModel->init($this->testTableName);
        $multiLanguageModel->load($this->testRecordId);
        $availableLanguages = $multiLanguageModel->getAvailableInLangs();

        $this->assertCount(2, $availableLanguages);
    }

    public function testGetAvailableInLangsIgnoresEmptyValues(): void
    {
        $this->removeTestTable();
        $this->createTestTableWithUppercaseFields();
        $this->updateViews();

        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();
        $queryBuilder
            ->insert($this->testTableName)
            ->values([
                'OXID' => ':oxid',
                'TEST_FIELD' => ':testField',
                'TEST_FIELD_1' => ':testField1',
            ])
            ->setParameters([
                'oxid' => $this->testRecordId,
                'testField' => '',
                'testField1' => null,
            ])
            ->execute();

        $multiLanguageModel = oxNew(MultiLanguageModel::class);
        $multiLanguageModel->init($this->testTableName);
        $multiLanguageModel->load($this->testRecordId);
        $availableLanguages = $multiLanguageModel->getAvailableInLangs();

        $this->assertEmpty($availableLanguages);
    }

    private function createTestTableWithUppercaseFields(): void
    {
        $connection = $this->get(ConnectionFactoryInterface::class)->create();

        $createTableQuery = "CREATE TABLE IF NOT EXISTS " . $this->testTableName . " (
            OXID char(32) NOT NULL,
            TEST_FIELD varchar(10),
            TEST_FIELD_1 varchar(10),
            TEST_FIELD_2 varchar(10),
            PRIMARY KEY (OXID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

        $connection->executeStatement($createTableQuery);
    }

    private function createTestTableWithLowercaseFields(): void
    {
        $connection = $this->get(ConnectionFactoryInterface::class)->create();

        $createTableQuery = "CREATE TABLE IF NOT EXISTS " . $this->testTableName . " (
            oxid char(32) NOT NULL,
            test_field varchar(10),
            test_field_1 varchar(10),
            test_field_2 varchar(10),
            PRIMARY KEY (oxid)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

        $connection->executeStatement($createTableQuery);
    }

    private function removeTestTable(): void
    {
        $connection = $this->get(ConnectionFactoryInterface::class)->create();
        $connection->executeStatement("DROP TABLE IF EXISTS " . $this->testTableName);
    }

    private function addTableToMultilanguageConfiguration(): void
    {
        $shopConfiguration = Registry::getConfig();
        $multiLanguageTables = $shopConfiguration->getConfigParam('aMultiLangTables') ?: [];
        $multiLanguageTables[] = $this->testTableName;
        $shopConfiguration->setConfigParam('aMultiLangTables', $multiLanguageTables);
    }

    private function removeTableFromMultilanguageConfiguration(): void
    {
        $shopConfiguration = Registry::getConfig();
        $multiLanguageTables = $shopConfiguration->getConfigParam('aMultiLangTables') ?: [];
        $multiLanguageTables = array_diff($multiLanguageTables, [$this->testTableName]);
        $shopConfiguration->setConfigParam('aMultiLangTables', $multiLanguageTables);
    }
}
