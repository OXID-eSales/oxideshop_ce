<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 27.06.17
 * Time: 14:18
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\DocumentationTests;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\ListModel;
use OxidEsales\Eshop\Core\Price;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\EshopCommunity\Internal\ServiceFactory;
use OxidEsales\TestingLibrary\UnitTestCase;


class ArticlePriceCalculationTest extends UnitTestCase
{

    /** @var  User $user */
    private $user;

    /** @var  Article $article */
    private $article;

    public function setUp()
    {

        $this->user = oxNew(User::class);
        $this->user->setId('_test_user');
        $this->user->oxuser__oxusername = new Field('testuser');
        $this->user->oxuser__oxpasssalt = new Field('abcd');
        $this->user->setPassword($this->user->encodePassword('password', 'abcd'));
        $this->user->save();

        $this->user->addToGroup('oxidpricea');

        $this->user->login('testuser', 'password');

        $this->article = oxNew(Article::class);
        $this->article->setId('_test_article');
        $this->article->oxarticles__oxpricea = new Field(99.9);
        $this->article->save();
    }

    public function tearDown()
    {

        $this->user->removeFromGroup('oxpricea');

        $this->cleanUpTable('oxuser');
        $this->cleanUpTable('oxarticles');
    }

    public function testgetBasePriceNoPriceLoading()
    {

        $this->article->disablePriceLoad();

        $this->assertNull($this->article->getBasePrice());
    }

    public function _testgetBasePriceNoPriceLoadingByConfig()
    {

        $config = Registry::getConfig();
        $config->setConfigParam('bl_perfLoadPrice', false);

        $this->assertTrue(null === $this->article->getBasePrice());
    }

    public function testGetPriceNewArticle()
    {


        $price = $this->article->getPrice();
        $expectedPrice = oxNew(Price::class);
        $expectedPrice->setPrice(99.9, 19.0);

        $this->assertEquals($expectedPrice, $price);
    }

    public function testGetPriceSingleArticleWithPrice()
    {

        $expectedPrice = oxNew(Price::class);
        $expectedPrice->setBruttoPriceMode();
        $expectedPrice->setPrice(2.5, 14.0);

        $this->article->setPrice($expectedPrice);

        $price = $this->article->getPrice();

        $this->assertEquals($expectedPrice, $price);
    }

    /**
     * @dataProvider basePriceTestInputI
     */
    public function testGetBasePrice($articleId, $amount, $expected)
    {

        $this->getConfig()->setConfigParam('blOverrideZeroABCPrices', true);


        $article = new Article();
        $this->assertTrue($article->load($articleId));
        $this->assertEquals($expected, $article->getBasePrice($amount));
    }

    public function basePriceTestInputI()
    {

        return [
            ['articleid' => '1651', 'amount' => 3, 'expected' => 29.0],  // Bulk price
            ['articleid' => '1651', 'amount' => 7, 'expected' => 27.5],  // Bulk price
            ['articleid' => '1651', 'amount' => 49, 'expected' => 25.0], // Bulk Price
            ['articleid' => '1651', 'amount' => 50, 'expected' => 25.0], // Bulk Price
            ['articleid' => '1651', 'amount' => 51, 'expected' => 23.0], // Bulk Price
            ['articleid' => '_test_article', 'amount' => 7, 'expected' => 99.9] // Group price
        ];
    }

    /**
     * @dataProvider basePriceTestInputII
     */
    public function testGetBasePriceWithoutZeroOverride($articleId, $amount, $expected)
    {

        $this->getConfig()->setConfigParam('blOverrideZeroABCPrices', false);

        $article = new Article();
        $this->assertTrue($article->load($articleId));
        $this->assertEquals($expected, $article->getBasePrice($amount));
    }

    public function basePriceTestInputII()
    {

        return [
            ['articleid' => '1651', 'amount' => 3, 'expected' => 0.0],  // Bulk price
            ['articleid' => '1651', 'amount' => 7, 'expected' => 0.0],  // Bulk price
            ['articleid' => '1651', 'amount' => 49, 'expected' => 0.0], // Bulk Price
            ['articleid' => '1651', 'amount' => 50, 'expected' => 0.0], // Bulk Price
            ['articleid' => '1651', 'amount' => 51, 'expected' => 0.0], // Bulk Price
            ['articleid' => '_test_article', 'amount' => 7, 'expected' => 99.9] // Group price
        ];
    }

    /**
     * @dataProvider articleVatInput
     *
     * @param $articleId
     * @param $expectedVat
     */

    public function testGetArticleVat($articleId, $expectedVat)
    {

        /** @var Article $article */
        $article = oxNew(Article::class);
        $this->assertTrue($article->load($articleId));
        $this->assertEquals($expectedVat, $article->getArticleVat());
    }

    public function articleVatInput()
    {

        return [
            ['articleid' => '1651', 'expectedvat' => 19.0]
        ];
    }

    /**
     * @dataProvider priceData
     *
     * @param $shownetprice
     * @param $enternetprice
     * @param $databaseprice
     */
    public function testGetPriceWithViewAndDBNetPrices($shownetprice, $enternetprice, $databaseprice, $price)
    {

        $config = Registry::getConfig();
        $config->setConfigParam('blShowNetPrice', $shownetprice);
        $config->setConfigParam('blEnterNetPrice', $enternetprice);

        /** @var Article article */
        $this->article->oxarticles__oxpricea = new Field($databaseprice);
        $this->article->save();

        /** @var Price $price */
        $priceObject = $this->article->getPrice();

        $this->assertEquals(100.0, $priceObject->getNettoPrice());
        $this->assertEquals(119.0, $priceObject->getBruttoPrice());
        $this->assertEquals($price, $priceObject->getPrice());
    }

    public function priceData()
    {
        return [
            ['shownetprice' => true, 'enternetprice' => true, 'databaseprice' => 100.0, 'price' => 100.0],
            ['shownetprice' => true, 'enternetprice' => false, 'databaseprice' => 119.0, 'price' => 100.0],
            ['shownetprice' => false, 'enternetprice' => true, 'databaseprice' => 100.0, 'price' => 119.0],
            ['shownetprice' => false, 'enternetprice' => false, 'databaseprice' => 119.0, 'price' => 119.0]
        ];
    }

}
