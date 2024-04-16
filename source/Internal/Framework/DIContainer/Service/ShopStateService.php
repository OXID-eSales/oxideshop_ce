<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service;

use Doctrine\DBAL\Exception\TableNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\BootstrapConnectionFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;

/**
 * @internal
 */
class ShopStateService implements ShopStateServiceInterface
{
    public function __construct(
        private readonly BasicContextInterface $basicContext,
        private readonly string $anyUnifiedNamespace
    ) {
    }

    public function isLaunched(): bool
    {
        return $this->areUnifiedNamespacesGenerated()
            && $this->doesConfigTableExist();
    }

    /**
     * @return bool
     */
    private function areUnifiedNamespacesGenerated(): bool
    {
        return class_exists($this->anyUnifiedNamespace);
    }

    private function doesConfigTableExist(): bool
    {
        try {
            (new BootstrapConnectionFactory())
                ->create()
                ->exec(
                    sprintf("SELECT 1 FROM `%s` LIMIT 1", $this->basicContext->getConfigTableName())
                );
        } catch (TableNotFoundException) {
            return false;
        }

        return true;
    }
}
