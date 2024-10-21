<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Htaccess;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;

class HtaccessDaoFactory implements HtaccessDaoFactoryInterface
{
    private const FILENAME = '.htaccess';

    public function __construct(private readonly BasicContextInterface $basicContext)
    {
    }

    /**
     * @throws HtaccessAccessException
     */
    public function createRootHtaccessDao(): HtaccessDaoInterface
    {
        return new HtaccessDao($this->getRootHtaccessPath());
    }

    /**
     * @throws HtaccessAccessException
     */
    private function getRootHtaccessPath(): string
    {
        clearstatcache();
        $path = realpath($this->basicContext->getSourcePath() . DIRECTORY_SEPARATOR . self::FILENAME);
        if (!$path || !is_file($path)) {
            throw new HtaccessAccessException(
                sprintf('Root %s file not found or not accessible', self::FILENAME)
            );
        }

        return $path;
    }
}
