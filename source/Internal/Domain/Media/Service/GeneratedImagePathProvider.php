<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Service;

use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Request;

readonly class GeneratedImagePathProvider implements GeneratedImagePathProviderInterface
{
    private const GENERATED_IMAGE_DIRECTORY = 'out/pictures/generated';

    public function __construct(private Request $request)
    {
    }

    public function getGeneratedImagePath(): string
    {
        $requestPath = $this->getCanonicalRequestPath();
        $generatedImagePath = $this->extractGeneratedImagePath($requestPath);

        return $this->isInsideGeneratedImageDirectory($generatedImagePath) ? $generatedImagePath : '';
    }

    private function getCanonicalRequestPath(): string
    {
        return Path::canonicalize(
            rawurldecode($this->request->getPathInfo())
        );
    }

    private function extractGeneratedImagePath(string $requestPath): string
    {
        return ltrim((string) strstr($requestPath, '/' . self::GENERATED_IMAGE_DIRECTORY), '/');
    }

    private function isInsideGeneratedImageDirectory(string $generatedImagePath): bool
    {
        return Path::isBasePath(self::GENERATED_IMAGE_DIRECTORY, $generatedImagePath);
    }
}
