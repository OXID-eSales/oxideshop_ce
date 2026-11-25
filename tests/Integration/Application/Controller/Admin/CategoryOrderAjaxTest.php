<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\CategoryOrderAjax;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Object2Category;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class CategoryOrderAjaxTest extends IntegrationTestCase
{
    private const CATEGORY_ID = 'category-order-ajax-test';

    public function tearDown(): void
    {
        $_POST = [];
        Registry::getSession()->deleteVariable('neworder_sess');

        parent::tearDown();
    }

    public function testProcessRequestReturnsGridData(): void
    {
        $this->expectOutputString('');
        $_POST = ['cmpid' => 'container1'];

        $gridData = json_decode(oxNew(CategoryOrderAjax::class)->processRequest()->getContent(), true);

        $this->assertSame(0, $gridData['totalRecords']);
    }

    public function testAddLastUnsortedArticleReturnsZero(): void
    {
        $this->givenCategoryWithArticles('article-1');

        $this->assertSame('0', $this->addArticleToNewOrder('article-1'));
        $this->assertSame(['article-1'], Registry::getSession()->getVariable('neworder_sess'));
    }

    public function testAddArticleWithOthersUnsortedReturnsOne(): void
    {
        $this->givenCategoryWithArticles('article-1', 'article-2');

        $this->assertSame('1', $this->addArticleToNewOrder('article-1'));
    }

    public function testRemoveArticleFromNewOrderReturnsOne(): void
    {
        $this->givenCategoryWithArticles('article-1');
        Registry::getSession()->setVariable('neworder_sess', ['article-1']);
        $_POST = ['cmpid' => 'container2', 'oxid' => self::CATEGORY_ID, '_6' => ['article-1']];

        $response = oxNew(CategoryOrderAjax::class)->processRequest('removeCatOrderArticle');

        $this->assertSame('1', $response->getContent());
        $this->assertSame([], Registry::getSession()->getVariable('neworder_sess'));
    }

    private function addArticleToNewOrder(string $articleId): string
    {
        $this->expectOutputString('');
        $_POST = ['cmpid' => 'container1', 'synchoxid' => self::CATEGORY_ID, '_7' => [$articleId]];

        return oxNew(CategoryOrderAjax::class)->processRequest('addCatOrderArticle')->getContent();
    }

    private function givenCategoryWithArticles(string ...$articleIds): void
    {
        foreach ($articleIds as $articleId) {
            $article = oxNew(Article::class);
            $article->setId($articleId);
            $article->oxarticles__oxtitle = new Field($articleId);
            $article->save();

            $assignment = oxNew(Object2Category::class);
            $assignment->setProductId($articleId);
            $assignment->setCategoryId(self::CATEGORY_ID);
            $assignment->save();
        }
    }
}
