<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Utility\Email;

/**
 * Class EmailValidatorService
 * @package OxidEsales\EshopCommunity\Internal\Utility\Email
 */
class EmailValidatorService implements EmailValidatorServiceInterface
{
    /**
     * @param mixed $email
     *
     * @return bool
     */
    public function isEmailValid($email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
