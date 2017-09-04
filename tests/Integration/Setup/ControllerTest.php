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
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Setup;


use OxidEsales\EshopCommunity\Setup\Controller;

require_once OX_BASE_PATH . 'Setup' . DIRECTORY_SEPARATOR . 'functions.php';

class TestSetupController extends \OxidEsales\EshopCommunity\Setup\Controller
{
    public function setInstance($key, $object)
    {
        $storageKey = $this->getClassKey($key);
        static::$_aInstances[$storageKey] = $object;
    }

    public function getClassKey($instanceName)
    {
        return parent::getClass($instanceName);
    }
}

/**
 * SetupCoreTest tests
 */
class ControllerTest extends \OxidTestCase
{
    /**
     * Fixture set up.
     */
    protected function setUp()
    {
        parent::setUp();
        $_POST = [];
    }

    /**
     * Fixture tear down.
     */
    protected function tearDown()
    {
        $_POST = [];
        parent::tearDown();
    }

    /**
     * Test case that no database settings are supplied.
     */
    public function testDbConnectNoDataSupplied()
    {
        $controller = $this->getTestController();
        //NOTE: OxidTestCase::setExpectedException is not what we need here, try/catch is better suited
        try {
            $controller->dbConnect();
        } catch (\OxidEsales\EshopCommunity\Setup\Exception\SetupControllerExitException $exception) {
            $view = $controller->getView();
            $this->assertContains('ERROR: Please fill in all needed fields!', $view->getMessages()[0]);
            $this->assertEquals('400', $view->getNextSetupStep()); //STEP_DB_INFO
            $this->assertNull($view->getViewParam('aDB'));
        }
    }

    /**
     * Test case that mySQL version does not fit requirements.
     */
    public function testDbConnectMySQLVersionDoesNotFitRequirements()
    {
        $this->setPostData(array('aDB' => $this->getDatabaseCredentials()));
        $controller = $this->getTestController('5.4.53-0ubuntu0.14.04.1');

        //NOTE: OxidTestCase::setExpectedException is not what we need here, try/catch is better suited
        try {
            $controller->dbConnect();
        } catch (\OxidEsales\EshopCommunity\Setup\Exception\SetupControllerExitException $exception) {
            $view = $controller->getView();
            $this->assertContains('The installed MySQL version does not fit system requirements!', $view->getMessages()[0]);
            $this->assertEquals('400', $view->getNextSetupStep()); //STEP_DB_INFO
            $this->assertNull($view->getViewParam('aDB'));
        }
    }

    /**
     * Test case that all is well but database does not yet exist.
     * We get an Exception from Database::opeDatabase that is caught in Controller::dbConnect and
     * then database is created in Controller::dbConnect catch block.
     */
    public function testDbConnectAllIsWellButDatabaseNotYetCreated()
    {
        $this->setPostData(array('aDB' => $this->getDatabaseCredentials()));
        $controller = $this->getTestController('5.5.53-0ubuntu0.14.04.1', false, true);

        //NOTE: OxidTestCase::setExpectedException is not what we need here, try/catch is better suited
        try {
            $controller->dbConnect();
        } catch (\OxidEsales\EshopCommunity\Setup\Exception\SetupControllerExitException $exception) {
            $view = $controller->getView();
            $this->assertContains('ERROR: Database not available and also cannot be created!', $view->getMessages()[0]);
            $this->assertNull($view->getViewParam('blCreated'));
            $this->assertEquals('400', $view->getNextSetupStep()); //STEP_DB_INFO
            $this->assertEquals(1, $view->getViewParam('blCreated'));
            $this->assertNotNull($view->getViewParam('aDB'));
        }
    }

