<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Component\Widget;

use OxidEsales\Eshop\Application\Component\Widget\ArticleBox;
use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
final class ArticleBoxTest extends IntegrationTestCase
{
    public function testGetLinkWithoutParentViewReturnsOwnLink(): void
    {
        $articleBox = $this->createArticleBox();
        Registry::getConfig()->setActiveView($articleBox);

        $this->assertStringContainsString('cl=oxwarticlebox', $articleBox->getLink());
    }

    public function testGetActiveCategoryWithoutParentViewReturnsOwnCategory(): void
    {
        $category = oxNew(Category::class);
        $articleBox = $this->createArticleBox();
        $articleBox->setActiveCategory($category);
        Registry::getConfig()->setActiveView($articleBox);

        $this->assertSame($category, $articleBox->getActiveCategory());
    }

    private function createArticleBox(): ArticleBox
    {
        $articleBox = oxNew(ArticleBox::class);
        $articleBox->setClassKey('oxwarticlebox');

        return $articleBox;
    }
}
