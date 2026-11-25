<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Core\Exception\RoutingException;
use Symfony\Component\HttpFoundation\RequestStack;

class NotFoundController extends FrontendController
{
    protected $_sThisTemplate = 'message/err_404';

    protected $_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;

    public function __construct(private readonly RequestStack $requestStack)
    {
        parent::__construct();
    }

    public function init(): void
    {
        if ($this->isRequestedDirectly()) {
            throw new RoutingException('The not found page is only rendered for an error');
        }

        parent::init();
    }

    public function render()
    {
        $this->addTplParam('sUrl', $this->requestStack->getCurrentRequest()->getRequestUri());

        return parent::render();
    }

    protected function canRedirect(): bool
    {
        return false;
    }

    private function isRequestedDirectly(): bool
    {
        return $this->requestStack->getParentRequest() === null;
    }
}
