<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\WidgetControl;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\IncludeWidgetLogic;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;

final class IncludeWidgetLogicTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function tearDown(): void
    {
        Registry::set(WidgetControl::class, null);

        parent::tearDown();
    }

    public function testRenderWidgetPassesParametersToWidgetControl(): void
    {
        $widgetControl = $this->createMock(WidgetControl::class);
        $widgetControl->expects($this->once())
            ->method('buildIncludedWidgetResponse')
            ->with('oxwminibasket', ['nocookie' => 1], ['start', 'oxubase'])
            ->willReturn(new Response('widget'));
        Registry::set(WidgetControl::class, $widgetControl);

        $output = $this->get(IncludeWidgetLogic::class)->renderWidget(
            ['cl' => 'oxwMiniBasket', 'nocookie' => 1, '_parent' => 'start|oxubase']
        );

        $this->assertSame('widget', $output);
    }
}
