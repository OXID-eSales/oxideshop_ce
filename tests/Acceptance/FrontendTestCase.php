<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance;

abstract class FrontendTestCase extends AcceptanceTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->callShopSC("oxConfig", null, null, array(
            "iTopNaviCatCount" => array(
                "type" => "str",
                "value" => '3',
                "module" => "theme:azure"
            ),
            "aNrofCatArticles" => array(
                "type" => "arr",
                "value" => 'a:6:{i:0;s:2:"10";i:1;s:2:"20";i:2;s:2:"50";i:3;s:3:"100";i:4;s:1:"2";i:5;s:1:"1";}',
                "module" => "theme:azure"
            ),
            "aNrofCatArticlesInGrid" => array(
                "type" => "arr",
                "value" => 'a:4:{i:0;s:2:"12";i:1;s:2:"16";i:2;s:2:"24";i:3;s:2:"32";}',
                "module" => "theme:azure"
            )
        ));
    }
}
