<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Core\Fixtures;

use OxidEsales\Eshop\Application\Component\Widget\WidgetController;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;

class RedirectingWidget extends WidgetController
{
    public function render()
    {
        throw new RedirectException('https://shop.example/target');
    }
}
