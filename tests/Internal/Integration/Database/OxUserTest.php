<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 31.08.17
 * Time: 11:11
 */

namespace OxidEsales\EshopCommunity\Tests\Internal\Integration\Database;


use OxidEsales\EshopCommunity\Internal\Dao\UserDao;
use OxidEsales\EshopCommunity\Internal\Dao\UserDaoInterface;
use OxidEsales\EshopCommunity\Internal\Utilities\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Internal\Unit\ContextStub;
use OxidEsales\EshopCommunity\Tests\Internal\Unit\OxidLegacyServiceStub;

class OxUserTest extends AbstractDaoTests
{

    /** @var  UserDao $userDao */
    private $userDao;

    /** @var  ContextStub $context */
    private $context;

    /** @var  OxidLegacyServiceStub $legacyService */
    private $legacyService;

    public function setUp()
    {

        parent::setUp();

        $this->context = new ContextStub();
        $this->legacyService = new OxidLegacyServiceStub();

        $this->userDao = new UserDao($this->getDoctrineConnection(), $this->context, $this->legacyService);
    }

    /**
     * @dataProvider vatTestData
     *
     * @param $userId
     * @param $useAddress
     * @param $selectedAddress
     */
    public function testVatRegion($userId, $useAddress, $selectedAddress, $expected)
    {

        $this->legacyService->setSelectedShippingAddressId($selectedAddress);
        $this->context->setUseShippingAddressForVatCountry($useAddress);
        $this->assertEquals(
            $expected,
            $this->userDao->getVatRegion($userId)
        );
    }

    public function vatTestData()
    {

        return [
            ['userid'   => 1, 'useAddress' => false, 'selectedAddress' => '',
             'expected' => UserDaoInterface::VAT_REGION_HOME_COUNTRY],
            ['userid'   => 2, 'useAddress' => false, 'selectedAddress' => '',
             'expected' => UserDaoInterface::VAT_REGION_EU],
            ['userid'   => 3, 'useAddress' => false, 'selectedAddress' => '',
             'expected' => UserDaoInterface::VAT_REGION_OUTSIDE_EU],
            ['userid'   => 2, 'useAddress' => true, 'selectedAddress' => '1',
             'expected' => UserDaoInterface::VAT_REGION_HOME_COUNTRY],
            ['userid'   => 3, 'useAddress' => true, 'selectedAddress' => '2',
             'expected' => UserDaoInterface::VAT_REGION_EU],
            ['userid'   => 1, 'useAddress' => true, 'selectedAddress' => '3',
             'expected' => UserDaoInterface::VAT_REGION_OUTSIDE_EU],
            ['userid'   => 4, 'useAddress' => false, 'selectedAddress' => '',
             'expected' => UserDaoInterface::VAT_REGION_HOME_COUNTRY]
        ];
    }


    public function getFixtureFile()
    {
        return __DIR__ . '/../Fixtures/VatRegionTestFixture.xml';
    }
}