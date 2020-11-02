<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Utility\Authentication\Policy;

use OxidEsales\EshopCommunity\Internal\Utility\Authentication\Exception\PasswordPolicyException;

class PasswordPolicy implements PasswordPolicyInterface
{
    /**
     * Enforces password policy.
     *
     * @throws PasswordPolicyException
     */
    public function enforcePasswordPolicy(string $password): void
    {
        /*
         * A password policy should at least ensure, that the same character encoding is used for hashing and
         * verification. As there is no real way to ensure, that a byte stream is encoded in a certain character
         * set, at least is should ensured that the password is valid UTF-8.
         */
        if (!$this->isValidUtf8($password)) {
            throw new PasswordPolicyException('The password policy requires UTF-8 encoded strings');
        }
    }

    private function isValidUtf8(string $password): bool
    {
        /*
         * Use the PCRE_UTF8 pattern modifier to test, if the given string this is a valid UTF-8 string.
         * See http://php.net/manual/de/reference.pcre.pattern.modifiers.php
         * preg_match will return false on a invalid subject
         * Not perfect, but good enough.
         */
        return false !== preg_match('//u', $password);
    }
}
