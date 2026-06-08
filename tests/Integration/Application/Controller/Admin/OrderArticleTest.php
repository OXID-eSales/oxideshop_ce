<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\OrderArticle;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsObject;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class OrderArticleTest extends IntegrationTestCase
{
    private string $testProductNumber = 'product123';

    public function setUp(): void
    {
        parent::setUp();

        Registry::getConfig()->setAdminMode(true);
    }

    public function tearDown(): void
    {
        Registry::getConfig()->setAdminMode(false);
        UtilsObject::getInstance()->resetInstanceCache(Article::class);

        parent::tearDown();
    }

    public function testGetMainProductReturnsParentForVariant(): void
    {
        $parentId = $this->createTestArticle('parentProduct');
        $this->createTestArticle($this->testProductNumber, $parentId);

        $this->setRequestParameter('sSearchArtNum', $this->testProductNumber);

        $orderArticle = new OrderArticle();
        $result = $orderArticle->getMainProduct();

        $this->assertEquals($parentId, $result->getId());
    }

    public function testGetMainProductFindsStandaloneArticle(): void
    {
        $articleId = $this->createTestArticle($this->testProductNumber);

        $this->setRequestParameter('sSearchArtNum', $this->testProductNumber);

        $orderArticle = new OrderArticle();
        $result = $orderArticle->getMainProduct();

        $this->assertEquals($articleId, $result->getId());
    }

    public function testGetMainProductReturnsFalseForUnknownArticle(): void
    {
        $this->setRequestParameter('sSearchArtNum', 'nonExistentProductNumber');

        $orderArticle = new OrderArticle();
        $result = $orderArticle->getMainProduct();

        $this->assertFalse($result);
    }

    private function createTestArticle(string $artNum, string $parentId = ''): string
    {
        $article = oxNew(Article::class);
        $article->oxarticles__oxartnum = new Field($artNum, Field::T_RAW);
        $article->oxarticles__oxparentid = new Field($parentId, Field::T_RAW);
        $article->save();

        return $article->getId();
    }

    private function setRequestParameter(string $key, string $value): void
    {
        $_POST[$key] = $value;
    }
}
