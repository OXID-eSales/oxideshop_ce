<?php

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDao;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolver;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator\ModuleConfigurationValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator\ServicesYamlValidator;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use PHPUnit\Framework\MockObject\MockObject;
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

    /** @var ModuleConfigurationDaoInterface | MockObject */
    private $moduleConfigurationDao;

    public function setup(): void
    {
        parent::setUp();

        $context = new BasicContextStub();
        $context->setModulesPath(Path::join(__DIR__, 'Fixtures'));
        $this->moduleConfigurationDao = $this->getMockBuilder(ModuleConfigurationDaoInterface::class)->getMock();
        $this->modulePathResolver = new ModulePathResolver($this->moduleConfigurationDao, $context);
        $this->moduleConfiguration = new ModuleConfiguration();
        $this->validator = new ServicesYamlValidator($context, new ProjectYamlDao($context, new Filesystem()));
    }

    /**
     * @dataProvider data
     * @param $directory
     * @param $throwsException
     */
    public function testValidateNoServicesYaml($directory, $throwsException)
    {

        $this->moduleConfiguration->setPath($directory);

        $exceptionThrown = false;

        try {
                $this->validator->validate($this->moduleConfiguration, 1);
        }
        catch (\Exception $e)
        {
            $exceptionThrown = true;
        }

        $this->assertEquals(
            $throwsException,
            $exceptionThrown,
            $throwsException ? 'Expected exception missing' : 'Unexpected exception'
        );
    }

    public function data() {

        return [
            ['.', false],
            ['Working', false],
            ['NotWorking', true]
        ];

    }
}
