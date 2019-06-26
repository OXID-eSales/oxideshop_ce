<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxPasswordHasher;

class PasswordHasherTest extends \OxidTestCase
{
    public function testHash()
    {
        $sPassword = 'password';
        $sSalt = 'salt';

        $oHasher = $this->getMock('oxSha512Hasher');
        $oHasher->expects($this->once())->method('hash')->with($this->equalTo($sPassword . $sSalt));

        $oPasswordHasher = new oxPasswordHasher($oHasher);

        $oPasswordHasher->hash($sPassword, $sSalt);
    }
}
