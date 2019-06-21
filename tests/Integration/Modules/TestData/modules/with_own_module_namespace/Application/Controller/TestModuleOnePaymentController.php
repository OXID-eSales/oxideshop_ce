<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Controller;

use oxRegistry;

class TestModuleOnePaymentController extends TestModuleOnePaymentController_parent
{
    public function render()
    {
        $template = parent::render();

        $model = oxNew(\OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_own_module_namespace\Application\Model\TestModuleOneModel::class);
        $message = $model->getInfo();

        oxRegistry::getSession()->setVariable('payerror', '-1');
        oxRegistry::getSession()->setVariable('payerrortext', 'Test module prevents payment! ' . microtime(true) . $message);

        return $template;
    }
}
