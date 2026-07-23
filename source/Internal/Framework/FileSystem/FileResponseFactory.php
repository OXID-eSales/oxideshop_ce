<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

use function Symfony\Component\String\u;

readonly class FileResponseFactory implements FileResponseFactoryInterface
{
    public function fromFile(string $path, string $contentType, string $downloadFilename): Response
    {
        return $this->prepare(new BinaryFileResponse($path), $contentType, $downloadFilename);
    }

    public function fromContent(string $content, string $contentType, string $downloadFilename): Response
    {
        $response = $this->prepare(new Response($content), $contentType, $downloadFilename);
        $response->headers->set('Content-Length', (string) strlen($content));

        return $response;
    }

    public function fromCallback(callable $callback, string $contentType, string $downloadFilename): Response
    {
        return $this->prepare(new StreamedResponse($callback), $contentType, $downloadFilename);
    }

    public function notFound(): Response
    {
        return new Response('The requested file is not available.', Response::HTTP_NOT_FOUND);
    }

    private function prepare(Response $response, string $contentType, string $downloadFilename): Response
    {
        $response->headers->set('Content-Type', $contentType);
        $response->headers->set('Cache-Control', 'no-store, private');

        $filename = $this->withoutDirectoryParts($downloadFilename);
        $response->headers->set(
            'Content-Disposition',
            HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                $filename,
                $this->asciiFallback($filename)
            )
        );

        return $response;
    }

    private function withoutDirectoryParts(string $filename): string
    {
        $filename = strtr($filename, '\\', '/');
        $pos = strrpos($filename, '/');

        return $pos === false ? $filename : substr($filename, $pos + 1);
    }

    private function asciiFallback(string $filename): string
    {
        return (string) preg_replace('/[^\x20-\x7e]|%/', '_', u($filename)->ascii()->toString());
    }
}
