<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Exception;

use \Exception;
use OxidEsales\Eshop\Core\Exception\ExceptionHandler;
use OxidEsales\Eshop\Core\Exception\StandardException;
use oxSystemComponentException;
use \oxTestModules;

class ExceptionHandlerTest extends \OxidEsales\TestingLibrary\UnitTestCase
{

    protected $message = 'TEST_EXCEPTION';

    public function testCallUnExistingMethod()
    {
        $this->setExpectedException( \OxidEsales\Eshop\Core\Exception\SystemComponentException::class);
        $exceptionHandler = oxNew(\OxidEsales\Eshop\Core\Exception\ExceptionHandler::class);
        $exceptionHandler->__NotExistingFunction__();
    }

    public function testSetGetFileName()
    {
        $oTestObject = oxNew('oxexceptionhandler');
        $oTestObject->setLogFileName('TEST.log');
        $this->assertEquals('TEST.log', $oTestObject->getLogFileName());
    }

    /**
     * @dataProvider dataProviderExceptions Provides an OXID eShop style exception and a standard PHP Exception
     *
     * @param $exception
     */
    public function testExceptionHandlerReportsExceptionInDebugMode($exception)
    {
        $debug = true;
        $logFileName = 'oxexceptionhandlerTest_NotRenderer.txt';
        /** @var ExceptionHandler|\PHPUnit_Framework_MockObject_MockObject $exceptionHandlerMock */
        $exceptionHandlerMock = $this->getMock(
            ExceptionHandler::class,
            ['displayDebugMessage'], // Mock rendering of message in order not to print anything to the console
            [$debug]
        );
        $exceptionHandlerMock->expects($this->once())->method('displayDebugMessage');
        $exceptionHandlerMock->setLogFileName($logFileName);

        try {
            $exceptionHandlerMock->handleUncaughtException($exception);
        } catch (\Exception $e) {
            // Lets try to delete an possible left over file
            if (file_exists($this->getConfig()->getConfigParam('sShopDir') . 'log/' . $logFileName)) {
                unlink($this->getConfig()->getConfigParam('sShopDir') . 'log/' . $logFileName);
            }
            $this->fail('handleUncaughtException() throws an exception.');
        }
        if (!file_exists($this->getConfig()->getConfigParam('sShopDir') . 'log/' . $logFileName)) {
            $this->fail('No log file written');
        }
        $logFileContent = file_get_contents($this->getConfig()->getConfigParam('sShopDir') . 'log/' . $logFileName);
        unlink($this->getConfig()->getConfigParam('sShopDir') . 'log/' . $logFileName); // delete file first as assert may return out this function
        /** Test if the exception message is found in the lof file */
        $this->assertContains($this->message, $logFileContent);
    }

    public function dataProviderExceptions()
    {
        return [
            [ new StandardException($this->message) ],
            [ new \Exception($this->message) ],
        ];
    }


    public function testSetIDebug()
    {
        $oTestObject = $this->getProxyClass("oxexceptionhandler");
        $oTestObject->setIDebug(2);
        //nothing should happen in unittests
        $this->assertEquals(2, $oTestObject->getNonPublicVar('_iDebug'));
    }

    /**
     * @covers \OxidEsales\Eshop\Core\Exception\ExceptionHandler::handleDatabaseException()
     */
    public function testHandleDatabaseExceptionDelegatesToHandleUncaughtException() {
        /** @var ExceptionHandler $exceptionHandlerMock */
        $exceptionHandlerMock = $this->getMock(ExceptionHandler::class, ['handleUncaughtException']);
        $exceptionHandlerMock->expects($this->once())->method('handleUncaughtException');

        $databaseException = oxNew(\OxidEsales\Eshop\Core\Exception\DatabaseException::class, 'message', 0, new \Exception());

        $exceptionHandlerMock->handleDatabaseException($databaseException);
    }

    /**
     * @dataProvider dataProviderTestHandleUncaughtExceptionDebugStatus
     *
     * @param $debug
     */
    public function testHandleUncaughtExceptionWillAlwaysWriteToLogFile($debug)
    {
        /** @var ExceptionHandler|\PHPUnit_Framework_MockObject_MockObject $exceptionHandlerMock */
        $exceptionHandlerMock = $this->getMock(
            ExceptionHandler::class,
            ['writeExceptionToLog','displayOfflinePage','displayDebugMessage'],
            [$debug]
        );
        $exceptionHandlerMock->expects($this->once())->method('writeExceptionToLog');

        $exceptionHandlerMock->handleUncaughtException(new \Exception());
    }

    /**
     * The message is different, if in CLI mode.
     * Real message cannot be tested in UNIT or Integration tests
     *
     * @dataProvider dataProviderTestHandleUncaughtExceptionDebugStatus
     *
     * @covers \OxidEsales\Eshop\Core\Exception\ExceptionHandler::handleUncaughtException
     */
    public function testHandleUncaughtExceptionWillDisplayShortDebugMessageInCliMode($debug) {
        /** @var ExceptionHandler|\PHPUnit_Framework_MockObject_MockObject $exceptionHandlerMock */
        $exceptionHandlerMock = $this->getMock(
            ExceptionHandler::class,
            ['writeExceptionToLog'],
            [$debug]
        );
        ob_start();
        $exceptionHandlerMock->handleUncaughtException(new \Exception());
        $displayMessage = ob_get_clean();

        $this->assertContains('Uncaught exception. See ' . $exceptionHandlerMock->getLogFileName(), $displayMessage);
    }

