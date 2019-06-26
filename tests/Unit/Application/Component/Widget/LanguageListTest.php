<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

/**
 * Tests for oxwLanguageList class
 */
class LanguageListTest extends \OxidTestCase
{

    /**
     * Testing oxwLanguageList::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oLanguageList = oxNew('oxwLanguageList');
        $this->assertEquals('widget/header/languages.tpl', $oLanguageList->render());
    }
}
