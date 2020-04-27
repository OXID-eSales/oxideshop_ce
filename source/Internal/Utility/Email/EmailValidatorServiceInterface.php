<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Utility\Email;

interface EmailValidatorServiceInterface
{
    /**
     * @param mixed $email
     *
     * @return bool
     */
    public function isEmailValid($email): bool;
}
