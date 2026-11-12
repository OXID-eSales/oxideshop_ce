<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\RateLimiter\Storefront\Fixtures;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class ApiController
{
    #[Route('/api/products')]
    public function list(): Response
    {
        return new Response('api-body');
    }
}
