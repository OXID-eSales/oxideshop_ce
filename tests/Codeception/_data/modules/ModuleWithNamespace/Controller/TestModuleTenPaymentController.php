<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\ModuleWithNamespace\Application\Controller;

use OxidEsales\Eshop\Application\Controller\FrontendController;

final class TestModuleTenPaymentController extends FrontendController
{
    /**
     * @return mixed The template name.
     */
    public function render()
    {
        $template = parent::render();

        $model = oxNew('TestModuleTenModel');
        $message = $model->getInfo();

        oxRegistry::getSession()
            ->setVariable('payerror', '-1');
        oxRegistry::getSession()
            ->setVariable('payerrortext', 'Test module prevents payment! ' . microtime(true) . $message);

        return $template;
    }
}
