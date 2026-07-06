<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Response;

class OfflinePageResponse extends Response
{
    public function __construct(int $status = self::HTTP_NOT_FOUND)
    {
        parent::__construct($this->offlinePageContent(), $status);
    }

    private function offlinePageContent(): string
    {
        $page = Path::join(new BasicContext()->getSourcePath(), 'offline.html');

        return is_readable($page) ? (string) file_get_contents($page) : '';
    }
}
