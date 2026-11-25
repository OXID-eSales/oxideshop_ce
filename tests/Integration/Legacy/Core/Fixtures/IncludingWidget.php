<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Core\Fixtures;

use OxidEsales\Eshop\Application\Component\Widget\WidgetController;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\WidgetControl;
use Symfony\Component\HttpFoundation\Response;

class IncludingWidget extends WidgetController
{
    public function render()
    {
        Registry::get(WidgetControl::class)->buildIncludedWidgetResponse('failing_widget', [], []);

        return new Response('including widget');
    }
}
