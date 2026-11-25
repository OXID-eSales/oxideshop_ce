<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller;

use OxidEsales\Eshop\Application\Controller\ArticleDetailsController;
use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ArticleDetailsControllerTest extends IntegrationTestCase
{
    public function tearDown(): void
    {
        unset($_GET['anid'], $_SERVER['REQUEST_URI']);

        parent::tearDown();
    }

    public function testGetProductRejectsUnknownProduct(): void
    {
        $_GET['anid'] = 'unknown-product';
        $_SERVER['REQUEST_URI'] = '/unknown-product.html';

        $this->expectException(RoutingException::class);

        oxNew(ArticleDetailsController::class)->getProduct();
    }
}
