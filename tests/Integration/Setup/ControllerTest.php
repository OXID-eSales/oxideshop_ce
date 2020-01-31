<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Setup;

use OxidEsales\EshopCommunity\Setup\{Controller, Database, Exception\SetupControllerExitException, Language, Session};
use OxidEsales\TestingLibrary\UnitTestCase;

require_once OX_BASE_PATH . 'Setup' . DIRECTORY_SEPARATOR . 'functions.php';

class TestSetupController extends Controller
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
class ControllerTest extends UnitTestCase
{
    /**
     * The standard set up method.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->unsetPost();
    }

    /**
     * The standard tear down method.
     */
    protected function tearDown(): void
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
        } catch (SetupControllerExitException $exception) {
            $view = $controller->getView();

            $this->assertContains('ERROR: Please fill in all needed fields!', $view->getMessages()[0]);
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

        $controller = $this->getTestController(true);

        // NOTE: OxidTestCase::expectException is not what we need here, try/catch is better suited
        try {
            $controller->dbConnect();
        } catch (SetupControllerExitException $exception) {
            $view = $controller->getView();

            $this->assertContains('ERROR: Database not available and also cannot be created!', $view->getMessages()[0]);
            $this->assertEquals('400', $view->getNextSetupStep()); //STEP_DB_INFO
            $this->assertNotNull($view->getViewParam('aDB'));

            $this->assertNull($view->getViewParam('blCreated'));
            $this->assertEquals(1, $view->getViewParam('blCreated'));
        }
    }

    public function testDbConnectAllIsWellAndDatabaseAlreadyExists()
    {
        $databaseName = $this->getConfig()->getConfigParam('dbName');
        $this->setPostDatabase($this->getDatabaseCredentials($databaseName));

        $controller = $this->getTestController();

        // NOTE: OxidTestCase::expectException is not what we need here, try/catch is better suited
        try {
            $controller->dbConnect();
        } catch (SetupControllerExitException $exception) {
            $view = $controller->getView();

            $this->assertContains('ERROR: Seems there is already OXID eShop installed in database', $view->getMessages()[0]);
            $this->assertContains('If you want to overwrite all existing data and install anyway click', $view->getMessages()[1]);
            $this->assertContains('ow=1', $view->getMessages()[1]);
            $this->assertNull($view->getViewParam('blCreated'));
            $this->assertNotNull($view->getViewParam('aDB'));
        }
    }

    /**
     * @param bool   $expectDbCreation Expect Database::createDb call.
     * @param array  $sessionData      Store this data in the session.
     *
     * @return TestSetupController
     */
    protected function getTestController($expectDbCreation = false)
    {
        $sessionMock = $this->getMock(Session::class, ['getSid'], [], '', false);

        $languageMock = $this->getMock(Language::class, ['getInstance', 'getLanguage'], [], '', false);
        $languageMock->expects($this->any())->method('getLanguage')->will($this->returnValue('en'));

        $databaseMock = $this->getMock(Database::class, ['createDb', 'testCreateView']);
        $exception = new \Exception('bail out before we do harm while testing');
        $databaseMock->expects($this->any())->method('testCreateView')->will($this->throwException($exception));

        if ($expectDbCreation) {
            //we do not really want to create a new database while testing
            $databaseMock->expects($this->once())->method('createDb');
        } else {
            $databaseMock->expects($this->never())->method('createDb');
        }

        $controller = oxNew(TestSetupController::class);
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
