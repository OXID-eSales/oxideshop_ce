<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem;

use Symfony\Component\HttpFoundation\Response;

interface FileResponseFactoryInterface
{
    public function fromFile(string $path, string $contentType, string $downloadFilename): Response;

    public function fromContent(string $content, string $contentType, string $downloadFilename): Response;

    public function fromCallback(callable $callback, string $contentType, string $downloadFilename): Response;

    public function notFound(): Response;
}
