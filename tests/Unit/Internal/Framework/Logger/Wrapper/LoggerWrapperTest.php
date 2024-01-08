<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Logger\Wrapper;

use OxidEsales\EshopCommunity\Internal\Framework\Logger\Wrapper\LoggerWrapper;
use Psr\Log\LoggerInterface;

/**
 * Class LoggerWrapperTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Logger\Wrapper
 */
class LoggerWrapperTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @dataProvider dataProviderPsrInterfaceMethods
     *
     * @param string $methodName The name of the method to test
     */
    public function testAllInterfaceMethodsExceptLogAreHandled($methodName)
    {
        $messageToLog = "The message is {myMessage}";
        $contextToLog = ['myMessage' => 'Hello World!'];
        $loggerMock = $this->getLoggerMock();
        $loggerMock->expects($this->once())
            ->method($methodName)
            ->with(
                $this->equalTo($messageToLog),
                $this->equalTo($contextToLog)
            );

        $loggerServiceWrapper = new LoggerWrapper($loggerMock);
        $loggerServiceWrapper->$methodName($messageToLog, $contextToLog);
    }

    /**
     * @return array
     */
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

    public function testLog()
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

    /**
     * @return LoggerInterface
     */
    private function getLoggerMock()
    {
        $loggerMock = $this
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

        return $loggerMock;
    }
}
