<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Logger\ServiceWrapper;

use OxidEsales\EshopCommunity\Internal\Logger\ServiceWrapper\LoggerServiceWrapper;

/**
 * Class LoggerServiceWrapperTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Unit\Internal\Logger\ServiceWrapper
 */
class LoggerServiceWrapperTest extends \PHPUnit_Framework_TestCase
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

        $loggerServiceWrapper = new LoggerServiceWrapper($loggerMock);
        $loggerServiceWrapper->$methodName($messageToLog, $contextToLog);
    }

    /**
     * @return array
     */
    public function dataProviderPsrInterfaceMethods()
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

        $loggerServiceWrapper = new LoggerServiceWrapper($loggerMock);
        $loggerServiceWrapper->log($levelToLog, $messageToLog, $contextToLog);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface
     */
    private function getLoggerMock()
    {
        $loggerMock = $this
            ->getMockBuilder(\Psr\Log\LoggerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(
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
