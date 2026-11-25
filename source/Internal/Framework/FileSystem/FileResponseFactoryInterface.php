<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem;

use Symfony\Component\HttpFoundation\Response;

interface FileResponseFactoryInterface
{
    public function createFromFile(string $path, string $contentType, string $downloadFilename): Response;

    public function createFromContent(string $content, string $contentType, string $downloadFilename): Response;

    public function createStreamed(callable $callback, string $contentType, string $downloadFilename): Response;

    public function createNotFound(): Response;
}