    /**
     * Test case that mySQL version does not fit requirements.
     */
    public function testDbConnectAllIsWellAndDatabaseAlreadyExists()
    {
        $databaseName = $this->getConfig()->getConfigParam('dbName');
        $this->setPostData(array('aDB' => $this->getDatabaseCredentials($databaseName)));
        $controller = $this->getTestController('5.5.53-0ubuntu0.14.04.1');

        //NOTE: OxidTestCase::setExpectedException is not what we need here, try/catch is better suited
        try {
            $controller->dbConnect();
        } catch (\OxidEsales\EshopCommunity\Setup\Exception\SetupControllerExitException $exception) {
            $view = $controller->getView();
            $this->assertContains('ERROR: Seems there is already OXID eShop installed in database', $view->getMessages()[0]);
            $this->assertContains('If you want to overwrite all existing data and install anyway click', $view->getMessages()[1]);
            $this->assertContains('ow=1', $view->getMessages()[1]);
            $this->assertNull($view->getViewParam('blCreated'));
            $this->assertNotNull($view->getViewParam('aDB'));
        }
    }

    /**
     * Test case that mySQL version does not fit requirements.
     * Case database does not yet exist.
     */
    public function testDbConnectMySQLVersionDoesNotFitRecommendations()
    {
        $this->setPostData(array('aDB' => $this->getDatabaseCredentials()));
        $controller = $this->getTestController('5.6.53-0ubuntu0.14.04.1');

        //NOTE: OxidTestCase::setExpectedException is not what we need here, try/catch is better suited
        try {
            $controller->dbConnect();
        } catch (\OxidEsales\EshopCommunity\Setup\Exception\SetupControllerExitException $exception) {
            $view = $controller->getView();
            $this->assertContains('WARNING: A bug in MySQL 5.6 may lead to problems in OXID eShop Enterprise Edition', $view->getMessages()[0]);
            $this->assertContains('If you want to install anyway click', $view->getMessages()[1]);
            $this->assertContains('owrec=1', $view->getMessages()[1]);
            $this->assertNull($view->getViewParam('aDB')); //we never got that far
        }
    }

    /**
     * Test case that mySQL version does not fit requirements.
     * Case database does already exist.
     */
    public function testDbConnectMySQLVersionDoesNotFitRecommendationsDatabaseExists()
    {
        $databaseName = $this->getConfig()->getConfigParam('dbName');
        $this->setPostData(array('aDB' => $this->getDatabaseCredentials($databaseName)));
        $controller = $this->getTestController('5.6.53-0ubuntu0.14.04.1');

        //NOTE: OxidTestCase::setExpectedException is not what we need here, try/catch is better suited
        try {
            $controller->dbConnect();
        } catch (\OxidEsales\EshopCommunity\Setup\Exception\SetupControllerExitException $exception) {
            $view = $controller->getView();
            $this->assertContains('WARNING: A bug in MySQL 5.6 may lead to problems in OXID eShop Enterprise Edition', $view->getMessages()[0]);
            $this->assertContains('ERROR: Seems there is already OXID eShop installed in database', $view->getMessages()[1]);
            $this->assertContains('If you want to overwrite all existing data and install anyway click', $view->getMessages()[2]);
            $this->assertContains('owrec=1', $view->getMessages()[2]);
            $this->assertContains('ow=1', $view->getMessages()[2]);
            $this->assertNull($view->getViewParam('aDB'));  //we never got that far
        }
    }

    /**
     * Test case that mySQL version does not fit requirements and user already clicked 'install anyway' checkbox.
     * Case database does not yet exist.
     */
    public function testDbConnectMySQLVersionDoesNotFitRecommendationsIgnoreConfirmed()
    {
        $this->setPostData(array('aDB' => $this->getDatabaseCredentials()));
        $controller = $this->getTestController('5.6.53-0ubuntu0.14.04.1', true, true);
        $controller->dbConnect();

        $view = $controller->getView();
        $this->assertEmpty($view->getMessages());
        $this->assertNotNull($view->getViewParam('aDB'));
    }

