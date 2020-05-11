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
use OxidEsales\EshopCommunity\Internal\Utility\Email\EmailValidatorServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Utility\Hash\Service\PasswordHashServiceInterface;

class AdminFactory implements AdminFactoryInterface
{
    /**
     * @var ShopAdapterInterface
     */
    private $shopAdapter;

    /**
     * @var EmailValidatorServiceInterface
     */
    private $emailValidatorService;

    /**
     * @var PasswordHashServiceInterface
     */
    private $passwordHashService;

    public function __construct(
        ShopAdapterInterface $shopAdapter,
        EmailValidatorServiceInterface $emailValidatorService,
        PasswordHashServiceInterface $passwordHashService
    ) {
        $this->shopAdapter = $shopAdapter;
        $this->emailValidatorService = $emailValidatorService;
        $this->passwordHashService = $passwordHashService;
    }

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
    ): Admin {
        if (!$this->emailValidatorService->isEmailValid($email)) {
            throw new InvalidEmailException($email);
        }

        $this->checkRights($rights);

        if (!$this->shopAdapter->validateShopId($shopId)) {
            throw new InvalidShopException($shopId);
        }

        return new Admin(
            $this->shopAdapter->generateUniqueId(),
            $email,
            $this->passwordHashService->hash(($password)),
            $rights,
            $shopId
        );
    }

    /**
     * @throws InvalidRightsException
     */
    private function checkRights(string $rights): void
    {
        if (
            $rights != Admin::MALL_ADMIN &&
            !is_numeric($rights) &&
            !$this->shopAdapter->validateShopId((int) $rights)
        ) {
            throw new InvalidRightsException($rights);
        }
    }
}
