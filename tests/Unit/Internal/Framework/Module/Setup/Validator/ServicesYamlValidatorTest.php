<?php

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDao;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolver;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\InvalidModuleServicesException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator\ModuleConfigurationValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator\ServicesYamlValidator;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

class ServicesYamlValidatorTest extends TestCase
{
    /** @var ModuleConfigurationValidatorInterface */
    private $validator;

    /** @var ModuleConfiguration */
    private $moduleConfiguration;

    /** @var ModulePathResolver */
    private $modulePathResolver;

    private $testModuleId = 'testModuleId';

    /**
     * @var ContextStub
     */
    private $context;

    public function setup(): void
    {
        parent::setUp();

        $this->context = new BasicContextStub();
        $this->modulePathResolver = $this->getMockBuilder(ModulePathResolverInterface::class)->getMock();
        $this->moduleConfiguration = new ModuleConfiguration();
        $this->validator = new ServicesYamlValidator(
            $this->context,
            new ProjectYamlDao($this->context, new Filesystem()),
            $this->modulePathResolver
        );
    }

    public function testValidateNoServicesYaml(): void
    {
        $this->moduleConfiguration->setPath('.');
        $this->moduleConfiguration->setId($this->testModuleId);
        $this->modulePathResolver->method('getFullModulePathFromConfiguration')
            ->willReturn(Path::join(__DIR__, 'Fixtures', 'ModuleWithNoServices'));

        $this->validator->validate($this->moduleConfiguration, 1);
    }

    public function testWithCorrectServiceYaml(): void
    {
        $this->moduleConfiguration->setPath('Working');
        $this->moduleConfiguration->setId("testId");
        $this->modulePathResolver->method('getFullModulePathFromConfiguration')
            ->willReturn(Path::join(__DIR__, 'Fixtures', 'ModuleWithCorrectServiceYaml'));

        $this->validator->validate($this->moduleConfiguration, 1);
    }

    public function testWithWrongServiceYaml(): void
    {
        $this->moduleConfiguration->setPath('NotWorking');
        $this->moduleConfiguration->setModuleSource('NotWorking');
        $this->moduleConfiguration->setId($this->testModuleId);
        $this->modulePathResolver
            ->method('getFullModulePathFromConfiguration')
            ->willReturn(Path::join(__DIR__, 'Fixtures', 'ModuleWithWrongServiceYaml'));

        $this->expectException(InvalidModuleServicesException::class);
        $this->validator->validate($this->moduleConfiguration, 1);
    }
}
