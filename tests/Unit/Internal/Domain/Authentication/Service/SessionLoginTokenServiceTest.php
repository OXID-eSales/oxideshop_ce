<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Authentication\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Service\SessionLoginTokenService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SessionLoginTokenServiceTest extends TestCase
{
    private const PASSWORD_HASH = '$2y$12$fKcaDQwFvCQGcz1uEFADhuZ73e5D/AHo/n3gor8BJVuNFBpfewnj2';

    public function testIsValidAcceptsTokenOfCurrentPasswordHash(): void
    {
        $service = new SessionLoginTokenService();

        $this->assertTrue($service->isValid($service->generate(self::PASSWORD_HASH), self::PASSWORD_HASH));
    }

    public function testIsValidRejectsTokenAfterPasswordHashChanged(): void
    {
        $service = new SessionLoginTokenService();

        $this->assertFalse($service->isValid($service->generate(self::PASSWORD_HASH), 'changed-password-hash'));
    }

    #[DataProvider('invalidTokenDataProvider')]
    public function testIsValidRejectsInvalidToken(string $token): void
    {
        $this->assertFalse((new SessionLoginTokenService())->isValid($token, self::PASSWORD_HASH));
    }

    public static function invalidTokenDataProvider(): array
    {
        return [
            'empty token' => [''],
            'tampered token' => [str_repeat('a', 64)],
        ];
    }
}