    /**
     * @covers \OxidEsales\Eshop\Core\Exception\ExceptionHandler::handleUncaughtException
     */
    public function testHandleUncaughtExceptionWillDisplayDebugMessageIfDebugIsTrue() {
        $debug = true;
        /** @var ExceptionHandler|\PHPUnit_Framework_MockObject_MockObject $exceptionHandlerMock */
        $exceptionHandlerMock = $this->getMock(
            ExceptionHandler::class,
            ['writeExceptionToLog','displayDebugMessage'],
            [$debug]
        );
        $exceptionHandlerMock->expects($this->once())->method('displayDebugMessage');

        $exceptionHandlerMock->handleUncaughtException(new \Exception());
    }

    /**
     * @covers \OxidEsales\Eshop\Core\Exception\ExceptionHandler::handleUncaughtException
     */
    public function testHandleUncaughtExceptionWillDisplayOfflinePageIfDebugIsFalse() {
        $debug = false;
        /** @var ExceptionHandler|\PHPUnit_Framework_MockObject_MockObject $exceptionHandlerMock */
        $exceptionHandlerMock = $this->getMock(
            ExceptionHandler::class,
            ['writeExceptionToLog','displayOfflinePage'],
            [$debug]
        );
        $exceptionHandlerMock->expects($this->once())->method('displayOfflinePage');

        $exceptionHandlerMock->handleUncaughtException(new \Exception());
    }

    /**
     * This test is incomplete as constant OXID_PHP_UNIT is taken to define if exitApplication should be called or not.
     * If OXID_PHP_UNIT was a variable, the test could run.
     *
     * @covers \OxidEsales\Eshop\Core\Exception\ExceptionHandler::handleUncaughtException
     *
     * @dataProvider dataProviderTestHandleUncaughtExceptionDebugStatus
     *
     * @param $debug ExceptionHandler constructor parameter $debug i.e. debug level
     */
    public function testHandleUncaughtExceptionWillExitApplication($debug)
    {
        $this->markTestIncomplete('If OXID_PHP_UNIT was a variable, this test could run');

        /** @var ExceptionHandler|\PHPUnit_Framework_MockObject_MockObject $exceptionHandlerMock */
        $exceptionHandlerMock = $this->getMock(
            ExceptionHandler::class,
            ['writeExceptionToLog','displayOfflinePage','displayDebugMessage', 'exitApplication'],
            [$debug]
        );
        $exceptionHandlerMock->expects($this->once())->method('exitApplication');

        $OXID_PHP_UNIT = false;

        $exceptionHandlerMock->handleUncaughtException(new \Exception());
        $OXID_PHP_UNIT = true;
    }

    /**
     * @covers \OxidEsales\Eshop\Core\Exception\ExceptionHandler::handleUncaughtException
     */
    public function testHandleUncaughtExceptionAcceptsThrowables() {
        if (version_compare(PHP_VERSION, '7.0') < 0) {
            $this->markTestSkipped('php needs to >= 7.0');
        }

        $debug = false;
        /** @var ExceptionHandler|\PHPUnit_Framework_MockObject_MockObject $exceptionHandlerMock */
        $exceptionHandlerMock = $this->getMock(
            ExceptionHandler::class,
            ['writeExceptionToLog','displayOfflinePage'],
            [$debug]
        );
        $exceptionHandlerMock->expects($this->once())->method('displayOfflinePage');
        $exceptionHandlerMock->expects($this->once())->method('writeExceptionToLog');

        $exceptionHandlerMock->handleUncaughtException(new \Error());
    }

    /**
     * Data provider for testHandleUncaughtExceptionWillExitApplication
     *
     * @return array
     */
    public function dataProviderTestHandleUncaughtExceptionDebugStatus ()
    {
        return [
            ['debug' => true],
            ['debug' => false],
        ];
    }

    /**
     * @dataProvider dataProviderTestSetLogFileNameSetsFileRelativeToOxLogFileDirectory
     *
     * @covers \OxidEsales\Eshop\Core\Exception\ExceptionHandler::setLogFileName()
     */
    public function testSetLogFileNameSetsFileRelativeToOxLogFileDirectory($filePath)
    {
        $logDir = dirname(OX_LOG_FILE);
        $expectedFilePath = $logDir . DIRECTORY_SEPARATOR . basename($filePath);
        /** @var ExceptionHandler|\PHPUnit_Framework_MockObject_MockObject $exceptionHandlerMock */
        $exceptionHandlerMock = $this->getMock(
            ExceptionHandler::class,
            ['displayOfflinePage']
        );

        $exceptionHandlerMock->setLogFileName($filePath);

        $exceptionHandlerMock->handleUncaughtException(new \Exception('message', 1));
        if (!file_exists($expectedFilePath) || is_dir($expectedFilePath)) {
            $testResult = false;
        } else {
            unlink($expectedFilePath);
            $testResult = true;
        }

        $this->assertTrue($testResult, 'setLogFileName sets file relative to OX_LOG_FILE directory');
    }

