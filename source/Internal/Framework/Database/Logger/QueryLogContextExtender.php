<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database\Logger;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\Exception\AdminUserNotFoundException;

readonly class QueryLogContextExtender implements QueryLogContextExtenderInterface
{
    public function __construct(private ContextInterface $shopContext)
    {
    }

    public function extend(array $queryContext): array
    {
        $extendedContext = [
            'adminUserId' => $this->getAdminUserIdIfExists(),
            'shopId' => $this->shopContext->getCurrentShopId(),
            'trace' => debug_backtrace(),
        ];

        return array_merge($queryContext, $extendedContext);
    }

    private function getAdminUserIdIfExists(): string
    {
        try {
            $adminId = $this->shopContext->getAdminUserId();
        } catch (AdminUserNotFoundException) {
            $adminId = '';
        }

        return $adminId;
    }
}
