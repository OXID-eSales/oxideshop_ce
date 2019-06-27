<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use oxStr;
use oxStrMb;

class StrTest extends \OxidTestCase
{
    public function testGetStrHandler()
    {
        $oStr = $this->getProxyClass('oxStr');

        $this->assertTrue($oStr->UNITgetStrHandler() instanceof \OxidEsales\EshopCommunity\Core\StrMb);
    }

    public function testGetStr()
    {
        $this->assertTrue(oxStr::getStr() instanceof \OxidEsales\EshopCommunity\Core\StrMb);
    }
}
