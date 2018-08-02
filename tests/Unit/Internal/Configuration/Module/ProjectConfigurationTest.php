<?php
declare(strict_types = 1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Configuration\Module\DataObject;

use OxidEsales\EshopCommunity\Internal\Common\Exception\InvalidObjectIdException;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\EnvironmentConfiguration;
use PHPUnit\Framework\TestCase;

class ProjectConfigurationTest extends TestCase
{
    private $projectConfiguration;

    protected function setUp()
    {
        parent::setUp();
        $this->projectConfiguration = new ProjectConfiguration();
    }

    public function testGetNamesOfEnvironmentConfigurations()
    {
        $environmentConfiguration = new EnvironmentConfiguration();
        $this->projectConfiguration->setEnvironmentConfiguration('Testing', $environmentConfiguration);
        $this->projectConfiguration->setEnvironmentConfiguration('Production', $environmentConfiguration);

        $this->assertEquals(['Testing', 'Production'], $this->projectConfiguration->getNamesOfEnvironmentConfigurations());
    }

    public function testDeleteEnvironment()
    {
        $environmentConfiguration = new EnvironmentConfiguration();
        $this->projectConfiguration->setEnvironmentConfiguration('Testing', $environmentConfiguration);
        $this->projectConfiguration->setEnvironmentConfiguration('Production', $environmentConfiguration);
        $this->projectConfiguration->deleteEnvironmentConfiguration('Testing');

        $this->assertEquals(['Production'], $this->projectConfiguration->getNamesOfEnvironmentConfigurations());
    }

    public function testDeleteEnvironmentThrowsExceptionIfEnvironmentDoesNotExist()
    {
        $this->expectException(InvalidObjectIdException::class);
        $this->projectConfiguration->deleteEnvironmentConfiguration('Testing');
    }

    public function testGetEnvironmentConfiguration()
    {
        $environmentConfiguration = new EnvironmentConfiguration();
        $this->projectConfiguration->setEnvironmentConfiguration('Testing', $environmentConfiguration);
        $this->assertSame($environmentConfiguration, $this->projectConfiguration->getEnvironmentConfiguration('Testing'));
    }

    public function testGetEnvironmentConfigurationThrowsExceptionIfEnvironmentDoesNotExist()
    {
        $this->expectException(InvalidObjectIdException::class);
        $this->projectConfiguration->getEnvironmentConfiguration('Testing');
    }
}