<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

class Unit_Core_oxorderarticlelistTest extends OxidTestCase
{

    protected $_oOrderArticle = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setup()
    {
        parent::setUp();
        $this->_oOrderArticle = new oxorderarticle();
        $this->_oOrderArticle->setId('_testOrderArticleId');
        $this->_oOrderArticle->oxorderarticles__oxartid = new oxField('_testArticleId', oxField::T_RAW);
        $this->_oOrderArticle->oxorderarticles__oxorderid = new oxField('_testOrderId', oxField::T_RAW);
        $this->_oOrderArticle->save();

        $oOrder = new oxorder();
        $oOrder->setId('_testOrderId');
        $oOrder->oxorder__oxuserid = new oxField('oxdefaultadmin', oxField::T_RAW);
        $oOrder->save();

        $oArticle = new oxarticle();
        $oArticle->setId('_testArticleId');
        $oArticle->oxarticles__oxtitle = new oxField('testArticleTitle', oxField::T_RAW);
        $oArticle->oxarticles__oxactive = new oxField('1', oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField('10', oxField::T_RAW);
        $oArticle->oxarticles__oxshopid = new oxField(oxRegistry::getConfig()->getShopId(), oxField::T_RAW);

        $oArticle->save();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxorderarticles');
        $this->cleanUpTable('oxorder');
        $this->cleanUpTable('oxarticles');

        parent::tearDown();
    }

    /*
     * Test loading order articles for user
     */
    public function testLoadOrderArticlesForUser()
    {
        $oOrderArticleList = new oxorderarticlelist();
        $oOrderArticleList->loadOrderArticlesForUser('oxdefaultadmin');
        $this->assertEquals(1, $oOrderArticleList->count());
        $oOrderArticle = $oOrderArticleList->current();
        $this->assertEquals('_testOrderArticleId', $oOrderArticle->getId());
    }

    /*
     * Test loading order articles, if user is not set
     */
    public function testLoadOrderArticlesForUserIfUserIsNotSet()
    {
        $oOrderArticleList = new oxorderarticlelist();
        $oOrderArticleList->loadOrderArticlesForUser(null);
        $this->assertEquals(0, $oOrderArticleList->count());
    }

}
