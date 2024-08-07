<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Core;

use OxidEsales\Eshop\Application\Controller\SearchController;
use OxidEsales\Eshop\Core\Exception\ObjectException;
use OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver;
use OxidEsales\Eshop\Core\WidgetControl;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class WidgetControlTest extends IntegrationTestCase
{
    public function testIfDoesNotAllowToInitiateNonWidgetClass(): void
    {
        if (!ContainerFacade::getParameter('oxid_debug_mode')) {
            $this->markTestSkipped('Test works only in debug mode.');
        }

        $_SERVER['REQUEST_METHOD'] = 'POST';

        $this->expectException(ObjectException::class);

        /** @var WidgetControl $widgetControl */
        $widgetControl = oxNew(WidgetControl::class);
        $nonWidgetClass = (new ControllerClassNameResolver())->getIdByClassName(SearchController::class);
        $widgetControl->start($nonWidgetClass);
    }
}
