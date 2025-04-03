<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Controller\Fixtures\module1\src\Controller;

use OxidEsales\EshopCommunity\Internal\Framework\Controller\AbstractControllerDecorator;
use OxidEsales\EshopCommunity\Internal\Framework\Controller\ViewControllerInterface;

class ModuleControllerDecorator extends AbstractControllerDecorator
{
    public function __construct(protected readonly ViewControllerInterface $controller)
    {

    }

    public function init()
    {
        echo "Init Decorator";
        $this->controller->init();
    }
}
