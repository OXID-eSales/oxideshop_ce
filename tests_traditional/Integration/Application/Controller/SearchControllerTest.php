<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller;

use OxidEsales\EshopCommunity\Application\Controller\SearchController;
use OxidEsales\EshopCommunity\Application\Model\Article;
use OxidEsales\EshopCommunity\Core\Field;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;

final class SearchControllerTest extends UnitTestCase
{
    private $productTitle1 = '1000';
    private $productid1 = 'seacharticle1000';
    private $productTitle2 = '1001';
    private $productid2 = 'seacharticle1001';

    protected function setUp(): void
    {
        parent::setUp();

        $product1 = oxNew(Article::class);
        $product1->setId($this->productid1);
        $product1->oxarticles__oxtitle = new Field($this->productTitle1);
        $product1->oxarticles__oxsearchkeys = new Field($this->productTitle1);
        $product1->save();

        $product2 = oxNew(Article::class);
        $product2->setId($this->productid2);
        $product2->oxarticles__oxtitle = new Field($this->productTitle2);
        $product2->oxarticles__oxsearchkeys = new Field($this->productTitle2);
        $product2->save();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $delete = oxNew(Article::class);
        $delete->delete($this->productid1);
        $delete->delete($this->productid2);
    }

    public function testSearchAnd(): void
    {
        Registry::getConfig()->setConfigParam('blSearchUseAND', true);

        $this->setRequestParameter('searchparam', $this->productTitle1 . ' ' . $this->productTitle2);

        $searchController = oxNew(SearchController::class);
        $searchController->init();

        $this->assertEquals(0, ($searchController->getArticleList())->count());

        $this->setRequestParameter('searchparam', $this->productTitle1);
        $searchController->init();

        $articleList = $searchController->getArticleList();

        $this->assertEquals(1, ($searchController->getArticleList())->count());
        $this->assertEquals($this->productid1, $articleList->current()->getId());
    }

    public function testSearchOr(): void
    {
        Registry::getConfig()->setConfigParam('blSearchUseAND', false);

        $this->setRequestParameter('searchparam', $this->productTitle1 . ' ' . $this->productTitle2);

        $searchController = oxNew(SearchController::class);
        $searchController->init();

        $articleList = $searchController->getArticleList();
        $this->assertEquals(2, $articleList->count());

        $articleArray = $articleList->getArray();

        $this->assertTrue(
            array_key_exists(
                $this->productid1,
                $articleArray
            )
        );
        $this->assertTrue(
            array_key_exists(
                $this->productid2,
                $articleArray
            )
        );
    }
}
