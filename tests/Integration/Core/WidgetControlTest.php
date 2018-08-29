<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Exception\ObjectException;
use OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver;
use OxidEsales\Eshop\Core\WidgetControl;
use OxidEsales\TestingLibrary\UnitTestCase;

class WidgetControlTest extends UnitTestCase
{
    /**
     * Test checks if exception was thrown. Need to catch this exception so nothing would be logged in exception log file.
     */
    public function testIfDoesNotAllowToInitiateNonWidgetClass()
    {
        $originalDebugMode = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\ConfigFile::class)->getVar('iDebug');
        /** Set iDebug to 1, so the exception will be rethrown */
        \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\ConfigFile::class)->setVar('iDebug', 1);

        $wasExceptionThrown = false;
        try {
            /** @var WidgetControl $widgetControll */
            $widgetControll = oxNew(WidgetControl::class);
            $nonWidgetClass = (new ControllerClassNameResolver())->getIdByClassName(\OxidEsales\Eshop\Application\Controller\SearchController::class);
            $widgetControll->start($nonWidgetClass);
        } catch (ObjectException $exception) {
            $wasExceptionThrown = true;
        } finally {
            \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\ConfigFile::class)->setVar('iDebug', $originalDebugMode);
        }

        $this->assertLoggedException(\OxidEsales\Eshop\Core\Exception\ObjectException::class);
        $this->assertTrue($wasExceptionThrown, 'It was expected, that widget controll will not accept any other class, than widget.');
    }
}
