<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http;

use Symfony\Component\HttpFoundation\Response;

/**
 * @internal thrown only by ResponseReadyListener; dispatch ResponseReadyEvent instead
 */
class ResponseReady extends \Error
{
    public function __construct(private readonly Response $response)
    {
        parent::__construct();
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
