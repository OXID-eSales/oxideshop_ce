<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDao;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator\ModuleConfigurationValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator\ServicesYamlValidator;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

class ServicesYamlValidatorTest extends TestCase
{
    private ModuleConfigurationValidatorInterface $validator;

    private ModuleConfiguration $moduleConfiguration;

    public function setup(): void
    {
        parent::setUp();

        $context = new BasicContextStub();
        $context->setModulesPath(
            Path::join(
                __DIR__,
                'Fixtures'
            )
        );
        $this->moduleConfiguration = new ModuleConfiguration();
        $this->validator = new ServicesYamlValidator(
            $context,
            new ProjectYamlDao(
                $context,
                new Filesystem()
            )
        );
    }

    /**
     * @dataProvider data
     * @param $directory
     * @param $throwsException
     */
    public function testValidateNoServicesYaml($directory, $throwsException): void
    {
        $this->moduleConfiguration->setPath($directory);
        $exceptionThrown = false;

        try {
            $this->validator->validate(
                $this->moduleConfiguration,
                1
            );
        } catch (\Exception $e) {
            $exceptionThrown = true;
        }

        $this->assertEquals(
            $throwsException,
            $exceptionThrown,
            $throwsException ? 'Expected exception missing' : 'Unexpected exception'
        );
    }

    public function data(): array
    {
        return [
            ['.', false],
            ['Working', false],
            ['NotWorking', true],
        ];
    }
}
