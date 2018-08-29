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
class LinksTest extends \OxidTestCase
{

    /**
     * Test get link list.
     *
     * @return null
     */
    public function testGetLinksList()
    {
        $oLinks = $this->getProxyClass('links');
        $oLink = $oLinks->getLinksList()->current();
        $this->assertEquals('http://www.oxid-esales.com', $oLink->oxlinks__oxurl->value);
    }

    /**
     * Testing Links::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oLinks = oxNew('Links');
        $aResult = array();
        $aResults = array();

        $aResult["title"] = oxRegistry::getLang()->translateString('LINKS', oxRegistry::getLang()->getBaseLanguage(), false);
        $aResult["link"] = $oLinks->getLink();

        $aResults[] = $aResult;

        $this->assertEquals($aResults, $oLinks->getBreadCrumb());
    }
}
