<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller;

use OxidEsales\Eshop\Application\Controller\ArticleListController;
use OxidEsales\Eshop\Application\Model\CategoryList;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;

final class ArticleListControllerTest extends UnitTestCase
{
    private string $smartyUnparsedContent = '[{1|cat:2|cat:3}]';
    private string $smartyParsedContent = '123';
    private ArticleListController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareTestData();
    }

    public function testGetParsedContent(): void
    {
        $parsedContent = $this->controller->_collectMetaDescription('');

        $this->assertSame($this->smartyParsedContent, $parsedContent);
    }

    public function testGetParsedContentWithConfigurationOff(): void
    {
        Registry::getConfig()->setConfigParam('deactivateSmartyForCmsContent', true);

        $parsedContent = $this->controller->_collectMetaDescription('');

        $this->assertSame($this->smartyUnparsedContent, $parsedContent);
    }

    private function prepareTestData(): void
    {
        $categoryList = oxNew(CategoryList::class);

        $testCategoryId = $categoryList->getList()->arrayKeys()[0];
        $category = $categoryList->getList()[$testCategoryId];
        $category->oxcategories__oxlongdesc = new Field($this->smartyUnparsedContent);
        $category->save();

        $_GET['cnid'] = $testCategoryId;
        $this->controller = oxNew(ArticleListController::class);
    }
}
