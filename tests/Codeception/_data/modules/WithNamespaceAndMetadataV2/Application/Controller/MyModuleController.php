<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\Vendor1\WithNamespaceAndMetadataV2\Application\Controller;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Core\Registry;

final class MyModuleController extends FrontendController
{
    protected $_sThisTemplate = 'vendor1_controller_routing.tpl';

    private string $message = '';

    public function render()
    {
        return parent::render();
    }

    public function displayMessage(): void
    {
        $this->_aViewData['the_module_message'] = $this->getMessage();
        $this->render();
    }

    public function getMessage(): string
    {
        $this->message = Registry::getRequest()
                ->getRequestEscapedParameter('mymodule_message')
            . ' '
            . Registry::getConfig()->getShopId();

        return $this->message;
    }
}
