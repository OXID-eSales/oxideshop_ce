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
    public function testCallUnExistingMethod()
    {
        $this->expectException(\OxidEsales\Eshop\Core\Exception\SystemComponentException::class);
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
            [ new StandardException() ],
            [ new \Exception() ],
        ];
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

    public function testHandleUncaughtExceptionWritesToLogFile()
    {
        $this->expectException(\Exception::class);
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger
            ->expects($this->atLeastOnce())
            ->method('error');

        Registry::set('logger', $logger);

        $exceptionHandler = oxNew(ExceptionHandler::class);
        $exceptionHandler->handleUncaughtException(new \Exception());
    }
}
