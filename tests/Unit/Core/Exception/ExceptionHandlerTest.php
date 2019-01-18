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
use Psr\Log\NullLogger;

class ExceptionHandlerTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    protected $testExceptionMessage = 'TEST_EXCEPTION';

    public function testCallUnExistingMethod()
    {
        $this->expectException( \OxidEsales\Eshop\Core\Exception\SystemComponentException::class);
        $exceptionHandler = oxNew(\OxidEsales\Eshop\Core\Exception\ExceptionHandler::class);
        $exceptionHandler->__NotExistingFunction__();
    }

    /**
     * @dataProvider dataProviderExceptions Provides an OXID eShop style exception and a standard PHP Exception
     *
     * @param $exception
     */
    public function testExceptionHandlerLogExceptionInDebugMode($exception)
    {
        $logger = $this->getMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('error');

        Registry::set('logger', $logger);

        $debug = true;
        $exceptionHandler = oxNew(ExceptionHandler::class, $debug);

        ob_start();
        $exceptionHandler->handleUncaughtException($exception);
        $displayMessage = ob_get_clean();

        $this->assertContains($this->testExceptionMessage, $displayMessage);
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
        /** @var ExceptionHandler|\PHPUnit\Framework\MockObject\MockObject $exceptionHandlerMock */
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

        $debug = false;
        /** @var ExceptionHandler|\PHPUnit\Framework\MockObject\MockObject $exceptionHandlerMock */
        $exceptionHandlerMock = $this->getMock(
            ExceptionHandler::class,
            ['writeExceptionToLog'],
            [$debug]
        );

        $exceptionHandlerMock->expects($this->any())->method('writeExceptionToLog')->willReturn(true);
        ob_start();
        $exceptionHandlerMock->handleUncaughtException(new \Exception());
        $displayMessage = ob_get_clean();

        $this->assertContains('Uncaught exception. See error log for more information.', $displayMessage);
    }

    /**
     * @covers \OxidEsales\Eshop\Core\Exception\ExceptionHandler::handleUncaughtException
     */
    public function testHandleUncaughtExceptionWillDisplayDebugMessageIfDebugIsTrue() {

        Registry::set('logger', new NullLogger());

        $debug = true;
        /** @var ExceptionHandler|\PHPUnit\Framework\MockObject\MockObject $exceptionHandlerMock */
        $exceptionHandlerMock = $this->getMock(
            ExceptionHandler::class,
            ['displayDebugMessage'],
            [$debug]
        );

        ob_start();
        $exceptionHandlerMock->handleUncaughtException(new \Exception($this->testExceptionMessage));
        $displayMessage = ob_get_clean();

        $this->assertContains($this->testExceptionMessage, $displayMessage);
    }
}
