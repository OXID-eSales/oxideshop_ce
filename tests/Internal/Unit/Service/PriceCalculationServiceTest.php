<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 31.08.17
 * Time: 11:52
 */

namespace OxidEsales\EshopCommunity\Tests\Internal\Unit\Service;


use OxidEsales\EshopCommunity\Internal\Dao\DiscountDaoInterface;
use OxidEsales\EshopCommunity\Internal\Dao\PriceInformationDao;
use OxidEsales\EshopCommunity\Internal\Dao\PriceInformationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Dao\UserDao;
use OxidEsales\EshopCommunity\Internal\Dao\UserDaoInterface;
use OxidEsales\EshopCommunity\Internal\Service\PriceCalculationService;
use OxidEsales\EshopCommunity\Tests\Internal\Unit\ContextStub;
use OxidEsales\EshopCommunity\Tests\Internal\Unit\OxidLegacyServiceStub;

class PriceCalculationServiceTest extends \PHPUnit_Framework_TestCase
{

    /** @var  PriceCalculationService $priceCalculationService */
    private $priceCalculationService;

    /** @var PriceInformationDaoInterface */
    private $priceInformationDaoMock;

    /** @var  UserDaoInterface */
    private $userDaoMock;

    /** @var  ContextStub $context */
    private $context;

    /** @var  OxidLegacyServiceStub */
    private $legacyService;

    /** @var  DiscountDaoInterface */
    private $discountDaoMock;

    public function setUp()
    {

        parent::setUp();

        $this->context = new ContextStub();
        $this->legacyService = new OxidLegacyServiceStub();

        $this->priceInformationDaoMock = $this->getMockBuilder(PriceInformationDaoInterface::class)
            ->getMock();
        $this->userDaoMock = $this->getMockBuilder(UserDaoInterface::class)
            ->getMock();
        $this->discountDaoMock = $this->getMockBuilder(DiscountDaoInterface::class)
            ->getMock();

        $this->priceCalculationService = new PriceCalculationService(
            $this->priceInformationDaoMock,
            $this->userDaoMock,
            $this->discountDaoMock,
            $this->context,
            $this->legacyService
        );
    }

    public function testGetBasePrice()
    {
        // TODO mk: Write test
    }

    public function testGetPriceObject()
    {
        // TODO mk: write test

    }

    public function testGetArticleVat()
    {
        // TODO mk: write test

    }

    /**
     * @dataProvider vatData
     *
     * @param $vatRegion
     * @param $ustidExists
     * @param $expected
     */
    public function testIsUserVatTaxable($vatRegion, $ustidExists, $expected)
    {

        $this->userDaoMock->method('getVatRegion')->with('userid')->willReturn(UserDaoInterface::VAT_REGION_HOME_COUNTRY);
        $this->userDaoMock->method('ustIdExist')->with('userid')->willReturn(true);
        $this->assertEquals(true, $this->priceCalculationService->isUserVatTaxable('userid'));
    }

    public function vatData()
    {

        return [
            ['vatregion' => UserDaoInterface::VAT_REGION_HOME_COUNTRY, 'ustidexists' => true, 'exepcted' => true],
            ['vatregion' => UserDaoInterface::VAT_REGION_HOME_COUNTRY, 'ustidexists' => false, 'exepcted' => true],
            ['vatregion' => UserDaoInterface::VAT_REGION_EU, 'ustidexists' => true, 'exepcted' => false],
            ['vatregion' => UserDaoInterface::VAT_REGION_EU, 'ustidexists' => false, 'exepcted' => true],
            ['vatregion' => UserDaoInterface::VAT_REGION_OUTSIDE_EU, 'ustidexists' => true, 'exepcted' => false],
            ['vatregion' => UserDaoInterface::VAT_REGION_OUTSIDE_EU, 'ustidexists' => false, 'exepcted' => false]
        ];
    }

}