<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Exception;

use OxidEsales\Eshop\Core\Exception\ExceptionHandler;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Registry;
use Psr\Log\LoggerInterface;

class ExceptionHandlerTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    protected $message = 'TEST_EXCEPTION';

    public function testCallUnExistingMethod()
    {
        $this->expectException(\OxidEsales\Eshop\Core\Exception\SystemComponentException::class);
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
        $this->expectException(get_class($exception));

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger
            ->expects($this->atLeastOnce())
            ->method('error')
            ->with($exception->getMessage(), [$exception]);

        Registry::set('logger', $logger);

        $debug = true;
        $exceptionHandler = oxNew(ExceptionHandler::class, $debug);
        $exceptionHandler->handleUncaughtException($exception);
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
    public function testHandleDatabaseExceptionDelegatesToHandleUncaughtException()
    {
        /** @var ExceptionHandler|\PHPUnit\Framework\MockObject\MockObject $exceptionHandlerMock */
        $exceptionHandlerMock = $this->getMock(ExceptionHandler::class, ['handleUncaughtException']);
        $exceptionHandlerMock->expects($this->once())->method('handleUncaughtException');

        $databaseException = oxNew(\OxidEsales\Eshop\Core\Exception\DatabaseException::class, 'message', 0, new \Exception());

        $exceptionHandlerMock->handleDatabaseException($databaseException);
    }

    /**
     * @expectedException \Exception
     *
     * @dataProvider dataProviderTestHandleUncaughtExceptionDebugStatus
     *
     * @param $debug
     */
    public function testHandleUncaughtExceptionWillAlwaysWriteToLogFile($debug)
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger
            ->expects($this->atLeastOnce())
            ->method('error');

        Registry::set('logger', $logger);

        $exceptionHandler = oxNew(ExceptionHandler::class, $debug);
        $exceptionHandler->handleUncaughtException(new \Exception());
    }

    /**
     * Data provider for testHandleUncaughtExceptionWillExitApplication
     *
     * @return array
     */
    public function dataProviderTestHandleUncaughtExceptionDebugStatus()
    {
        return [
            ['debug' => true],
            ['debug' => false],
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
}
