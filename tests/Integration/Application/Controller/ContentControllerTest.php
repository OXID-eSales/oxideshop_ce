<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller;

use OxidEsales\Eshop\Application\Controller\ContentController;
use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ContentControllerTest extends IntegrationTestCase
{
    public function tearDown(): void
    {
        unset($_GET['oxcid']);

        parent::tearDown();
    }

    public function testRenderRejectsUnknownContent(): void
    {
        $_GET['oxcid'] = 'unknown-content';

        $this->expectException(RoutingException::class);

        oxNew(ContentController::class)->render();
    }
}
