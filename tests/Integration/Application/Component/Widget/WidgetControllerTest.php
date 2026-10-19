<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Component\Widget;

use OxidEsales\Eshop\Application\Component\Widget\WidgetController;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
final class WidgetControllerTest extends IntegrationTestCase
{
    public function testRenderReturnsEmptyWhenCalledWithoutParentView(): void
    {
        $widget = oxNew(WidgetController::class);
        Registry::getConfig()->setActiveView($widget);

        $this->assertSame('', $widget->render());
    }
}
