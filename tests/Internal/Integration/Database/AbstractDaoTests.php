<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 02.08.17
 * Time: 10:14
 */
namespace OxidEsales\EshopCommunity\Tests\Internal\Integration\Database;

use Doctrine\DBAL\Connection;

abstract class AbstractDaoTests extends \PHPUnit_Extensions_Database_TestCase
{

    // only instantiate pdo once for test clean-up/fixture load
    static private $databaseHandler = null;

    /** @var  Connection */
    protected $doctrineConnection;

    /** @var \PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection $connection */
    protected $connection = null;

    /** @var \OxidEsales\EshopCommunity\Tests\Internal\Unit\ContextStub $contextStub */
    protected $contextStub;

    /** @var \OxidEsales\EshopCommunity\Tests\Internal\Unit\OxidLegacyServiceStub $legacyServiceStub */
    protected $legacyServiceStub;

    public function setUp()
    {

        parent::setUp();

        $this->contextStub = new \OxidEsales\EshopCommunity\Tests\Internal\Unit\ContextStub();
        $this->legacyServiceStub = new \OxidEsales\EshopCommunity\Tests\Internal\Unit\OxidLegacyServiceStub();
    }

    /**
     * @return Connection
     */
    final public function getDoctrineConnection()
    {

        if ($this->doctrineConnection === null) {
            $config = new \Doctrine\DBAL\Configuration();

            $connectionParams = array(
                'dbname'   => $GLOBALS['DB_DBNAME'],
                'user'     => $GLOBALS['DB_USER'],
                'password' => $GLOBALS['DB_PASSWD'],
                'host'     => $GLOBALS['DB_HOST'],
                'port'     => 3306,
                'charset'  => 'utf8',
                'driver'   => $GLOBALS['DB_DRIVER'],
            );
            $this->doctrineConnection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        }

        return $this->doctrineConnection;
    }

    /**
     * @return \PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
     */
    final public function getConnection()
    {
        if ($this->connection === null) {
            $this->connection = $this->createDefaultDBConnection($this->getDoctrineConnection()->getWrappedConnection(), $GLOBALS['DB_DBNAME']);
        }

        return $this->connection;
    }

    /**
     * @return \PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet($this->getFixtureFile());
    }

    abstract public function getFixtureFile();

}
?>
