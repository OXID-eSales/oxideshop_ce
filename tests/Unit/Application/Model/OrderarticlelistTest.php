<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;
use OxidEsales\Eshop\Application\Model\Order;

class OrderarticlelistTest extends \OxidTestCase
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
        $oOrder = oxNew(Order::class);
        $oOrder->setId('_testOrderId');
        $oOrder->oxorder__oxuserid = new oxField('oxdefaultadmin', oxField::T_RAW);
        $oOrder->save();

        $this->_oOrderArticle = oxNew('oxorderarticle');
        $this->_oOrderArticle->setId('_testOrderArticleId');
        $this->_oOrderArticle->oxorderarticles__oxartid = new oxField('_testArticleId', oxField::T_RAW);
        $this->_oOrderArticle->oxorderarticles__oxorderid = new oxField($oOrder->getId());
        $this->_oOrderArticle->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArticleId');
        $oArticle->oxarticles__oxtitle = new oxField('testArticleTitle', oxField::T_RAW);
        $oArticle->oxarticles__oxactive = new oxField('1', oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField('10', oxField::T_RAW);
        $oArticle->oxarticles__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);

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
        $oOrderArticleList = oxNew('oxorderarticlelist');
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
        $oOrderArticleList = oxNew('oxorderarticlelist');
        $oOrderArticleList->loadOrderArticlesForUser(null);
        $this->assertEquals(0, $oOrderArticleList->count());
    }
}
