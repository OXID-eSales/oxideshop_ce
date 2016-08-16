<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version       OXID eShop CE
 */

namespace Integration\Core;

use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Database;
use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * Class DatabaseTest
 * 
 * @group database-adapter
 * @covers OxidEsales\Eshop\Core\Database
 */
class DatabaseTest extends UnitTestCase
{
    /**
     * Test case for oxDb::reportDatabaseConnectionException()
     */
    public function testNotifyConnectionErrors()
    {
        // TODO Put this in PHPDoc block again: @expectedException DatabaseConnectionException

        $this->markTestSkipped('Fix this test');

        $oDbInst = $this->getMock("oxDb", array("errorMsg", "errorNo"));
        $oDbInst->expects($this->never())->method('errorMsg');
        $oDbInst->expects($this->never())->method('errorNo');

        $oConfigFile = $this->getBlankConfigFile();
        $oConfigFile->sAdminEmail = "adminemail";
        $oConfigFile->dbUser = "dbuser";

        $exception = oxNew('Exception');

        $oDb = $this->getMock('Unit\Core\oxDbPublicized', array("getConfig", "sendMail"));
        $oDb->setConfig($oConfigFile);
        $oDb->expects($this->once())->method('sendMail')->with($this->equalTo('adminemail'), $this->equalTo('Offline warning!'));

        $this->setExpectedException('OxidEsales\Eshop\Core\exception\DatabaseException');
        $oDb->notifyConnectionErrors($exception);
    }

    /**
     * Test case for oxDb::onConnectionError()
     *
     * TODO Move this test to integration tests
     */
    public function testOnConnectionError()
    {
        $this->markTestSkipped('Fix this test');

        $exception = oxNew('OxidEsales\Eshop\Core\exception\DatabaseConnectionException', 'THE CONNECTION ERROR MESSAGE!', 42, new \Exception());

        $oDb = $this->getMock('Unit\Core\oxDbPublicized', array('reportDatabaseConnectionException', 'redirectToMaintenancePageWithoutDbConnection'));
        $oDb->expects($this->once())->method('reportDatabaseConnectionException')->with($this->equalTo($exception));
        $oDb->expects($this->once())->method('redirectToMaintenancePageWithoutDbConnection');

        $oDb->onConnectionError($exception);
    }

    /**
     * Helper methods
     */

    /**
     * @return ConfigFile
     */
    protected function getBlankConfigFile()
    {
        return new ConfigFile($this->createFile('config.inc.php', '<?php '));
    }

    /**
     * @param $methodName
     * @param $params
     */
    protected static function callMethod($methodName, array $params = array())
    {
        $class = new Database();
        $reflectedMethod = self::getReflectedMethod($methodName);

        return $reflectedMethod->invokeArgs($class, $params);
    }
}
