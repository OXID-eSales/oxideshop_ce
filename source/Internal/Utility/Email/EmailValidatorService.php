<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Utility\Email;

/**
 * Class EmailValidatorService.
 */
class EmailValidatorService implements EmailValidatorServiceInterface
{
    /**
     * @param mixed $email
     */
    public function isEmailValid($email): bool
    {
        return false !== filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
