<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\Fixtures\controller_extension_module\src;

class GreetingController extends GreetingController_parent
{
    public function getGreeting(): string
    {
        return 'greeting from controller_extension_module';
    }
}
