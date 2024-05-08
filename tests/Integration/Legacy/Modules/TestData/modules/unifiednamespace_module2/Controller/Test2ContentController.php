<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\unifiednamespace_module2\Controller;

class Test2ContentController extends Test2ContentController_parent
{
    public function getTitle(): string
    {
        return parent::getTitle() . ' - Module_2_Controller';
    }
}
