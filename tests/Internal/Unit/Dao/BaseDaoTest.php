<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 18.07.17
 * Time: 10:16
 */

namespace OxidEsales\EshopCommunity\Tests\Internal\Unit\Dao;

use Doctrine\DBAL\Connection;
use OxidEsales\EshopCommunity\Tests\Internal\Unit\ContextStub;
use OxidEsales\EshopCommunity\Tests\Internal\Unit\OxidLegacyServiceStub;

class BaseDaoTest extends \PHPUnit_Framework_TestCase
{

    /** @var string $tableName */
    private $tableName = 'mytablename';

    /** @var \OxidEsales\EshopCommunity\Internal\Dao\BaseDao $baseDao $articleDao */
    private $baseDao;
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

        $this->baseDao = new \OxidEsales\EshopCommunity\Internal\Dao\BaseDao($this->tableName, $this->connectionMock, $this->contextStub, $this->legacyServiceStub);
    }


    public function testGetViewName()
    {

        $viewName = $this->baseDao->getViewName(false);

        $this->assertEquals('oxv_mytablename_de', $viewName);
    }

    public function testGetTablenameName()
    {


        $viewName = $this->baseDao->getViewName(true);

        $this->assertEquals($this->tableName, $viewName);
    }
}
