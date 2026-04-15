<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http\Exception;

use Symfony\Component\HttpFoundation\Response;

class ResponseException extends \RuntimeException
{
    public function __construct(private readonly Response $response)
    {
        parent::__construct('Response exception');
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
