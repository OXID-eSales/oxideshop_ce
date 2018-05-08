<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Logger\ServiceFactory;

use OxidEsales\EshopCommunity\Internal\Logger\DataObject\MonologConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Logger\ServiceFactory\MonologLoggerServiceFactory;
use OxidEsales\EshopCommunity\Internal\Logger\Validator\LoggerConfigurationValidatorInterface;
use Psr\Log\LoggerInterface;

class MonologFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|MonologConfigurationInterface $configurationMock */
        $configurationMock = $this->getMock(MonologConfigurationInterface::class);
        $configurationMock->expects($this->any())->method('getLogFilePath')->willReturn('string');

        /** @var \PHPUnit_Framework_MockObject_MockObject|LoggerConfigurationValidatorInterface $configurationValidatorMock */
        $configurationValidatorMock =  $this->getMock(LoggerConfigurationValidatorInterface::class);
        $configurationValidatorMock->expects($this->any())->method('validate');

        $loggerFactory = new MonologLoggerServiceFactory($configurationMock, $configurationValidatorMock);


        $this->assertInstanceOf(
            LoggerInterface::class,
            $loggerFactory->create()
        );
    }
}
