<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Utility\Email;

/**
 * Class EmailValidatorServiceBridge
 * @package OxidEsales\EshopCommunity\Internal\Utility\Email
 */
class EmailValidatorServiceBridge implements EmailValidatorServiceBridgeInterface
{
    public function __construct(private EmailValidatorServiceInterface $emailValidatorService)
    {
    }

    /**
     * @param mixed $email
     *
     * @return bool
     */
    public function isEmailValid($email): bool
    {
        return $this->emailValidatorService->isEmailValid($email);
    }
}
