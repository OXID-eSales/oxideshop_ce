<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

class Sha512HasherTest extends \OxidTestCase
{
    public function testEncrypt()
    {
        $sHash = 'b32e441399b4601e11846563bea5c6597b7fbeeb8d443a05cdaf0c5615f6bd9c168eac63856945c2b188f933db330f8202bbd4a2a4abadef0ed96f6247970622';
        $oHasher = oxNew('oxSha512Hasher');

        $this->assertSame($sHash, $oHasher->hash('somestring05853e9aba10b9c25a3b8af5618ec9fa'));
    }
}
