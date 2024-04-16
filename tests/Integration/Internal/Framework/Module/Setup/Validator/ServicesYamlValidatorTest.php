<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Setup\Validator;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDao;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\InvalidModuleServicesException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator\ModuleConfigurationValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator\ServicesYamlValidator;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class ServicesYamlValidatorTest extends TestCase
{
    private ModuleConfigurationValidatorInterface $validator;
    private ModuleConfiguration $moduleConfiguration;
    private ModulePathResolverInterface|MockObject $modulePathResolver;
    private string $testModuleId = 'testModuleId';

    public function setUp(): void
    {
        parent::setUp();

        $context = new BasicContextStub();
        $this->modulePathResolver = $this->getMockBuilder(ModulePathResolverInterface::class)->getMock();
        $this->moduleConfiguration = new ModuleConfiguration();
        $this->validator = new ServicesYamlValidator(
            $context,
            new ProjectYamlDao(
                $context,
                new Filesystem()
            ),
            $this->modulePathResolver
        );
    }

    #[DoesNotPerformAssertions]
    public function testValidateNoServicesYaml(): void
    {
        $this->moduleConfiguration->setModuleSource('.');
        $this->moduleConfiguration->setId($this->testModuleId);
        $this->modulePathResolver->method('getFullModulePathFromConfiguration')
            ->willReturn(Path::join(__DIR__, 'Fixtures', 'ModuleWithNoServices'));

        $this->validator->validate($this->moduleConfiguration, 1);
    }

    #[DoesNotPerformAssertions]
    public function testWithCorrectServiceYaml(): void
    {
        $this->moduleConfiguration->setModuleSource('Working');
        $this->moduleConfiguration->setId("testId");
        $this->modulePathResolver->method('getFullModulePathFromConfiguration')
            ->willReturn(Path::join(__DIR__, 'Fixtures', 'ModuleWithCorrectServiceYaml'));

        $this->validator->validate($this->moduleConfiguration, 1);
    }

    public function testWithWrongServiceYaml(): void
    {
        $this->moduleConfiguration->setModuleSource('NotWorking');
        $this->moduleConfiguration->setId($this->testModuleId);
        $this->modulePathResolver
            ->method('getFullModulePathFromConfiguration')
            ->willReturn(Path::join(__DIR__, 'Fixtures', 'ModuleWithWrongServiceYaml'));

        $this->expectException(InvalidModuleServicesException::class);
        $this->validator->validate($this->moduleConfiguration, 1);
    }
}
