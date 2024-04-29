<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\without_own_module_namespace\Application\Controller;

use OxidEsales\Eshop\Core\Registry;


class TestModuleTwoPaymentController extends TestModuleTwoPaymentController_parent
{
    public function render()
    {
        $template = parent::render();

        $model = oxNew('TestModuleTwoModel');
        $message = $model->getInfo();

        Registry::getSession()->setVariable('payerror', '-1');
        Registry::getSession()->setVariable('payerrortext', 'Test module prevents payment! ' . microtime(true) . $message);

        return $template;
    }
}
