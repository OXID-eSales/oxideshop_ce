<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller;

use OxidEsales\Eshop\Application\Controller\NotFoundController;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Request;

final class NotFoundControllerTest extends IntegrationTestCase
{
    public function testRendersTheNotFoundTemplateForTheRequestedUri(): void
    {
        ContainerFacade::get(Request::class)->server->set('REQUEST_URI', '/retired-url/?a=1');
        $controller = oxNew(NotFoundController::class);

        $template = $controller->render();

        $this->assertSame('message/err_404', $template);
        $this->assertSame('/retired-url/?a=1', $controller->getViewData()['sUrl']);
    }

    public function testIsExcludedFromIndexing(): void
    {
        $controller = oxNew(NotFoundController::class);

        $this->assertSame(VIEW_INDEXSTATE_NOINDEXNOFOLLOW, $controller->noIndex());
    }
}
