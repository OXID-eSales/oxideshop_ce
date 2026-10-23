<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http\ErrorRenderer;

use OxidEsales\Eshop\Core\ShopControl;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\OfflinePageResponse;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;

readonly class ShopHtmlErrorRenderer implements ErrorRendererInterface
{
    public function __construct(
        private ErrorRendererInterface $htmlErrorRenderer,
        private bool $debug,
    ) {
    }

    public function render(\Throwable $exception): FlattenException
    {
        if ($this->debug) {
            return $this->htmlErrorRenderer->render($exception);
        }

        $flattenException = FlattenException::createFromThrowable($exception);

        return match (true) {
            $flattenException->getStatusCode() === Response::HTTP_NOT_FOUND
                => $this->renderNotFoundPage($flattenException),
            $flattenException->getStatusCode() >= Response::HTTP_INTERNAL_SERVER_ERROR
                => $this->renderOfflinePage($flattenException),
            default => $this->htmlErrorRenderer->render($exception),
        };
    }

    private function renderNotFoundPage(FlattenException $exception): FlattenException
    {
        try {
            $response = oxNew(ShopControl::class)->buildResponse('notfound', '');
        } catch (RedirectException $redirect) {
            return $exception
                ->setStatusCode($redirect->getStatusCode())
                ->setHeaders(['Location' => $redirect->getUrl()])
                ->setAsString('');
        }

        return $exception
            ->setHeaders(
                $response->headers->allPreserveCaseWithoutCookies() + $exception->getHeaders() + ['Content-Type' => 'text/html']
            )
            ->setAsString((string) $response->getContent());
    }

    private function renderOfflinePage(FlattenException $exception): FlattenException
    {
        return $exception
            ->setHeaders($exception->getHeaders() + ['Content-Type' => 'text/html'])
            ->setAsString((string) new OfflinePageResponse()->getContent());
    }
}
