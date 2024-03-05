<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database\Logger;

use Doctrine\DBAL\Logging\SQLLogger;
use Exception;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\Exception\AdminUserNotFoundException;
use Psr\Log\LoggerInterface;

class QueryLogger implements SQLLogger
{
    public function __construct(
        private readonly QueryFilterInterface $queryFilter,
        private readonly ContextInterface $context,
        private readonly LoggerInterface $psrLogger
    ) {
    }

    public function startQuery($query, ?array $params = null, ?array $types = null): void
    {
        if ($this->filterPass($query)) {
            $queryData = $this->getQueryData($query, $params);
            $this->psrLogger->debug($this->getLogMessage($queryData));
        }
    }

    private function filterPass(string $query): bool
    {
        return $this->queryFilter->shouldLogQuery($query, $this->context->getSkipLogTags());
    }

    /**
     * Collect query information.
     *
     * @param string $query The query to be executed.
     * @param array|null $params The query parameters.
     *
     * @return array
     */
    private function getQueryData($query, array $params = null): array
    {
        $backTraceInfo = $this->getQueryTrace();

        return [
            'adminUserId' => $this->getAdminUserIdIfExists(),
            'shopId' => $this->context->getCurrentShopId(),
            'class' => $backTraceInfo['class'] ?? '',
            'function' => $backTraceInfo['function'] ?? '',
            'file' => $backTraceInfo['file'] ?? '',
            'line' => $backTraceInfo['line'] ?? '',
            'query' => $query,
            'params' => serialize($params),
        ];
    }

    /**
     * Get first entry from backtrace that is not connected to database.
     * This has to be the origin of the query.
     */
    private function getQueryTrace(): array
    {
        $queryTraceItem = [];
        foreach ((new Exception())->getTrace() as $item) {
            if (
                stripos($item['class'], $this::class) === false &&
                stripos($item['class'], 'Doctrine') === false
            ) {
                $queryTraceItem = $item;
                break;
            }
        }

        return $queryTraceItem;
    }

    private function getAdminUserIdIfExists(): string
    {
        try {
            $adminId = $this->context->getAdminUserId();
        } catch (AdminUserNotFoundException) {
            $adminId = '';
        }

        return $adminId;
    }

    private function getLogMessage(array $queryData): string
    {
        $message = '';
        foreach ($queryData as $key => $value) {
            $message .= PHP_EOL . $key . ': ' . $value;
        }

        return $message . PHP_EOL;
    }

    public function stopQuery(): void
    {
    }
}
