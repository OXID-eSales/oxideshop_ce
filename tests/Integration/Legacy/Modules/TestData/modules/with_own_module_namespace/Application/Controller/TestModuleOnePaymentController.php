<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\with_own_module_namespace\Application\Controller;

use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\with_own_module_namespace\Application\Model\TestModuleOneModel;
use oxRegistry;

class TestModuleOnePaymentController extends TestModuleOnePaymentController_parent
{
    public function render()
    {
        $template = parent::render();

        $model = oxNew(TestModuleOneModel::class);
        $message = $model->getInfo();

        oxRegistry::getSession()->setVariable('payerror', '-1');
        oxRegistry::getSession()->setVariable(
            'payerrortext',
            'Test module prevents payment! ' . microtime(true) . $message
        );

        return $template;
    }
}
