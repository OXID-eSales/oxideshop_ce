<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 07.08.17
 * Time: 10:20
 */

namespace OxidEsales\EshopCommunity\Tests\Internal\Integration\Database\OxArticles;


use OxidEsales\EshopCommunity\Tests\Internal\Integration\Database\AbstractDaoTests;

abstract class AbstractOxArticlesTest extends AbstractDaoTests
{

    /** @var \OxidEsales\EshopCommunity\Internal\Dao\ArticleDao $articleDao $articleDao */
    protected $articleDao;

    public function setUp()
    {

        parent::setUp();

        $this->articleDao = new \OxidEsales\EshopCommunity\Internal\Dao\ArticleDao(
            $this->getDoctrineConnection(),
            $this->contextStub,
            $this->legacyServiceStub
        );
    }

}