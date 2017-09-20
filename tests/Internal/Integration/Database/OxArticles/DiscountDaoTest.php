<?php

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 07.08.17
 * Time: 10:10
 */

namespace OxidEsales\EshopCommunity\Tests\Internal\Integration\Database\OxArticles;

use OxidEsales\EshopCommunity\Internal\Dao\DiscountDao;
use OxidEsales\EshopCommunity\Internal\Dao\PriceInformationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Dao\UserDaoInterface;

/**
 * Class PriceInformationDaoIIITest
 *
 * This test class tests the discounts fetching
 *
 * @package OxidEsales\EshopCommunity\Tests\Internal\Integration\Database\OxArticles
 */
class DiscountDaoTest extends AbstractOxArticlesTest
{

    /** @var  DiscountDao $discountDao */
    private $discountDao;

    /** @var  UserDaoInterface $userDao */
    private $userDao;

    /** @var  PriceInformationDaoInterface $priceInformationDao */
    private $priceInformationDao;

    public function setUp()
    {


        parent::setUp();

        $this->userDao = $this->getMockBuilder(UserDaoInterface::class)->getMock();
        $this->priceInformationDao = $this->getMockBuilder(PriceInformationDaoInterface::class)->getMock();
        $this->discountDao = new DiscountDao(
            $this->getDoctrineConnection(),
            $this->priceInformationDao,
            $this->userDao,
            $this->contextStub,
            $this->legacyServiceStub
        );
    }

    /**
     * @dataProvider discountData
     *
     * @param $articleId
     * @param $userId
     * @param $countryId
     * @param $expected
     */
    public function testArticleDiscount($articleId, $amount, $userId, $countryId, $timecheck, $expected)
    {

        $this->contextStub->setUseTimeCheck($timecheck);

        $this->userDao->method('getUserCountryId')->willReturn($countryId);

        $discounts = $this->discountDao->getArticleDiscounts($articleId, $amount, $userId);
        $this->assertEquals(sizeof($expected), sizeof($discounts));
        for ($i = 0; $i < sizeof($expected); $i++) {
            $this->assertEquals($expected[$i], $discounts[$i]->getId());
        }
    }

    public function discountData()
    {

        return [
            // Just a simple article discount
            ['articleid' => 'A1', 'amount' => 1, 'userid' => 'U-', 'countryid' => 'C-', 'timecheck' => true, 'expected' => ['D1']],
            // Discount is not for amount > 5 (this feature does not make any sense at all)
            ['articleid' => 'A1', 'amount' => 6, 'userid' => 'U-', 'countryid' => 'C-', 'timecheck' => true, 'expected' => []],
            // Article discount for variant through parent mapping
            ['articleid' => 'A2', 'amount' => 1, 'userid' => 'U-', 'countryid' => 'C-', 'timecheck' => true, 'expected' => ['D1']],
            // Article discount for variant through category mapping
            ['articleid' => 'A3', 'amount' => 1, 'userid' => 'U-', 'countryid' => 'C-', 'timecheck' => true, 'expected' => ['D6']],
            // Check that the type field is evaluated - an existing user id should not trigger as article id
            ['articleid' => 'U1', 'amount' => 1, 'userid' => 'U-', 'countryid' => 'C-', 'timecheck' => true, 'expected' => []],
            // Simple user discount
            ['articleid' => 'A-', 'amount' => 1, 'userid' => 'U1', 'countryid' => 'C-', 'timecheck' => true, 'expected' => ['D2']],
            // Simple country discount
            ['articleid' => 'A-', 'amount' => 1, 'userid' => 'U-', 'countryid' => 'C1', 'timecheck' => true, 'expected' => ['D3']],
            // Combined article, user and country discount
            ['articleid' => 'A1', 'amount' => 1, 'userid' => 'U1', 'countryid' => 'C1', 'timecheck' => true, 'expected' => ['D1', 'D2', 'D3']],
            // User discount with time check on (active is false, but timerange matches current time)
            ['articleid' => 'A-', 'amount' => 1, 'userid' => 'U2', 'countryid' => 'C-', 'timecheck' => true, 'expected' => ['D4']],
            // User discount without time check on (timerange matches current time but is not evaluated)
            ['articleid' => 'A-', 'amount' => 1, 'userid' => 'U2', 'countryid' => 'C-', 'timecheck' => false, 'expected' => []]

        ];
    }

    public function getFixtureFile()
    {
        return dirname(__FILE__) . '/../../Fixtures/DiscountDaoTestFixture.xml';
    }

}