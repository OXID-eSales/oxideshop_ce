<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\DIContainer\CompilerPass\Fixtures;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OptionalPathParameterController
{
    #[Route('/{page}', defaults: ['page' => 'home'])]
    public function page(string $page): Response
    {
        return new Response($page);
    }
}
