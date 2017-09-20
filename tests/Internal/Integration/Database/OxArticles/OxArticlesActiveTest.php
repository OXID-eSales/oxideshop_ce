<?php

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 07.08.17
 * Time: 10:10
 */

namespace OxidEsales\EshopCommunity\Tests\Internal\Integration\Database\OxArticles;

use OxidEsales\EshopCommunity\Tests\Internal\Integration\Database\AbstractDaoTests;

class OxArticlesActiveTest extends AbstractOxArticlesTest
{

    /** @var int $rowsInFixture */
    private $rowsInFixture = 4;


    /**
     * Just confirms that the fixture is correctly loaded
     */
    public function testAllRows()
    {

        // For rows in fixture
        $this->assertEquals($this->rowsInFixture, $this->connection->getRowCount('oxarticles'));
    }

    /**
     * Don't use oxActiveFrom and oxActiveTo in check
     */
    public function testGetActiveCheckQueryWithoutTime()
    {

        // Two hidden filtered out, one not active filtered out
        $this->assertEquals(
            $this->rowsInFixture - 3, $this->connection->getRowCount(
            'oxarticles',
            $this->articleDao->getIsActiveSqlSnippet(true)
        )
        );
    }

    /**
     * Use oxActiveFrom and oxActiveTo in check
     */
    public function testGetActiveCheckQueryWithTime()
    {

        // TODO: This actually checks a wrong behaviour. All hidden should also be filtered out.
        // But this is just as the shop works now.

        // One not in time range filtered out
        $this->contextStub->setUseTimeCheck(true);
        $this->assertEquals(
            $this->rowsInFixture - 1, $this->connection->getRowCount(
            'oxarticles',
            $this->articleDao->getIsActiveSqlSnippet(true)
        )
        );
    }

    public function getFixtureFile()
    {
        return dirname(__FILE__) . '/../../Fixtures/OxArticlesActiveTestFixture.xml';
    }

}