    /**
     * Test case that mySQL version does not fit requirements and user already clicked 'install anyway' checkbox.
     * Case database does already exist.
     */
    public function testDbConnectMySQLVersionDoesNotFitRecommendationsDatabaseExistsIgnoreConfirmed()
    {
        $databaseName = $this->getConfig()->getConfigParam('dbName');
        $this->setPostData(array('aDB' => $this->getDatabaseCredentials($databaseName)));
        $controller = $this->getTestController('5.6.53-0ubuntu0.14.04.1', true, false);

        //NOTE: OxidTestCase::setExpectedException is not what we need here, try/catch is better suited
        try {
            $controller->dbConnect();
        } catch (\OxidEsales\EshopCommunity\Setup\Exception\SetupControllerExitException $exception) {
            $view = $controller->getView();
            $this->assertContains('ERROR: Seems there is already OXID eShop installed in database', $view->getMessages()[0]);
            $this->assertContains('If you want to overwrite all existing data and install anyway', $view->getMessages()[1]);
            $this->assertNotContains('owrec=1', $view->getMessages()[1]);
            $this->assertContains('ow=1', $view->getMessages()[1]);
            $this->assertNotNull($view->getViewParam('aDB'));
        }
    }

    /**
     * Test Controller::dbCreate() (which only creates/overwrites the needed tables)
     * in case MySQl version does not match the recommendations.
     */
    public function testDbCreateMySQLVersionDoesNotFitRecommendationsDbDoesAlreadyExist()
    {
        $databaseName = $this->getConfig()->getConfigParam('dbName');
        $databaseCredentials = $this->getDatabaseCredentials($databaseName);
        $controller = $this->getTestController('5.6.53-0ubuntu0.14.04.1', false, false, array('aDB' => $databaseCredentials));

        //NOTE: OxidTestCase::setExpectedException is not what we need here, try/catch is better suited
        try {
            $controller->dbCreate();
        } catch (\OxidEsales\EshopCommunity\Setup\Exception\SetupControllerExitException $exception) {
            $view = $controller->getView();
            $this->assertContains('WARNING: A bug in MySQL 5.6 may lead to problems in OXID eShop Enterprise Edition', $view->getMessages()[0]);
            $this->assertContains('ERROR: Seems there is already OXID eShop installed in database', $view->getMessages()[1]);
            $this->assertContains('If you want to overwrite all existing data and install anyway click', $view->getMessages()[2]);
            $this->assertContains('owrec=1', $view->getMessages()[2]);
            $this->assertContains('ow=1', $view->getMessages()[2]);
        }
    }

    /**
     * In some cases we might come out of Controller::dbConnect with a not yet existing database.
     * Means Controller::dbCreate() (which only creates/overwrites the needed tables) needs ot handle
     * the case that the database does not yet exist.
     * NOTE: this will only happen if the MySQL version does not fit recommendations.
     */
    public function testDbCreateMySQLVersionDoesNotFitRecommendationsDbDoesNotExist()
    {
        $databaseCredentials = $this->getDatabaseCredentials();
        $controller = $this->getTestController('5.6.53-0ubuntu0.14.04.1', false, false, array('aDB' => $databaseCredentials));

        //NOTE: OxidTestCase::setExpectedException is not what we need here, try/catch is better suited
        try {
            $controller->dbCreate();
        } catch (\OxidEsales\EshopCommunity\Setup\Exception\SetupControllerExitException $exception) {
            $view = $controller->getView();
            $this->assertContains('WARNING: A bug in MySQL 5.6 may lead to problems in OXID eShop Enterprise Edition', $view->getMessages()[0]);
            $this->assertContains('If you want to install anyway click', $view->getMessages()[1]);
            $this->assertContains('owrec=1', $view->getMessages()[1]);
        }
    }

    /**
     * Test Controller::dbCreate() (which only creates/overwrites the needed tables)
     * in case MySQl version does not match the recommendations. User clicked ignore checkbox.
     */
    public function testDbCreateMySQLVersionDoesNotFitRecommendationsDbDoesAlreadyExistIgnoreConfirmed()
    {
        $databaseName = $this->getConfig()->getConfigParam('dbName');
        $databaseCredentials = $this->getDatabaseCredentials($databaseName);
        $controller = $this->getTestController('5.6.53-0ubuntu0.14.04.1', true, false, array('aDB' => $databaseCredentials));

        //NOTE: OxidTestCase::setExpectedException is not what we need here, try/catch is better suited
        try {
            $controller->dbCreate();
        } catch (\OxidEsales\EshopCommunity\Setup\Exception\SetupControllerExitException $exception) {
            $view = $controller->getView();
            $this->assertContains('bail out before we do harm while testing', $view->getMessages()[0]);
        }
    }

