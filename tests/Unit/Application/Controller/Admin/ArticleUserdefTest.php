<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxTestModules;

/**
 * Tests for Article_Userdef class
 */
class ArticleUserdefTest extends \OxidTestCase
{

    /**
     * Article_Userdef::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");
        oxTestModules::addFunction('oxarticle', 'isDerived', '{ return true; }');

        // testing..
        $oView = oxNew('Article_Userdef');
        $this->assertEquals('article_userdef.tpl', $oView->render());
        $this->assertTrue($oView->getViewDataElement('readonly'));
    }
}
