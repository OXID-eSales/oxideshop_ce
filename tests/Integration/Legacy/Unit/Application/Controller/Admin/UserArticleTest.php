<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\OrderArticleList;

/**
 * Tests for User_Article class
 */
class UserArticleTest extends \PHPUnit\Framework\TestCase
{

    /**
     * User_Article::Render() test case
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "oxdefaultadmin");

        // testing..
        $oView = oxNew('User_Article');
        $this->assertEquals('user_article', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oArticlelist']));
        $this->assertTrue($aViewData['oArticlelist'] instanceof orderarticlelist);
    }
}
