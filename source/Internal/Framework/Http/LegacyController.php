<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopControl;
use OxidEsales\EshopCommunity\Internal\Framework\Session\SessionInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{path}', name: 'legacy', requirements: ['path' => '.*'], priority: -1000)]
readonly class LegacyController
{
    public function __construct(
        private ContextInterface $context,
        private SessionInterface $session,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        Registry::getConfig()->init();

        return oxNew(ShopControl::class)->buildResponse(
            $this->resolveControllerKey(),
            Registry::getRequest()->getRequestEscapedParameter('fnc')
        );
    }

    private function resolveControllerKey(): string
    {
        return Registry::getConfig()->getRequestControllerId()
            ?: $this->getDefaultControllerKey();
    }

    private function getDefaultControllerKey(): string
    {
        if ($this->context->isAdmin()) {
            return $this->session->get('auth') ? 'admin_start' : 'login';
        }

        return 'start';
    }
}
