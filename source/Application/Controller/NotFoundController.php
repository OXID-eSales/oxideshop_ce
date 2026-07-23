<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use Symfony\Component\HttpFoundation\Request;

class NotFoundController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    protected $_sThisTemplate = 'message/err_404';

    protected $_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;

    public function render()
    {
        $this->addTplParam('sUrl', ContainerFacade::get(Request::class)->getRequestUri());

        return parent::render();
    }
}
