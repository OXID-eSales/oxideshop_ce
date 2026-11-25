<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Response;

readonly class OfflinePageResponseFactory implements OfflinePageResponseFactoryInterface
{
    public function __construct(
        private BasicContextInterface $context,
    ) {
    }

    public function create(): Response
    {
        return new Response($this->getOfflinePage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function getOfflinePage(): string
    {
        $page = Path::join($this->context->getSourcePath(), 'offline.html');

        return is_readable($page) ? (string) file_get_contents($page) : '';
    }
}
