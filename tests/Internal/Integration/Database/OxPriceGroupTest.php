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

class OxPriceGroupTest extends AbstractDaoTests
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
     * @dataProvider priceGroupTestData
     *
     * @param $userId
     * @param $expected
     */
    public function testPriceGroup($userId, $expected)
    {

        $this->assertEquals(
            $expected,
            $this->userDao->getPriceGroup($userId)
        );
    }

    public function priceGroupTestData()
    {

        return [
            ['userid'   => "U1", 'exected' => 'b'],
            ['userid'   => "U2", 'exected' => null],
            ['userid'   => "U3", 'exected' => 'b'],
            ['userid'   => "U4", 'exected' => 'b'],
            ['userid'   => "U5", 'exected' => null]
        ];
    }


    public function getFixtureFile()
    {
        return __DIR__ . '/../Fixtures/OxUserGroupTestFixture.xml';
    }
}