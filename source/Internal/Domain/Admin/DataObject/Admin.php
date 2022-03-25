<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Admin\DataObject;

class Admin
{
    public const MALL_ADMIN = 'malladmin';

    public function __construct(
        /**
         * @var string
         */
        private $id,
        /**
         * @var string
         */
        private $email,
        /**
         * @var string
         */
        private $passwordHash,
        /**
         * @var string
         */
        private $rights,
        /**
         * @var int
         */
        private $shopId
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getRights(): string
    {
        return $this->rights;
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }
}
