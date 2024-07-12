<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use oxStr;
use oxStrMb;

class StrTest extends \PHPUnit\Framework\TestCase
{
    public function testGetStrHandler()
    {
        $oStr = $this->getProxyClass('oxStr');

        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Core\StrMb::class, $oStr->getStrHandler());
    }

    public function testGetStr()
    {
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Core\StrMb::class, oxStr::getStr());
    }
}
