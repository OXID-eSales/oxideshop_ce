<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Controller\Fixtures\module1\src\Controller;

use OxidEsales\EshopCommunity\Core\Controller\BaseController;

class ModuleControllerAsService extends BaseController
{
    protected $_sThisTemplate = '@module1/module_controller_as_service';

    public function testFunction(): void
    {
        $this->addTplParam('_functionOutput', 'Function output');
    }
}
