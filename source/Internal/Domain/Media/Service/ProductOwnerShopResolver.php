<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

readonly class ProductOwnerShopResolver implements ProductOwnerShopResolverInterface
{
    public function __construct(
        private QueryBuilderFactoryInterface $queryBuilderFactory,
        private ContextInterface $context,
    ) {
    }

    public function resolveShopChain(Id $productId): array
    {
        $currentShop = $this->context->getCurrentShopId();

        $ownerShop = $this->queryBuilderFactory
            ->create()
            ->select('oxshopid')
            ->from('oxarticles')
            ->where('oxid = :oxid')
            ->setParameter('oxid', (string) $productId)
            ->executeQuery()
            ->fetchOne();

        if ($ownerShop === false || (int) $ownerShop === $currentShop) {
            return [$currentShop];
        }

        return [$currentShop, (int) $ownerShop];
    }
}
