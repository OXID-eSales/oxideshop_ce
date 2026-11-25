<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http\ErrorRenderer;

use OxidEsales\Eshop\Core\ShopControl;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\ShopOfflineException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\OfflinePageResponseFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

readonly class ShopHtmlErrorRenderer implements ErrorRendererInterface
{
    public function __construct(
        private ErrorRendererInterface $htmlErrorRenderer,
        private bool $debug,
        private ContextInterface $context,
        private OfflinePageResponseFactoryInterface $offlinePageResponseFactory,
        private LoggerInterface $logger,
    ) {
    }

    public function render(Throwable $exception): FlattenException
    {
        if ($this->debug || $this->context->isAdmin()) {
            return $this->htmlErrorRenderer->render($exception);
        }

        $flattenException = FlattenException::createFromThrowable($exception);

        return match (true) {
            $flattenException->getStatusCode() === Response::HTTP_NOT_FOUND
                => $this->renderNotFoundPage($exception, $flattenException),
            $flattenException->getStatusCode() >= Response::HTTP_INTERNAL_SERVER_ERROR
                => $this->renderOfflinePage($flattenException),
            default => $this->htmlErrorRenderer->render($exception),
        };
    }

    private function renderNotFoundPage(Throwable $exception, FlattenException $flattenException): FlattenException
    {
        try {
            $response = oxNew(ShopControl::class)->buildResponse('notfound', '');
        } catch (RedirectException $redirect) {
            return $flattenException
                ->setStatusCode($redirect->getStatusCode())
                ->setHeaders(['Location' => $redirect->getUrl()])
                ->setAsString('');
        } catch (ShopOfflineException $offline) {
            return $this->renderOfflinePage(FlattenException::createFromThrowable($offline));
        } catch (Throwable $renderingError) {
            $this->logger->error('The not found page cannot be rendered', ['exception' => $renderingError]);

            return $this->htmlErrorRenderer->render($exception);
        }

        return $flattenException
            ->setHeaders(
                $response->headers->allPreserveCaseWithoutCookies()
                + $flattenException->getHeaders()
                + ['Content-Type' => 'text/html']
            )
            ->setAsString((string) $response->getContent());
    }

    private function renderOfflinePage(FlattenException $exception): FlattenException
    {
        return $exception
            ->setHeaders($exception->getHeaders() + ['Content-Type' => 'text/html'])
            ->setAsString((string) $this->offlinePageResponseFactory->create()->getContent());
    }
}
