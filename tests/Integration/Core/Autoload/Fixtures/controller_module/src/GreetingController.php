<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\Fixtures\controller_module\src;

use OxidEsales\Eshop\Application\Controller\StartController;

class GreetingController extends StartController
{
    public function getGreeting(): string
    {
        return 'greeting from controller_module';
    }
}
