<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use OxidEsales\EshopCommunity\Internal\Framework\Controller\ViewControllerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeResponseSendEvent extends Event
{
    public function __construct(
        private Response $response,
        private ViewControllerInterface $controller
    ) {
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getController(): ViewControllerInterface
    {
        return $this->controller;
    }
}
