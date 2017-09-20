<?php

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 07.08.17
 * Time: 10:10
 */

namespace OxidEsales\EshopCommunity\Tests\Internal\Integration\Database\OxArticles;

use OxidEsales\EshopCommunity\Tests\Internal\Integration\Database\AbstractDaoTests;

/**
 * Class OxArticlesActiveTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Internal\Integration\Database\OxArticles
 *
 * This tests the stock code as provided by the shop - even where it does not make
 * any sense.
 *
 * There are several issues:
 *
 * - Why there is no active check when the parent is buyable makes no sense
 *
 * - When there is an active check (triggered by parent not buyable), then
 *   the hidden flag is not considered (which it should be). In fact, the
 *   method for the active snippet should be used, not some separate active
 *   check. And then one could rely on the test for the active snippet.
 *
 * All in all, the method has to vast a complexity to really test all cases. This
 * is just a selection.
 *
 * Also to reduce complexity there is no testing that it works with views instead of the table.
 * Testing that the correct view is selected should have been handled by other tests.
 *
 */
class OxArticlesCheckStockTest extends AbstractOxArticlesTest
{

    /**
     * @dataProvider stockTestDataProviderForBuyableParent
     */
    public function testGetStockCheckQueryParentBuyable($oxid, $isInStock)
    {

        $this->contextStub->setVariantParentBuyable(true);

        $where = "oxid = '$oxid'" . $this->articleDao->getStockCheckQuerySnippet(true);

        $this->assertEquals($isInStock, $this->connection->getRowCount('oxarticles', $where));
    }

    public function stockTestDataProviderForBuyableParent()
    {

        return [
            ['id' => '1', 'isInStock' => 1], // Stock flag is set, neither stock nor varstock is set
            ['id' => '2', 'isInStock' => 0], // Out of stock flag is set
            ['id' => '3', 'isInStock' => 1], // Out of stock flag is set, but stock is set
            ['id' => '4', 'isInStock' => 1]  // Out of stock, but variant is in stock without varstock being set
        ];
    }

    /**
     * If the parent is not buyable some crude SQL is build:
     *
     * ( oxarticles.oxstockflag != 2 or ( oxarticles.oxstock + oxarticles.oxvarstock ) > 0  )
     * and IF( oxarticles.oxvarcount = 0, 1,
     * ( select 1 from oxarticles as art where art.oxparentid=oxarticles.oxid
     *                                     and art.oxactive = 1 and
     *                                       ( art.oxstockflag != 2 or art.oxstock > 0 ) limit 1 ) )
     *
     * So the algorithm is:
     *
     * When the article has no variants (varcount is 0), then
     *   an article is in stock if
     *     the stockflag is not set to "out of stock" (2)
     *   or
     *     the article stock or the variant stock is > 0
     * else if the article has variants (varcount != 0)
     *     the above is true
     *   and
     *       there is at least one variant that is active
     *     and
     *       the stockflag is not set to out of stock or the stock is > 0
     *
     *
     * @dataProvider stockTestDataProviderForNotBuyableParent
     */
    public function testGetStockCheckQueryParentBuyableNotBuyableNoTimestamp($oxid, $isInStock)
    {

        $this->contextStub->setVariantParentBuyable(false);
        $this->contextStub->setUseTimeCheck(false);

        $where = "oxid = '$oxid'" . $this->articleDao->getStockCheckQuerySnippet(true);

        $this->assertEquals($isInStock, $this->connection->getRowCount('oxarticles', $where));
    }

    public function stockTestDataProviderForNotBuyableParent()
    {

        return [
            ['id' => '4', 'isInStock' => 0], // In stock, but variant is not in stock
            ['id' => '6', 'isInStock' => 1], // In stock, and variant is active
            ['id' => '8', 'isInStock' => 0], // In stock, but variant is not active
            ['id' => '10', 'isInStock' => 0]  // In stock, but variant is not active due to timestamp
        ];
    }

    /**
     * @dataProvider stockTestDataProviderForNotBuyableParentTimestamped
     */
    public function testGetStockCheckQueryParentBuyableNotBuyableWithTimestamp($oxid, $isInStock)
    {

        $this->contextStub->setVariantParentBuyable(false);
        $this->contextStub->setUseTimeCheck(true);

        $where = "oxid = '$oxid'" . $this->articleDao->getStockCheckQuerySnippet(true);

        $this->assertEquals($isInStock, $this->connection->getRowCount('oxarticles', $where));
    }

    public function stockTestDataProviderForNotBuyableParentTimestamped()
    {

        return [
            ['id' => '4', 'isInStock' => 0], // In stock, but variant is not in stock
            ['id' => '6', 'isInStock' => 1], // In stock, and variant is active
            ['id' => '8', 'isInStock' => 0], // In stock, but variant is not active
            ['id' => '10', 'isInStock' => 1]  // In stock, but variant is active due to timestamp
        ];
    }

    public function getFixtureFile()
    {
        return dirname(__FILE__) . '/../../Fixtures/OxArticlesCheckStockTestFixture.xml';
    }

}