    /**
     * Test Controller::dbCreate() (which only creates/overwrites the needed tables)
     * in case MySQl version does not match the recommendations. User clicked ignore checkbox.
     */
    public function testDbCreateMySQLVersionDoesNotFitRecommendationsDbDoesNotExistIgnoreConfirmed()
    {
        $databaseCredentials = $this->getDatabaseCredentials();
        $controller = $this->getTestController('5.6.53-0ubuntu0.14.04.1', true, true, array('aDB' => $databaseCredentials));

        //NOTE: OxidTestCase::setExpectedException is not what we need here, try/catch is better suited
        try {
            $controller->dbCreate();
        } catch (\OxidEsales\EshopCommunity\Setup\Exception\SetupControllerExitException $exception) {
            $view = $controller->getView();
            $this->assertContains('bail out before we do harm while testing', $view->getMessages()[0]);
        }
    }

    /**
     * Test helper to get prepared test controller object.
     *
     * @param string $databaseVersion
     * @param bool   $ignoreWarning    User clicked ignore checkbox.
     * @param bool   $expectDbCreation Expect Database::createDb call.
     * @param array  $sessionData      Store this data in the session.
     *
     * @return object
     */
    protected function getTestController($databaseVersion = '5.6.53-0ubuntu0.14.04.1', $ignoreWarning = false, $expectDbCreation = false, $sessionData = array())
    {
        $sessionMock = $this->getMock(\OxidEsales\EshopCommunity\Setup\Session::class, array('getSid'), array(), '', false);
        if ($ignoreWarning) {
            $_GET['owrec'] = 1;
        }
        if (is_array($sessionData) && !empty($sessionData)) {
            foreach( $sessionData as $key => $value) {
                $sessionMock->setSessionParam($key, $value);
            }
        }

        $languageMock = $this->getMock(\OxidEsales\EshopCommunity\Setup\Language::class, array('getInstance', 'getLanguage'), array(), '', false);
        $languageMock->expects($this->any())->method('getLanguage')->will($this->returnValue('en'));

        $databaseMock = $this->getMock(\OxidEsales\EshopCommunity\Setup\Database::class, array('getDatabaseVersion', 'createDb', 'testCreateView'));
        $databaseMock->expects($this->any())->method('getDatabaseVersion')->will($this->returnValue($databaseVersion));
        $exception = new \Exception('bail out before we do harm while testing');
        $databaseMock->expects($this->any())->method('testCreateView')->will($this->throwException($exception));

        if ($expectDbCreation) {
            //we do not really want to create a new database while testing
            $databaseMock->expects($this->once())->method('createDb');
        } else {
            $databaseMock->expects($this->never())->method('createDb');
        }

        $controller = oxNew(\OxidEsales\EshopCommunity\Tests\Integration\Setup\TestSetupController::class);
        $controller->setInstance('Session', $sessionMock);
        $controller->setInstance('Language', $languageMock);
        $controller->setInstance('Database', $databaseMock);

        $this->assertEmpty($controller->getView()->getMessages());

        return $controller;
    }

    /**
     * Test helper.
     *
     * @param array $parameters
     */
    protected function setPostData($parameters)
    {
        foreach ($parameters as $key => $value) {
            $_POST[$key] = $value;
        }
    }

    /**
     * @param string $databaseName
     *
     * @return array
     */
    protected function getDatabaseCredentials($databaseName = '')
    {
        $databaseName = $databaseName ?: time();

        $myConfig = $this->getConfig();
        $parameters['dbHost'] = $myConfig->getConfigParam('dbHost');
        $parameters['dbPort'] = $myConfig->getConfigParam('dbPort') ? $myConfig->getConfigParam('dbPort') : 3306;
        $parameters['dbUser'] = $myConfig->getConfigParam('dbUser');
        $parameters['dbPwd'] = $myConfig->getConfigParam('dbPwd');
        $parameters['dbName'] = $databaseName;

        return $parameters;
    }

}
