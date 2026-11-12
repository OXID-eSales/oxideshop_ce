<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Http\Fixtures;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class RoutedController
{
    #[Route('/kernel-routing/{id}')]
    public function show(string $id): Response
    {
        return new Response('routed-body id=' . $id);
    }
}
