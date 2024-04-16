<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\GenericImport\ImportObject;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use OxidEsales\Eshop\Application\Model\Article as Product;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Model\MultiLanguageModel;
use OxidEsales\EshopCommunity\Core\GenericImport\ImportObject\Accessories2Article;
use OxidEsales\EshopCommunity\Core\GenericImport\ImportObject\Article;
use OxidEsales\EshopCommunity\Core\GenericImport\ImportObject\ArticleExtends;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

use function array_map;
use function sort;

final class ImportObjectTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Registry::getConfig()->setAdminMode(true);
    }

    public function tearDown(): void
    {
        parent::setUp();

        Registry::getConfig()->setAdminMode(false);
    }

    #[RunInSeparateProcess]
    public function testGetFields(): void
    {
        $model = oxNew(Product::class);
        $model->setEnableMultilang(false);
        $model->setLanguage(0);
        $modelFields = $model->getFieldNames();

        $importFields = oxNew(Article::class)->getFieldList();

        $this->assertSame(
            $this->sortFields($modelFields),
            $this->sortFields($importFields)
        );
    }

    #[RunInSeparateProcess]
    public function testGetFieldsWithImportObjectAndI18nAsShopObjectName(): void
    {
        $model = oxNew(MultiLanguageModel::class);
        $model->init('oxartextends');
        $model->setEnableMultilang(false);
        $model->setLanguage(0);
        $modelFields = $model->getFieldNames();

        $importFields = oxNew(ArticleExtends::class)->getFieldList();

        $this->assertSame(
            $this->sortFields($modelFields),
            $this->sortFields($importFields)
        );
    }

    #[RunInSeparateProcess]
    public function testGetFieldsWithImportObjectAndEmptyShopObjectName(): void
    {
        $model = oxNew(BaseModel::class);
        $model->init('oxaccessoire2article');
        $modelFields = $model->getFieldNames();

        $importFields = oxNew(Accessories2Article::class)->getFieldList();

        $this->assertSame(
            $this->sortFields($modelFields),
            $this->sortFields($importFields)
        );
    }

    private function sortFields(array $fields): array
    {
        sort($fields);

        return array_map('strtolower', $fields);
    }
}
