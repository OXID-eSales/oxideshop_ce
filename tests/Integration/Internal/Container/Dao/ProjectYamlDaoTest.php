<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Container\Dao;

use OxidEsales\EshopCommunity\Internal\Container\BootstrapContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDao;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

class ProjectYamlDaoTest extends TestCase
{
    use ContainerTrait;

    /**
     * @var ProjectYamlDaoInterface $dao
     */
    private $dao;

    public function setUp()
    {
        $contextStub = $this->getMockBuilder(BasicContext::class)
            ->disableOriginalConstructor()
            ->setMethods(['getGeneratedServicesFilePath'])->getMock();
        $contextStub
            ->method('getGeneratedServicesFilePath')
            ->willReturn($this->getTestGeneratedServicesFilePath());

        $this->dao = new ProjectYamlDao(
            $contextStub,
            $this->get('oxid_esales.symfony.file_system')
        );
    }

    protected function tearDown()
    {
        parent::tearDown();
        $projectFilePath = $this->getTestGeneratedServicesFilePath();
        if (file_exists($projectFilePath)) {
            unlink($projectFilePath);
        }
    }

    public function testLoading()
    {
        $testData = <<<EOT
imports:
  -
    resource: /some/non/existing/path/services.yaml

EOT;
        file_put_contents(
            $this->getTestGeneratedServicesFilePath(),
            $testData
        );

        $projectYaml = $this->dao->loadProjectConfigFile();
        $this->assertArrayHasKey('imports', $projectYaml->getConfigAsArray());
    }

    public function testLoadingEmptyFile()
    {
        file_put_contents(
            $this->getTestGeneratedServicesFilePath(),
            ''
        );

        $projectYaml = $this->dao->loadProjectConfigFile();
        $this->assertCount(0, $projectYaml->getConfigAsArray());
    }

    public function testLoadingNonExistingFile()
    {
        $projectYaml = $this->dao->loadProjectConfigFile();
        $this->assertCount(0, $projectYaml->getConfigAsArray());
    }

    public function testWriting()
    {
        $projectYaml = new DIConfigWrapper(
            ['imports'  => [['resource' => 'some/path']],
                'services' => ['somekey' => ['factory' => ['some/factory', 'someMethod']]]]
        );
        $this->dao->saveProjectConfigFile($projectYaml);

        $projectYaml = $this->dao->loadProjectConfigFile();
        $this->assertCount(2, $projectYaml->getConfigAsArray());
        $this->assertEquals('some/path', $projectYaml->getConfigAsArray()['imports'][0]['resource']);
    }

    public function testClearingCacheOnWriting()
    {
        $bootstrapContainer = (new BootstrapContainerBuilder())->create();
        $bootstrapContainer->getDefinition(ProjectYamlDaoInterface::class)->setPublic(true);
        $bootstrapContainer->compile();

        $dao = $bootstrapContainer->get(ProjectYamlDaoInterface::class);
        $context = $bootstrapContainer->get(BasicContextInterface::class);

        $projectYaml = new DIConfigWrapper([]);

        // Make sure that the cache file exists
        ContainerFactory::resetContainer();
        ContainerFactory::getInstance()->getContainer();
        $this->assertFileExists($context->getContainerCacheFilePath());

        // This should trigger the event that deletes the cachefile
        $dao->saveProjectConfigFile($projectYaml);

        $this->assertFileNotExists($context->getContainerCacheFilePath());

        ContainerFactory::getInstance()->getContainer();
        // Verify container has been rebuild be checking that a cachefile exists
        $this->assertFileExists($context->getContainerCacheFilePath());
    }

    private function getTestGeneratedServicesFilePath(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'generated_project.yaml';
    }
}
