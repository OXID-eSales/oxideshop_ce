<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http\ErrorRenderer;

use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class ProblemJsonErrorRenderer implements ErrorRendererInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private ErrorRendererInterface $htmlErrorRenderer,
        private bool $debug,
    ) {
    }

    public function render(\Throwable $exception): FlattenException
    {
        if ($this->requestStack->getCurrentRequest()?->getPreferredFormat() !== 'json') {
            return $this->htmlErrorRenderer->render($exception);
        }

        $flattenException = FlattenException::createFromThrowable($exception);

        return $flattenException
            ->setHeaders($flattenException->getHeaders() + ['Content-Type' => 'application/json'])
            ->setAsString((string) json_encode(
                $this->getProblem($flattenException),
                JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR
            ));
    }

    private function getProblem(FlattenException $exception): array
    {
        $problem = [
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10',
            'title' => 'An error occurred',
            'status' => $exception->getStatusCode(),
            'detail' => $exception->getStatusText(),
        ];

        if (!$this->debug) {
            return $problem;
        }

        return array_merge($problem, [
            'detail' => $exception->getMessage(),
            'class' => $exception->getClass(),
            'trace' => $exception->getTrace(),
        ]);
    }
}
