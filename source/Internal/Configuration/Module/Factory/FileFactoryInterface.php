<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\Factory;

/**
 * @internal
 */
interface FileFactoryInterface
{
    /**
     * @return \SplFileObject
     */
    public function create(): \SplFileObject;
}
