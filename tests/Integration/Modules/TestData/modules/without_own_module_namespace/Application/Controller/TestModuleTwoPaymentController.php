<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

class TestModuleTwoPaymentController extends TestModuleTwoPaymentController_parent
{
    public function render()
    {
        $template = parent::render();

        $model = oxNew('TestModuleTwoModel');
        $message = $model->getInfo();

        oxRegistry::getSession()->setVariable('payerror', '-1');
        oxRegistry::getSession()->setVariable('payerrortext', 'Test module prevents payment! ' . microtime(true) . $message);

        return $template;
    }
}
