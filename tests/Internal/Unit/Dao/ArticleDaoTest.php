<?php

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 20.07.17
 * Time: 14:17
 */

namespace OxidEsales\EshopCommunity\Tests\Internal\Unit\Dao;


use Doctrine\DBAL\Connection;
use OxidEsales\EshopCommunity\Tests\Internal\Unit\ContextStub;
use OxidEsales\EshopCommunity\Tests\Internal\Unit\OxidLegacyServiceStub;

class ArticleDaoTest extends \PHPUnit_Framework_TestCase
{

    /** @var \OxidEsales\EshopCommunity\Internal\Dao\ArticleDao $articleDao $articleDao */
    private $articleDao;
    /** @var ContextStub $contextStub */
    private $contextStub;
    /** @var OxidLegacyServiceStub $legacyServiceStub */
    private $legacyServiceStub;

    /** @var  Connection $connectionMock */
    private $connectionMock;

    public function setUp()
    {

        $this->contextStub = new ContextStub();
        $this->legacyServiceStub = new OxidLegacyServiceStub();
        $this->connectionMock = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();

        $this->articleDao = new \OxidEsales\EshopCommunity\Internal\Dao\ArticleDao($this->connectionMock, $this->contextStub, $this->legacyServiceStub);
    }

    public function testGetViewName()
    {


        $this->assertEquals('oxv_oxarticles_de', $this->articleDao->getViewName(false));
    }

    public function testGetTableName()
    {

        $this->assertEquals('oxarticles', $this->articleDao->getViewName(true));
    }

    public function testGetActiveCheckQueryWithoutTime()
    {

        $snippet = $this->articleDao->getIsActiveSqlSnippet(false);
        $this->assertEquals(' oxv_oxarticles_de.oxactive = 1  and oxv_oxarticles_de.oxhidden = 0 ', $snippet);
    }

    public function testGetActiveCheckQueryWithoutTimeForTable()
    {

        $snippet = $this->articleDao->getIsActiveSqlSnippet(true);
        $this->assertEquals(' oxarticles.oxactive = 1  and oxarticles.oxhidden = 0 ', $snippet);
    }

    public function testGetActiveCheckQueryWithTime()
    {

        $this->contextStub->setUseTimeCheck(true);
        $snippet = $this->articleDao->getIsActiveSqlSnippet(false);
        $this->assertEquals(
            ' (   oxv_oxarticles_de.oxactive = 1  and oxv_oxarticles_de.oxhidden = 0  ' .
            'or  ( oxv_oxarticles_de.oxactivefrom < \'2017-07-24 10:00:00\' and ' .
            'oxv_oxarticles_de.oxactiveto > \'2017-07-24 10:00:00\' ) ) ', $snippet
        );
    }

    public function testGetActiveCheckQueryWithTimeForTable()
    {

        $this->contextStub->setUseTimeCheck(true);
        $snippet = $this->articleDao->getIsActiveSqlSnippet(true);
        $this->assertEquals(
            ' (   oxarticles.oxactive = 1  and oxarticles.oxhidden = 0  or  ' .
            '( oxarticles.oxactivefrom < \'2017-07-24 10:00:00\' ' .
            'and oxarticles.oxactiveto > \'2017-07-24 10:00:00\' ) ) ', $snippet
        );
    }

    public function testGetStockCheckQuerySnippetNoStock()
    {

        $this->contextStub->setUseStock(false);
        $snippet = $this->articleDao->getStockCheckQuerySnippet(true);
        $this->assertTrue('' === $snippet);
    }

    public function testGetStockCheckQueryParentBuyable()
    {

        $this->contextStub->setVariantParentBuyable(true);
        $snippet = $this->articleDao->getStockCheckQuerySnippet(false);
        $this->assertEquals(
            ' and ( oxv_oxarticles_de.oxstockflag != 2 or ( oxv_oxarticles_de.oxstock + ' .
            'oxv_oxarticles_de.oxvarstock ) > 0  ) ', $snippet
        );
    }

