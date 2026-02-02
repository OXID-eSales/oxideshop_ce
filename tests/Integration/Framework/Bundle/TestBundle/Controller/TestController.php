<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Framework\Bundle\TestBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController
{
    #[Route('/test-bundle/hello', name: 'test_bundle_hello', methods: ['GET'])]
    public function hello(): Response
    {
        return new Response('Hello from TestBundle');
    }
}
