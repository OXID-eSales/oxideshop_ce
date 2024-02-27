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
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Exception\UserExistsException;
use OxidEsales\EshopCommunity\Internal\Utility\Email\EmailValidatorServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Utility\Hash\Service\PasswordHashServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Dao\AdminDaoInterface;

class AdminFactory implements AdminFactoryInterface
{
    public function __construct(
        private ShopAdapterInterface $shopAdapter,
        private EmailValidatorServiceInterface $emailValidatorService,
        private PasswordHashServiceInterface $passwordHashService,
        private AdminDaoInterface $adminDaoService
    ) {
    }

    /**
     * @param string $email
     * @param string $password
     * @param string $rights
     * @param int $shopId
     * @return Admin
     * @throws InvalidEmailException
     * @throws InvalidRightsException
     * @throws InvalidShopException
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
        
        if($this->adminDaoService->userNameExits($email, $shopId)) {
            echo 'User already exists: '.$email.PHP_EOL;
            exit;
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
