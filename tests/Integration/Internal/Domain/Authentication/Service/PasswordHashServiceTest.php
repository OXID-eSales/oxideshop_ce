<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Password\Service;

use OxidEsales\EshopCommunity\Internal\Utility\Authentication\Exception\PasswordPolicyException;
use OxidEsales\EshopCommunity\Internal\Utility\Hash\Service\PasswordHashServiceInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class PasswordHashServiceTest extends TestCase
{
    use ContainerTrait;

    /**
     * End-to-end test to ensure, that the password policy checking is called during password hashing
     */
    public function testPasswordHashServiceEnforcesPasswordPolicy(): void
    {
        $this->expectException(PasswordPolicyException::class);

        $passwordUtf8 = 'äääääää';
        $passwordIso = mb_convert_encoding($passwordUtf8, 'ISO-8859-15');

        $passwordHashService = $this->get(PasswordHashServiceInterface::class);
        $passwordHashService->hash($passwordIso);
    }
}
