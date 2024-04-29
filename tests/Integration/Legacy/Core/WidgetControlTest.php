<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Exception\ObjectException;
use OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver;
use OxidEsales\Eshop\Core\WidgetControl;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\TestingLibrary\UnitTestCase;

class WidgetControlTest extends IntegrationTestCase
{
    /**
     * Test checks if exception was thrown. Need to catch this exception so nothing would be logged in exception log file.
     */
    public function testIfDoesNotAllowToInitiateNonWidgetClass()
    {
        \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\ConfigFile::class)->setVar('iDebug', 1);
        $_SERVER["REQUEST_METHOD"] = 'POST';

        $this->expectException(\OxidEsales\Eshop\Core\Exception\ObjectException::class);

        /** @var WidgetControl $widgetControll */
        $widgetControll = oxNew(WidgetControl::class);
        $nonWidgetClass = (new ControllerClassNameResolver())->getIdByClassName(\OxidEsales\Eshop\Application\Controller\SearchController::class);
        $widgetControll->start($nonWidgetClass);
    }
}
