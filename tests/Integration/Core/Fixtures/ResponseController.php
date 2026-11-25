<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Fixtures;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Response;

class ResponseController extends FrontendController
{
    public function render()
    {
        return new Response('rendered');
    }

    public function download(): Response
    {
        return new Response('downloaded');
    }
}
