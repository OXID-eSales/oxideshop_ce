<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

class TplTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing Tpl::render()
     */
    public function testRender()
    {
        $this->setRequestParameter("tpl", 'fulder/subfolder/test');

        $oView = oxNew('Tpl');
        $this->assertSame('custom/test', $oView->render());
    }
}
