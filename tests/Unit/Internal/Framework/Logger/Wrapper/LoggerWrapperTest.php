<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Logger\Wrapper;

use OxidEsales\EshopCommunity\Internal\Framework\Logger\Wrapper\LoggerWrapper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class LoggerWrapperTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Logger\Wrapper
 */
final class LoggerWrapperTest extends TestCase
{
    #[DataProvider('dataProviderPsrInterfaceMethods')]
    public function testAllInterfaceMethodsExceptLogAreHandled(string $methodNameToTest): void
    {
        $messageToLog = "The message is {myMessage}";
        $contextToLog = ['myMessage' => 'Hello World!'];
        $loggerMock = $this->getLoggerMock();
        $loggerMock->expects($this->once())
            ->method($methodNameToTest)
            ->with(
                $this->equalTo($messageToLog),
                $this->equalTo($contextToLog)
            );

        $loggerServiceWrapper = new LoggerWrapper($loggerMock);
        $loggerServiceWrapper->$methodNameToTest($messageToLog, $contextToLog);
    }

    public static function dataProviderPsrInterfaceMethods(): array
    {
        return [
            ['emergency'],
            ['alert'],
            ['critical'],
            ['error'],
            ['warning'],
            ['notice'],
            ['info'],
            ['debug'],
            ['log'],
        ];
    }

    public function testLog(): void
    {
        $messageToLog = "The message is {myMessage}";
        $contextToLog = ['myMessage' => 'Hello World!'];
        $levelToLog = 'aLevelToLog';
        $loggerMock = $this->getLoggerMock();
        $loggerMock->expects($this->once())
            ->method('log')
            ->with(
                $this->equalTo($levelToLog),
                $this->equalTo($messageToLog),
                $this->equalTo($contextToLog)
            );

        $loggerServiceWrapper = new LoggerWrapper($loggerMock);
        $loggerServiceWrapper->log($levelToLog, $messageToLog, $contextToLog);
    }

    private function getLoggerMock(): LoggerInterface
    {
        return $this
            ->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'emergency',
                    'alert',
                    'critical',
                    'error',
                    'warning',
                    'notice',
                    'info',
                    'debug',
                    'log'
                ]
            )
            ->getMock();
    }
}
