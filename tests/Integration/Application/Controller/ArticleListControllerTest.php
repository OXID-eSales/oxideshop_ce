<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller;

use OxidEsales\Eshop\Application\Controller\ArticleListController;
use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ArticleListControllerTest extends IntegrationTestCase
{
    public function tearDown(): void
    {
        unset($_GET['cnid'], $_GET['pgNr']);

        parent::tearDown();
    }

    public function testRenderRejectsFurtherPageOfCategoryWithoutProducts(): void
    {
        $category = oxNew(Category::class);
        $category->setId((string) Id::generate());
        $category->assign(['oxtitle' => 'Empty category', 'oxactive' => 1, 'oxparentid' => 'oxrootid']);
        $category->save();
        $_GET['cnid'] = $category->getId();
        $_GET['pgNr'] = 1;

        $this->expectException(RoutingException::class);

        oxNew(ArticleListController::class)->render();
    }
}