    public function testGetStockCheckQueryParentBuyableNotBuyableNoTimestamp()
    {

        $this->contextStub->setVariantParentBuyable(false);
        $this->contextStub->setUseTimeCheck(false);

        $snippet = $this->articleDao->getStockCheckQuerySnippet(false);
        $this->assertEquals(
            '  and ( oxv_oxarticles_de.oxstockflag != 2 or ( oxv_oxarticles_de.oxstock + ' .
            'oxv_oxarticles_de.oxvarstock ) > 0  )  and IF( oxv_oxarticles_de.oxvarcount = 0, 1, ' .
            '( select 1 from oxv_oxarticles_de as art where art.oxparentid=oxv_oxarticles_de.oxid ' .
            'and art.oxactive = 1 and ( art.oxstockflag != 2 or art.oxstock > 0 ) limit 1 ) ) ', $snippet
        );
    }

    public function testGetStockCheckQueryParentBuyableNotBuyableWithTimestamp()
    {

        $this->contextStub->setVariantParentBuyable(false);
        $this->contextStub->setUseTimeCheck(true);

        $snippet = $this->articleDao->getStockCheckQuerySnippet(false);
        $this->assertEquals(
            '  and ( oxv_oxarticles_de.oxstockflag != 2 or ( oxv_oxarticles_de.oxstock + ' .
            'oxv_oxarticles_de.oxvarstock ) > 0  )  and IF( oxv_oxarticles_de.oxvarcount = 0, 1, ' .
            '( select 1 from oxv_oxarticles_de as art where art.oxparentid=oxv_oxarticles_de.oxid and  ' .
            '(  art.oxactive = 1 or  ( art.oxactivefrom < \'' .
            $this->legacyServiceStub->getCurrentTimeDBFormatted() .
            '\' and art.oxactiveto > \'' .
            $this->legacyServiceStub->getCurrentTimeDBFormatted() .
            '\' ) )  and ( art.oxstockflag != 2 or art.oxstock > 0 ) limit 1 ) ) ', $snippet
        );
    }

    public function testGetMaximumStockCheckQueryWithTablename()
    {

        $this->contextStub->setVariantParentBuyable(false);
        $this->contextStub->setUseTimeCheck(true);

        $snippet = $this->articleDao->getStockCheckQuerySnippet(true);
        $this->assertEquals(
            '  and ( oxarticles.oxstockflag != 2 or ( oxarticles.oxstock + ' .
            'oxarticles.oxvarstock ) > 0  )  and IF( oxarticles.oxvarcount = 0, 1, ' .
            '( select 1 from oxarticles as art where art.oxparentid=oxarticles.oxid and  ' .
            '(  art.oxactive = 1 or  ( art.oxactivefrom < \'' .
            $this->legacyServiceStub->getCurrentTimeDBFormatted() .
            '\' and art.oxactiveto > \'' .
            $this->legacyServiceStub->getCurrentTimeDBFormatted() .
            '\' ) )  and ( art.oxstockflag != 2 or art.oxstock > 0 ) limit 1 ) ) ', $snippet
        );
    }

    public function testGetMaximumStockCheckQueryWithSecondLanguage()
    {

        $this->contextStub->setVariantParentBuyable(false);
        $this->contextStub->setUseTimeCheck(true);
        $this->contextStub->setCurrentLanguageAbbrevitation('en');

        $snippet = $this->articleDao->getStockCheckQuerySnippet(false);
        $this->assertEquals(
            '  and ( oxv_oxarticles_en.oxstockflag != 2 or ( oxv_oxarticles_en.oxstock + ' .
            'oxv_oxarticles_en.oxvarstock ) > 0  )  and IF( oxv_oxarticles_en.oxvarcount = 0, 1, ' .
            '( select 1 from oxv_oxarticles_en as art where art.oxparentid=oxv_oxarticles_en.oxid and  ' .
            '(  art.oxactive = 1 or  ( art.oxactivefrom < \'' .
            $this->legacyServiceStub->getCurrentTimeDBFormatted() .
            '\' and art.oxactiveto > \'' .
            $this->legacyServiceStub->getCurrentTimeDBFormatted() .
            '\' ) )  and ( art.oxstockflag != 2 or art.oxstock > 0 ) limit 1 ) ) ', $snippet
        );
    }

}