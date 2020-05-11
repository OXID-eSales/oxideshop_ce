<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Admin\Factory;

use OxidEsales\EshopCommunity\Internal\Domain\Admin\DataObject\Admin;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Exception\InvalidEmailException;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Exception\InvalidRightsException;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Exception\InvalidShopException;

interface AdminFactoryInterface
{
    /**
     * @throws InvalidEmailException
     * @throws InvalidShopException
     * @throws InvalidRightsException
     */
    public function createAdmin(
        string $email,
        string $password,
        string $rights,
        int $shopId
    ): Admin;
}
