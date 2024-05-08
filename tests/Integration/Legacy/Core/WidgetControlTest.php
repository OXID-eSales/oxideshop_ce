<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Core;

use OxidEsales\Eshop\Application\Controller\SearchController;
use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Exception\ObjectException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver;
use OxidEsales\Eshop\Core\WidgetControl;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class WidgetControlTest extends IntegrationTestCase
{
    /**
     * Test checks if exception was thrown. Need to catch this exception so nothing would be logged in exception log file.
     */
    public function testIfDoesNotAllowToInitiateNonWidgetClass(): void
    {
        Registry::get(ConfigFile::class)->setVar('iDebug', 1);
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $this->expectException(ObjectException::class);

        /** @var WidgetControl $widgetControll */
        $widgetControll = oxNew(WidgetControl::class);
        $nonWidgetClass = (new ControllerClassNameResolver())->getIdByClassName(SearchController::class);
        $widgetControll->start($nonWidgetClass);
    }
}
