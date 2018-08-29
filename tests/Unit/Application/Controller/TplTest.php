<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

class TplTest extends \OxidTestCase
{

    /**
     * Testing Tpl::render()
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter("tpl", 'fulder/subfolder/test.tpl');

        $oView = oxNew('Tpl');
        $this->assertEquals('custom/test.tpl', $oView->render());
    }
}
