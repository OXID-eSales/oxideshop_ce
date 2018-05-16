<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Exception;

use OxidEsales\Eshop\Core\Exception\ExceptionHandler;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\EshopCommunity\Core\Registry;
use Psr\Log\NullLogger;

class ExceptionHandlerTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    protected $testExceptionMessage = 'TEST_EXCEPTION';

    public function testCallUnExistingMethod()
    {
        $this->setExpectedException( \OxidEsales\Eshop\Core\Exception\SystemComponentException::class);
        $exceptionHandler = oxNew(\OxidEsales\Eshop\Core\Exception\ExceptionHandler::class);
        $exceptionHandler->__NotExistingFunction__();
    }

    /**
     * @dataProvider dataProviderExceptions Provides an OXID eShop style exception and a standard PHP Exception
     *
     * @param $exception
     */
    public function testExceptionHandlerReportsExceptionInDebugMode($exception)
    {
        $debug = true;
        $logFileName = basename(OX_LOG_FILE);
        /** @var ExceptionHandler|\PHPUnit_Framework_MockObject_MockObject $exceptionHandlerMock */
        $exceptionHandlerMock = $this->getMock(
            ExceptionHandler::class,
            ['displayDebugMessage'], // Mock rendering of message in order not to print anything to the console
            [$debug]
        );
        $exceptionHandlerMock->expects($this->once())->method('displayDebugMessage');

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
        /** Test if the exception message is found in the log file */
        $this->assertContains($this->testExceptionMessage, $logFileContent);
    }

    public function dataProviderExceptions()
    {
        return [
            [ new StandardException($this->testExceptionMessage) ],
            [ new \Exception($this->testExceptionMessage) ],
        ];
    }

    /**
     * @covers \OxidEsales\Eshop\Core\Exception\ExceptionHandler::handleDatabaseException()
     */
    public function testHandleDatabaseExceptionDelegatesToHandleUncaughtException() {
        /** @var ExceptionHandler|\PHPUnit_Framework_MockObject_MockObject $exceptionHandlerMock */
        $exceptionHandlerMock = $this->getMock(ExceptionHandler::class, ['handleUncaughtException']);
        $exceptionHandlerMock->expects($this->once())->method('handleUncaughtException');

        $databaseException = oxNew(\OxidEsales\Eshop\Core\Exception\DatabaseException::class, 'message', 0, new \Exception());

        $exceptionHandlerMock->handleDatabaseException($databaseException);
    }

    /**
     * The message is different, if in CLI mode.
     * Real message cannot be tested in UNIT or Integration tests
     *
     * @covers \OxidEsales\Eshop\Core\Exception\ExceptionHandler::handleUncaughtException
     */
    public function testHandleUncaughtExceptionWillDisplayDebugMessageInCliMode() {

        Registry::set('logger', new NullLogger());

        $debug = true;
        /** @var ExceptionHandler|\PHPUnit_Framework_MockObject_MockObject $exceptionHandlerMock */
        $exceptionHandlerMock = $this->getMock(
            ExceptionHandler::class,
            null,
            [$debug]
        );
        ob_start();
        $exceptionHandlerMock->handleUncaughtException(new \Exception($this->testExceptionMessage));
        $displayMessage = ob_get_clean();

        $this->assertContains($this->testExceptionMessage, $displayMessage);
    }

    /**
     * @covers \OxidEsales\Eshop\Core\Exception\ExceptionHandler::handleUncaughtException
     */
    public function testHandleUncaughtExceptionWillDisplayDebugMessageIfDebugIsTrue() {

        Registry::set('logger', new NullLogger());

        $debug = true;
        /** @var ExceptionHandler|\PHPUnit_Framework_MockObject_MockObject $exceptionHandlerMock */
        $exceptionHandlerMock = $this->getMock(
            ExceptionHandler::class,
            ['displayDebugMessage'],
            [$debug]
        );
        $exceptionHandlerMock->expects($this->once())->method('displayDebugMessage');

        $exceptionHandlerMock->handleUncaughtException(new \Exception());
    }
}
