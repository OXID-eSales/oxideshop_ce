<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxRegistry;

/**
 * Testing links class
 */
class LinksTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Test get link list.
     */
    public function testGetLinksList()
    {
        $oLinks = $this->getProxyClass('links');
        $oLink = $oLinks->getLinksList()->current();
        $this->assertEquals('http://www.oxid-esales.com', $oLink->oxlinks__oxurl->value);
    }

    /**
     * Testing Links::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $oLinks = oxNew('Links');
        $aResult = [];
        $aResults = [];

        $aResult["title"] = oxRegistry::getLang()->translateString('LINKS', oxRegistry::getLang()->getBaseLanguage(), false);
        $aResult["link"] = $oLinks->getLink();

        $aResults[] = $aResult;

        $this->assertEquals($aResults, $oLinks->getBreadCrumb());
    }
}
