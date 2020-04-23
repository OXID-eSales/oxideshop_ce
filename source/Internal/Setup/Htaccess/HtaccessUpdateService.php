<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Htaccess;

class HtaccessUpdateService implements HtaccessUpdateServiceInterface
{
    /** @var HtaccessDaoFactoryInterface */
    private $htaccessDaoFactory;

    /** @param HtaccessDaoFactoryInterface $htaccessDaoFactory */
    public function __construct(
        HtaccessDaoFactoryInterface $htaccessDaoFactory
    ) {
        $this->htaccessDaoFactory = $htaccessDaoFactory;
    }

    /** @inheritDoc */
    public function updateRewriteBaseDirective(string $rewriteBase): void
    {
        $this->htaccessDaoFactory->createRootHtaccessDao()->setRewriteBase($rewriteBase);
    }
}
