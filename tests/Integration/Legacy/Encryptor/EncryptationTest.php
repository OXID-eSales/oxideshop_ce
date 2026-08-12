<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Encryptor;

use OxidEsales\Eshop\Core\Decryptor;
use OxidEsales\Eshop\Core\Encryptor;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class EncryptationTest extends IntegrationTestCase
{
    public static function providerEncodingAndDecodingGivesSameResultWithCorrectKey(): array
    {
        return [['testString', ''],
            ['testString', 1],
            ['testString', 'shortKey'],
            ['testString', 'longKeyLongKey_LongKeyLongKey'],
            ['', 'testKey'],
        ];
    }

    #[DataProvider('providerEncodingAndDecodingGivesSameResultWithCorrectKey')]
    public function testEncodingAndDecodingGivesSameResultWithCorrectKey(string $sString, string|int $sKey): void
    {
        $oEncryptor = oxNew(Encryptor::class);
        $oDecryptor = oxNew(Decryptor::class);

        $sEncrypted = $oEncryptor->encrypt($sString, $sKey);
        $this->assertSame($sString, $oDecryptor->decrypt($sEncrypted, $sKey));
    }

    public function testEncodingAndDecodingGivesDifferentResultWithIncorrectKey(): void
    {
        $oEncryptor = oxNew(Encryptor::class);
        $oDecryptor = oxNew(Decryptor::class);

        $sEncrypted = $oEncryptor->encrypt('testString', 'correctKey');
        $this->assertNotSame('testString', $oDecryptor->decrypt($sEncrypted, 'incorrectKey'));
    }
}
