<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 27.06.17
 * Time: 14:18
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\DocumentationTests;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Price;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\EshopCommunity\Internal\ServiceFactory;
use OxidEsales\TestingLibrary\UnitTestCase;


class ArticleTest extends UnitTestCase
{

    const TIME_PATTERN = "'\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}'";

    /** @var  Article $article */
    private $article;

    public function setUp()
    {

        ServiceFactory::reset();

        $this->setLanguage(0);

        $this->article = oxNew(Article::class);
        $this->article->setId('_test_article');
        $this->article->save();
    }

    public function tearDown()
    {

        $this->cleanUpTable('oxarticles');
    }

    public function testGetActiveCheckQueryDefaultDe()
    {

        $result = $this->article->getActiveCheckQuery();

        $this->assertEquals(' oxv_oxarticles_de.oxactive = 1  and oxv_oxarticles_de.oxhidden = 0 ', $result);
    }

    public function testGetActiveCheckQueryDefaultEn()
    {

        $this->setLanguage(1);
        $this->article = oxNew(Article::class);
        $this->assertEquals(1, $this->article->getLanguage());

        $result = $this->article->getActiveCheckQuery();

        $this->assertEquals(' oxv_oxarticles_en.oxactive = 1  and oxv_oxarticles_en.oxhidden = 0 ', $result);
    }

    public function testGetActiveCheckQueryUseTimeCheck()
    {

        Registry::getConfig()->setConfigParam('blUseTimeCheck', true);

        $result = $this->article->getActiveCheckQuery();

        $this->assertRegExp(
            '/ \(   oxv_oxarticles_de.oxactive = 1  and oxv_oxarticles_de.oxhidden = 0  or  \( oxv_oxarticles_de.oxactivefrom < ' .
            self::TIME_PATTERN .
            ' and oxv_oxarticles_de.oxactiveto > ' .
            self::TIME_PATTERN .
            ' \) \) /', $result
        );
    }

    public function testGetActiveCheckQueryTrue()
    {

        Registry::getConfig()->setConfigParam('blUseTimeCheck', false);
        $result = $this->article->getActiveCheckQuery(true);

        $this->assertEquals(' oxarticles.oxactive = 1  and oxarticles.oxhidden = 0 ', $result);
    }

    public function testGetActiveCheckQueryFalse()
    {

        Registry::getConfig()->setConfigParam('blUseTimeCheck', false);
        $result = $this->article->getActiveCheckQuery(false);

        $this->assertEquals(' oxv_oxarticles_de.oxactive = 1  and oxv_oxarticles_de.oxhidden = 0 ', $result);
    }

    public function testGetStockCheckQueryDontUseStock()
    {

        $config = Registry::getConfig();
        $config->setConfigParam('blUseStock', false);
        // config parameter blVariantParentBuyable does not matter here
        // config parameter blUseTimeCheck does not matter here

        $result = $this->article->getStockCheckQuery();

        $this->assertEquals('', $result);
    }

    public function testGetStockCheckQueryUseStockParentNotBuyable()
    {

        $config = Registry::getConfig();
        $config->setConfigParam('blUseStock', true);
        $config->setConfigParam('blVariantParentBuyable', true);
        // config parameter blUseTimeCheck does not matter here

        $result = $this->article->getStockCheckQuery();

        $this->assertEquals(
            ' and ( oxv_oxarticles_de.oxstockflag != 2 or ( oxv_oxarticles_de.oxstock + ' .
            'oxv_oxarticles_de.oxvarstock ) > 0  ) ', $result
        );
    }

    public function testGetStockCheckQueryUseStock()
    {

        $config = Registry::getConfig();
        $config->setConfigParam('blUseStock', true);
        $config->setConfigParam('blVariantParentBuyable', false);
        $config->setConfigParam('blUseTimeCheck', false);

        $result = $this->article->getStockCheckQuery();

        $this->assertEquals(
            '  and ( oxv_oxarticles_de.oxstockflag != 2 or ( oxv_oxarticles_de.oxstock + ' .
            'oxv_oxarticles_de.oxvarstock ) > 0  )  and IF( oxv_oxarticles_de.oxvarcount = 0, 1, ' .
            '( select 1 from oxv_oxarticles_de as art where art.oxparentid=oxv_oxarticles_de.oxid ' .
            'and art.oxactive = 1 and ( art.oxstockflag != 2 or art.oxstock > 0 ) limit 1 ) ) ', $result
        );
    }

    public function testGetStockCheckQueryUseStockParentNotBuyableWithTimeCheck()
    {

        $config = Registry::getConfig();
        $config->setConfigParam('blUseStock', true);
        $config->setConfigParam('blVariantParentBuyable', false);
        $config->setConfigParam('blUseTimeCheck', true);

        $result = $this->article->getStockCheckQuery();

        $this->assertRegExp(
            '/  and \( oxv_oxarticles_de.oxstockflag != 2 or \( oxv_oxarticles_de.oxstock \+ ' .
            'oxv_oxarticles_de.oxvarstock \) > 0  \)  and IF\( oxv_oxarticles_de.oxvarcount = 0, 1, ' .
            '\( select 1 from oxv_oxarticles_de as art where art.oxparentid=oxv_oxarticles_de.oxid and  ' .
            '\(  art.oxactive = 1 or  \( art.oxactivefrom < ' .
            self::TIME_PATTERN .
            ' and art.oxactiveto > ' .
            self::TIME_PATTERN .
            ' \) \)  and \( art.oxstockflag != 2 or art.oxstock > 0 \) limit 1 \) \) /',
            $result
        );
    }

}