    /**
     * Dataprovider for testSetLogFileNameSetsFileRelativeToOxLogFileDirectory
     *
     * @return array
     */
    public function dataProviderTestSetLogFileNameSetsFileRelativeToOxLogFileDirectory()
    {
        return [
            ['filepath' => 'my.log'],
            ['filepath' =>'/some/place/on/the/file/system/my.log'],
        ];
    }

    /**
     * @covers \OxidEsales\Eshop\Core\Exception\ExceptionHandler::getLogFileName()
     */
    public function testGetLogFileNameReturnsBaseNameOfLogeFile()
    {
        /** @var ExceptionHandler $exceptionHandlerMock */
        $exceptionHandler = oxNew(ExceptionHandler::class);

        $actualLogFileName = $exceptionHandler->getLogFileName();
        $expectedLogFileName = basename($actualLogFileName);

        $this->assertEquals($expectedLogFileName, $actualLogFileName, 'getLogFileName returns basename of logFile');
    }

    /**
     * @covers \OxidEsales\Eshop\Core\Exception\ExceptionHandler::writeExceptionToLog()
     */
    public function testWriteExceptionToLogCallsExceptionFormatter()
    {
        $fileName = dirname(OX_LOG_FILE) . DIRECTORY_SEPARATOR . __FUNCTION__ . '.log';
        /** @var ExceptionHandler|\PHPUnit_Framework_MockObject_MockObject $exceptionHandlerMock */
        $exceptionHandlerMock = $this->getMock(
            ExceptionHandler::class,
            ['getFormattedException', 'displayOfflinePage']
        );
        $exceptionHandlerMock->expects($this->once())->method('getFormattedException');

        $exceptionHandlerMock->setLogFileName($fileName);

        $exceptionHandlerMock->handleUncaughtException(new \Exception('message', 1));
        if (file_exists($fileName) && !is_dir($fileName)) {
            unlink($fileName);
        }
    }

    /**
     * @covers \OxidEsales\Eshop\Core\Exception\ExceptionHandler::writeExceptionToLog()
     */
    public function testWriteExceptionToLogAcceptsThrowables()
    {
        if (version_compare(PHP_VERSION, '7.0') < 0) {
            $this->markTestSkipped('php needs to >= 7.0');
        }

        $fileName = dirname(OX_LOG_FILE) . DIRECTORY_SEPARATOR . __FUNCTION__ . '.log';

        /** @var ExceptionHandler|\PHPUnit_Framework_MockObject_MockObject $exceptionHandlerMock */
        $exceptionHandlerMock = $this->getMock(
            ExceptionHandler::class,
            ['getFormattedException']
        );
        $exceptionHandlerMock->setLogFileName($fileName);

        $exceptionHandlerMock->expects($this->once())->method('getFormattedException');
        $exceptionHandlerMock->writeExceptionToLog(new \Error('message', 1));

        if (file_exists($fileName) && !is_dir($fileName)) {
            unlink($fileName);
        } else {
            $this->fail('test file does not exist or is directory: ' . $fileName);
        }
    }

    /**
     * @covers \OxidEsales\Eshop\Core\Exception\ExceptionHandler::getFormattedException()
     */
    public function testGetFormattedException()
    {
        $logContent = null;
        $fileName = dirname(OX_LOG_FILE) . DIRECTORY_SEPARATOR . __FUNCTION__ . '.log';
        $exceptionHandlerMock = $this->getMock(
            ExceptionHandler::class,
            ['displayOfflinePage']
        );
        $exceptionHandlerMock->setLogFileName($fileName);

        $handeledException = new \Exception('message', 1);
        $exceptionHandlerMock->handleUncaughtException($handeledException);

        if (file_exists($fileName) && !is_dir($fileName)) {
            $logContent = file_get_contents($fileName);
            unlink($fileName);
        } else {
            $this->fail('test file does not exist or is directory: ' . $fileName);
        }
        $expectedLogContents = [
            'error type' => 'exception',
            'exception type' => 'type',
            'exception code' => 'code ',
            'file where the exception has been thrown' => 'file ',
            'line  where the exception has been thrown' => 'line ',
            'exception message' => 'message ',
        ];

        foreach ($expectedLogContents as $expectedField => $expectedValue) {
            $this->assertContains($expectedValue, $logContent, 'Log formatter puts ' . $expectedField);
        }
    }

    /**
     * @covers \OxidEsales\Eshop\Core\Exception\ExceptionHandler::getFormattedException()
     */
    public function testGetFormattedExceptionAcceptsThrowables()
    {
        if (version_compare(PHP_VERSION, '7.0') < 0) {
            $this->markTestSkipped('php needs to >= 7.0');
        }
        $exceptionHandlerMock = oxNew(ExceptionHandler::class);
        $error = new \Error('message', 1);
        $exceptionHandlerMock->getFormattedException($error);
    }
}
