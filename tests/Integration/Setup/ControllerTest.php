<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
class ControllerTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    /**
     * The standard set up method.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->unsetPost();
    }

    /**
     * The standard tear down method.
     */
    protected function tearDown()
    {
        $this->unsetPost();

        parent::tearDown();
    }

    /**
     * Test case that no database settings are supplied.
     */
    public function testDbConnectNoDataSupplied()
    {
        $controller = $this->getTestController();

        // NOTE: OxidTestCase::expectException is not what we need here, try/catch is better suited
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
        $this->setPostDatabase($this->getDatabaseCredentials());

        $controller = $this->getTestController('5.4.53-0ubuntu0.14.04.1');

        // NOTE: OxidTestCase::expectException is not what we need here, try/catch is better suited
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
        $this->setPostDatabase($this->getDatabaseCredentials());

        $controller = $this->getTestController('5.5.53-0ubuntu0.14.04.1', false, true);

        // NOTE: OxidTestCase::expectException is not what we need here, try/catch is better suited
        try {
            $controller->dbConnect();
        } catch (\OxidEsales\EshopCommunity\Setup\Exception\SetupControllerExitException $exception) {
            $view = $controller->getView();

            $this->assertContains('ERROR: Database not available and also cannot be created!', $view->getMessages()[0]);
            $this->assertEquals('400', $view->getNextSetupStep()); //STEP_DB_INFO
            $this->assertNotNull($view->getViewParam('aDB'));

            $this->assertNull($view->getViewParam('blCreated'));
            $this->assertEquals(1, $view->getViewParam('blCreated'));
        }
    }

    /**
     * Test case that mySQL version does not fit requirements.
     */
    public function testDbConnectAllIsWellAndDatabaseAlreadyExists()
    {
        $databaseName = $this->getConfig()->getConfigParam('dbName');
        $this->setPostDatabase($this->getDatabaseCredentials($databaseName));

        $controller = $this->getTestController('5.5.53-0ubuntu0.14.04.1');

        // NOTE: OxidTestCase::expectException is not what we need here, try/catch is better suited
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
        $this->setPostDatabase($this->getDatabaseCredentials());

        $controller = $this->getTestController('5.6.53-0ubuntu0.14.04.1');

        // NOTE: OxidTestCase::expectException is not what we need here, try/catch is better suited
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
        $this->setPostDatabase($this->getDatabaseCredentials($databaseName));

        $controller = $this->getTestController('5.6.53-0ubuntu0.14.04.1');

        // NOTE: OxidTestCase::expectException is not what we need here, try/catch is better suited
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
        $this->setPostDatabase($this->getDatabaseCredentials());

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
        $this->setPostDatabase($this->getDatabaseCredentials($databaseName));

        $controller = $this->getTestController('5.6.53-0ubuntu0.14.04.1', true, false);

        // NOTE: OxidTestCase::expectException is not what we need here, try/catch is better suited
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

        $controller = $this->getTestController('5.6.53-0ubuntu0.14.04.1', false, false, ['aDB' => $databaseCredentials]);

        // NOTE: OxidTestCase::expectException is not what we need here, try/catch is better suited
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

        $controller = $this->getTestController('5.6.53-0ubuntu0.14.04.1', false, false, ['aDB' => $databaseCredentials]);

        // NOTE: OxidTestCase::expectException is not what we need here, try/catch is better suited
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

        $controller = $this->getTestController('5.6.53-0ubuntu0.14.04.1', true, false, ['aDB' => $databaseCredentials]);

        // NOTE: OxidTestCase::expectException is not what we need here, try/catch is better suited
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

        $controller = $this->getTestController('5.6.53-0ubuntu0.14.04.1', true, true, ['aDB' => $databaseCredentials]);

        // NOTE: OxidTestCase::expectException is not what we need here, try/catch is better suited
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
     * @return TestSetupController
     */
    protected function getTestController($databaseVersion = '5.6.53-0ubuntu0.14.04.1', $ignoreWarning = false, $expectDbCreation = false, $sessionData = [])
    {
        if ($ignoreWarning) {
            $_GET['owrec'] = 1;
        }

        $sessionMock = $this->getMock(\OxidEsales\EshopCommunity\Setup\Session::class, ['getSid'], [], '', false);
        if (is_array($sessionData) && !empty($sessionData)) {
            foreach ($sessionData as $key => $value) {
                $sessionMock->setSessionParam($key, $value);
            }
        }

        $languageMock = $this->getMock(\OxidEsales\EshopCommunity\Setup\Language::class, ['getInstance', 'getLanguage'], [], '', false);
        $languageMock->expects($this->any())->method('getLanguage')->will($this->returnValue('en'));

        $databaseMock = $this->getMock(\OxidEsales\EshopCommunity\Setup\Database::class, ['getDatabaseVersion', 'createDb', 'testCreateView']);
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
     * @param array $databaseSettings The settings we want to write into the POST for the database.
     */
    protected function setPostDatabase($databaseSettings)
    {
        $this->setPostData(['aDB' => $databaseSettings]);
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
        if (!$databaseName) {
            $databaseName = time();
        }

        $myConfig = $this->getConfig();
        $parameters['dbHost'] = $myConfig->getConfigParam('dbHost');
        $parameters['dbPort'] = $myConfig->getConfigParam('dbPort') ? $myConfig->getConfigParam('dbPort') : 3306;
        $parameters['dbUser'] = $myConfig->getConfigParam('dbUser');
        $parameters['dbPwd'] = $myConfig->getConfigParam('dbPwd');
        $parameters['dbName'] = $databaseName;

        return $parameters;
    }

    protected function unsetPost()
    {
        $_POST = [];
    }
}
