<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Utility\Email;

/**
 * Class EmailValidatorServiceBridge.
 */
class EmailValidatorServiceBridge implements EmailValidatorServiceBridgeInterface
{
    /**
     * @var EmailValidatorServiceInterface
     */
    private $emailValidatorService;

    /**
     * EmailValidatorServiceBridge constructor.
     */
    public function __construct(EmailValidatorServiceInterface $emailValidatorService)
    {
        $this->emailValidatorService = $emailValidatorService;
    }

    /**
     * @param mixed $email
     */
    public function isEmailValid($email): bool
    {
        return $this->emailValidatorService->isEmailValid($email);
    }
}
