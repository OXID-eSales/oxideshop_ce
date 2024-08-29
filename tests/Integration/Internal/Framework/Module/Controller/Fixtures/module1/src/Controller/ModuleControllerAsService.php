<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Controller\Fixtures\module1\src\Controller;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\EshopCommunity\Core\Controller\BaseController;
use OxidEsales\EshopCommunity\Internal\Framework\Controller\ControllerInterface;

class ModuleControllerAsService extends BaseController implements ControllerInterface
{
    protected $_sThisTemplate = '@module1/module_controller_as_service';

    public function testFunction(): void
    {
        echo 'Function output';
    }
}
